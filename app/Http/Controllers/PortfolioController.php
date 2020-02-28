<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\PortfolioScrip;
use App\Portfolio;

class PortfolioController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function index(Request $request)
    {
        if($request->has('withScrips')){
             $portfolios = $request->user()->portfolios()->with('scrips')->get();
        }else{
             $portfolios = $request->user()->portfolios;
        }
       

        return $portfolios;
    }

    public function show(Request $request, $id)
    {
        return $request->user()->portfolios()->with(['scrips' => function ($query)
        {
            return $query->where('share_status', 'buy');
        }])->find($id);
    }

    public function delete(Request $request, $id)
    {
        $portfolio = $request->user()->portfolios()->find($id);
        $portfolio->scrips()->delete();
        $portfolio->delete();
        return  $request->user()->portfolios;
    }

    public function create(Request $request)
    {
        $newScrips = [];
        $this->validate($request, [
            'portfolio_name' => 'required',
            'cash_amount' => 'required|numeric',
            'broker_fee' => 'required|numeric',
        ]);    

        $portfolio = new Portfolio([
            'portfolio_name'=> $request->get('portfolio_name'),
            'cash_amount'=> $request->get('cash_amount'),
            'broker_fee'=> $request->get('broker_fee'),
        ]);

        $portfolio = $request->user()->portfolios()->save($portfolio);

        $row = 0;
        $actions = $request->get('actions');
        $scrip_ids = $request->get('scrip_id');

        $commissions = $request->get('commission');
        $no_of_shares = $request->get('no_of_shares');
        $buying_price = $request->get('buying_price');
        $buying_date = $request->get('buying_date');

        $instrument_ids = $request->get('instrument_ids');


        $cost = 0;
        $newRow = 0;
        $formRow = 0;
        foreach($instrument_ids as $instrument_id){

            if($instrument_ids[$newRow] == ""){
                $newRow++;
                continue;
            }

            if($buying_price[$formRow] =="" || $commissions[$formRow] == "" || $no_of_shares[$formRow] == "" ){
                abort(403, "Invalid request. Please double check your changes.");
            }

            if($buying_date[$formRow] == ""){
                $buying_date[$formRow] = date('Y-m-d');
            }

            $newScrips[] = new PortfolioScrip([
                'instrument_id' => $instrument_ids[$newRow],
                'buying_price' => $buying_price[$formRow],
                'commission' => $commissions[$formRow],
                'no_of_shares' => $no_of_shares[$formRow],
                'buying_date' => $buying_date[$formRow],
                'share_status' => 'buy',
                 ]);

                $sellValue = $no_of_shares[$formRow] * $buying_price[$formRow];
                $cost+= $sellValue + ($commissions[$formRow]/100)* $sellValue;
            $formRow++;
            $newRow++;
        }
        $portfolio->scrips()->saveMany($newScrips);


    }

    public function update(Request $request, $id)
    {
        $newScrips = [];
        $this->validate($request, [
            'portfolio_name' => 'required',
            'cash_amount' => 'required|numeric',
            'broker_fee' => 'required|numeric',
        ]);        

     

        $portfolio = $request->user()->portfolios()->with(['scrips' => function ($query)
                    {
                        return $query->where('share_status', 'buy');
                    }])->find($id);



        //set portfolio values
        $portfolio->portfolio_name = $request->get('portfolio_name');
        $portfolio->cash_amount = $request->get('cash_amount');
        $portfolio->broker_fee = $request->get('broker_fee');

        $row = 0;
        $actions = $request->get('actions');
        $scrip_ids = $request->get('scrip_id');

        $commissions = $request->get('commission');
        $no_of_shares = $request->get('no_of_shares');
        $buying_price = $request->get('buying_price');
        $buying_date = $request->get('buying_date');

        $instrument_ids = $request->get('instrument_ids');

        //handle existing scrips action
        $formRow = 0;
        foreach ($actions?:[] as $action) {
            //get the scrip row from portfolio_scrips 
            $scrip = $portfolio->scrips()->find($scrip_ids[$row]);

            if($action  == "Delete"){
                //delete the script without any calculation      
                $scrip->delete();
            }else if($action  == "Edit"){
                // update wihout any calculation
                $scrip->buying_price = $buying_price[$formRow];
                $scrip->commission = $commissions[$formRow];
                $scrip->buying_date = $buying_date[$formRow];
                $scrip->no_of_shares = $no_of_shares[$formRow];
                $scrip->save();

                $formRow++;
            }else if($action == "Sell"){
                // update both scrip and portfolio table
                $scrip->sell_price = $buying_price[$formRow];
                $scrip->commission = $commissions[$formRow];
                $scrip->sell_date = date('Y-m-d') ;
                
                if($scrip->no_of_shares == $no_of_shares[$formRow]){
                    $scrip->share_status = "sell";
                    $scrip->sell_price = $buying_price[$formRow];
                    $scrip->sell_date = date('Y-m-d');
                }else if($scrip->no_of_shares > $no_of_shares[$formRow]){
                    $scrip->no_of_shares = $scrip->no_of_shares - $no_of_shares[$formRow];

                    $newScrips[] = new PortfolioScrip([
                        'instrument_id' => $scrip->instrument_id,
                        'buying_price' => $scrip->buying_price,
                        'sell_price' => $buying_price[$formRow],
                        'commission' => $commissions[$formRow],
                        'no_of_shares' => $no_of_shares[$formRow],
                        'buying_date' => $scrip->buying_date,
                        'sell_date' => date('Y-m-d'),
                        'share_status' => 'sell',
                         ]);

                }else{
                    abort(401, "Sell quantity can not me more than holding quantity");
                }
                $sellValue = $no_of_shares[$formRow] * $buying_price[$formRow];
                $portfolio->cash_amount+= $sellValue - ($commissions[$formRow]/100)* $sellValue;
                $scrip->save();                

                $formRow++;
            }
            $row++;
        }



        $cost = 0;
        $newRow = 0;
        foreach($instrument_ids as $instrument_id){

            if($instrument_ids[$newRow] == ""){
                $newRow++;
                continue;
            }

            if($buying_price[$formRow] =="" || $commissions[$formRow] == "" || $no_of_shares[$formRow] == "" ){
                abort(403, "Invalid request. Please double check your changes.");
            }

            if($buying_date[$formRow] == ""){
                $buying_date[$formRow] = date('Y-m-d');
            }

            $newScrips[] = new PortfolioScrip([
                'instrument_id' => $instrument_ids[$newRow],
                'buying_price' => $buying_price[$formRow],
                'commission' => $commissions[$formRow],
                'no_of_shares' => $no_of_shares[$formRow],
                'buying_date' => $buying_date[$formRow],
                'share_status' => 'buy',
                 ]);

                $sellValue = $no_of_shares[$formRow] * $buying_price[$formRow];
                $cost+= $sellValue + ($commissions[$formRow]/100)* $sellValue;
            $formRow++;
            $newRow++;
        }
        $portfolio->scrips()->saveMany($newScrips);
        $portfolio->cash_amount -= $cost;
        $portfolio->save();
        $portfolio = $request->user()->portfolios()->with(['scrips' => function ($query)
                    {
                        return $query->where('share_status', 'buy');
                    }])->find($id);


        return $portfolio;
    }

    //
}

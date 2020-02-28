<?php

namespace App\Http\Controllers;
use App\Instrument;
use App\Market;
use Illuminate\Http\Request;
class InstrumentController extends Controller
{
    public function index($code)
    {
        $code = strtoupper($code);
        return $this->instruments()->where('instrument_code', $code)->orderBy("data_banks_intradays.id", "desc")->first();
    }

    public function all()
    {
        return $this->instruments()->orderBy('code', 'asc')->get()->keyBy('code');
    }

    public function taChart(Request $request)
    {
        if($request->has('TickerSymbol'))
        {
            $chart = new \App\Classes\Chart();
            return response()->make($chart->html());        
        }
        return abort(404);

    }

    public function news(Request $request, $code)
    {
        $instrument = Instrument::find($code);
        return $instrument->news()->orderBy('news.id', 'desc')->paginate(10);
    }


    public function intraday(Request $request, $code)
    {
        $data = [];
        $code = strtoupper($code);
        $instrument = Instrument::find($code);
        $in = $instrument->intraday();
        //add params from query string $_GET
        if($request->has("date")){
            $in->where("trade_date", $request->get('date'));
        }
        // dd($in);
        $in->chunk(10, function ($intraday) use (&$data)
        {
            foreach ($intraday as $row) {
                $data[0][] = $row->close_price;
                $data[1][] = $row->new_volume;
                $data[2][] = strtotime($row->lm_date_time);
            }
            if(count($data[0])> 1000){
                return false;
            }
        });
        return $data;
    }


    public function history(Request $request, $code)
    {
        // avilable resulations: D, 1, 5
        $data = [];
        $code = strtoupper($code);
        $instrument = Instrument::find($code);
        return $instrument->getHistoryByRequest($request);
    }

    public function instruments()
    {
        $instruments = Instrument::getAll();
        return $instruments;
    }

    public function corporateActions($symbol)
    {
        $instrument = Instrument::find($symbol);
        return $instrument->corporateActions;
    }

    public function highLow(Request $request, $code)
    {
        $range = $request->range;
        if(empty($range)){
            $range = 365;
        }
        $date = date("Y-m-d", strtotime($range." days ago"));
        $instrument = Instrument::find($code);
        $result = $instrument->eod()->where('date', '>=', $date)->selectRaw('max(high) as high, min(low) as low')->first();
        return $result;
        
    }

    public function se()
    {
       // return $data = Instrument::where('active', 1)->with('eod')->where('date', date('Y-m-d'))->get();
        $data = collect(\DB::select("select * from instruments left join data_banks_eods on data_banks_eods.instrument_id = instruments.id  where active = 1 and data_banks_eods.date = '".date('Y-m-d')."' and sector_list_id not in (23, 24) "));
       
        $sorted = $data->sortBy(function ($symbol, $key) {
            return  ($symbol->tradevalues * 1000)/$symbol->trade;
        });   
        return $sorted->values()->all();     
        return ('ss');
 
    }
}

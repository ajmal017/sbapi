<?php

namespace App\Http\Controllers;
use App\Instrument;
use Illuminate\Http\Request;
class InstrumentController extends Controller
{
    public function index($code)
    {
        $code = strtoupper($code);
        return Instrument::find($code);
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
            if(count($data)> 10){
                return false;
            }
        });
        return $data;
    }
}

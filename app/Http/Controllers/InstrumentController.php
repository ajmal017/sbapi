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
        return $this->instruments()->get()->keyBy('code');
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
        // avilable resulations: D, 1M, 5M
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
}

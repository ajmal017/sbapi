<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;

/**
 * 
 */
class Instrument extends Model
{
	protected $primaryKey = "instrument_code";
	public $incrementing = false;


    public function news()
    {
        return $this->hasMany(News::class, 'instrument_id', 'id');
    }

	public function corporateActions()
	{
		return $this->hasMany(\App\CorporateAction::class, 'instrument_id', 'id');
	}

    public function intraday()
    {
        return $this->hasMany(\App\Intraday::class, 'instrument_id', 'id');
    }

    public function eod()
    {
        return $this->hasMany(\App\EOD::class, 'instrument_id', 'id');
    }

	public static function getAll()
	{
        $instruments = self::select("instrument_code as code", "data_banks_intradays.market_id", "sector_list_id as sector_id", "name", "quote_bases as category", "open_price as open", "high_price as high", "low_price as low", "close_price as close", "yday_close_price as yday_close", "total_trades as trades", "total_volume as volume", "total_value as value", "lm_date_time as updated_at", "instruments.id as id" )->where('active', 1)->leftJoin('data_banks_intradays', function ($join)
        {
            $join->on("data_banks_intradays.instrument_id", "instruments.id");
            if(!app("request")->has("date") && !app('request')->has('before')){
                  $join->on("data_banks_intradays.batch", "instruments.batch_id");
            }
        })->whereNotNull('instruments.batch_id');     
            
        if(app("request")->has("date")){
            $instruments->where("data_banks_intradays.batch", "=", Market::getMarketByDate(app("request")->get('date'))->batch);
        }else if(app("request")->has("before")){
             $instruments->where("data_banks_intradays.batch", "=", Market::getMarketBeforeDate(app("request")->get('before'))->batch);
        }
        return $instruments;		
	}

    public function floorToMinute($timestring, $resolution) {

        $minutes = date('i', strtotime($timestring));


        $minutes = $minutes - ($minutes % $resolution);
        $minutes = (string) $minutes;

        $timestring[17] = "0";
        $timestring[18] = "0";
        try {
            $timestring[14] = $minutes[0];
            if(isset($minutes[1])){
                  $timestring[15] = $minutes[1];
              }else{
                $timestring[15] = "0";
              }
          
        } catch (\Exception $e) {
        }
        return $timestring;
    }

    public function getHistoryByRequest($request)
    {
        $data = [];
        // avilable resolutions: D, 1, 5, 15, 30, 1H, 2H, 3H, 1W, 1M

        //set resolution
         $resolution = "D";
        if($request->has('resolution')){
            $resolution = $request->get('resolution');
        }
        $this->request = $request;
        $applyFloor = false;
        switch ($resolution) {
            case 'D':
            $in = $this->eod();
            $in->orderBy('date', 'desc')->limit(200);
            // return $this->getFiveMinutesHistory()
            break;
            case '1':
            $in = $this->intraday()->limit(2000);
            $in->orderBy('id', 'desc')->selectRaw("DISTINCT(trade_time), data_banks_intradays.*")
            // ->where('new_volume', '!=', 0)
            ;
            break;
            default:
            $applyFloor = true;
            $in = $this->intraday()->limit(2000);
            $in->orderBy('id', 'desc')->selectRaw("DISTINCT(total_volume), data_banks_intradays.*");

                break;
        }

        // dd($data);
        // $in = $instrument->intraday();
        // $in = $this->eod();

        //resolution -> 5 15 30 D W M 
        //add params from query string $_GET
        if($request->has("date")){
            $in->where("trade_date", $request->get('date'));
        }
        
        // dd($in);
        $floorData = [];
        $sortedDates = [];

        $intraday = $in->get();

        // dd(json_decode(Redis::get('EOD.Adjusted.KPCL')));
        // dd($intraday[0]);
        $applyFloor = false;
        $intraday = json_decode(Redis::get('EOD.Adjusted.'.$this->instrument_code));
       //  dump(date("Y-m-d H:i:s", 1581444000));
       // dump(date("y-m-d H:i:s"));
       //  dd($intraday[0]);
            // intraday table rows
            // foreach ($intraday as $row) {
            //     $data[0][] = $row->open_price;
            //     $data[1][] = $row->high_price;
            //     $data[2][] = $row->low_price;
            //     $data[3][] = $row->close_price;
            //     $data[4][] = $row->new_volume;
            //     $data[5][] = strtotime($row->lm_date_time);
            // }

            //eod table rows
            // $intraday->reverse();

            // dd($this->id);
            if($applyFloor){
                //have to floor in resolution
                foreach ($intraday as $row) {

                         $date = $this->floorToMinute($row->date, $resolution);
                    if(isset($floorData[$date])){
                        //update array data
                        if($floorData[$date]->low > $row->close){
                             $floorData[$date]->low = $row->close;
                         }
                        if($floorData[$date]->high < $row->close){
                             $floorData[$date]->high = $row->close;
                         }
                         $floorData[$date]->volume = $floorData[$date]->volume + $row->volume;
                         // dump($date);
                         // dump($floorData[$date]);
                         // dump("//");

                        
                        if($row->volume < 1){
                            // $floorData[$date]->open = $row->close;
                            // dump($row->volume);
                        }else{
                            $floorData[$date]->open = $row->close;
                        }
                
                        
                    }else{
                        // insert array data
                        $row->high = $row->close;
                        $row->low = $row->close;
                        $row->open = $row->close;
                        $floorData[$date] = $row;
                        $sortedDates[] = $date;
                    }

                }
                foreach ($sortedDates as $key => $value) {
                    $row = $floorData[$value];
                    $data[0][] = $row->open;
                    $data[1][] = $row->high;
                    $data[2][] = $row->low;
                    $data[3][] = $row->close;
                    $data[4][] = $row->volume;
                    $data[5][] = strtotime($value);
                }
            }else{
                foreach ($intraday as $row) {
                    $data[0][] = $row->open;
                    $data[1][] = $row->high;
                    $data[2][] = $row->low;
                    $data[3][] = $row->close;
                    $data[4][] = $row->volume;
                    $data[5][] = $row->date_timestamp;
                    // $data[5][] = strtotime($row->date);
                }
            }


        $data[0] = array_reverse($data[0]);
        $data[1] = array_reverse($data[1]);
        $data[2] = array_reverse($data[2]);
        $data[3] = array_reverse($data[3]);
        $data[4] = array_reverse($data[4]);
        $data[5] = array_reverse($data[5]);
        return $data;
    }














    // old codes
        public static function getInstrumentsAll($exchangeId=0)
    {

        /*We will use session value of active_exchange_id as default if exist*/
        if(!$exchangeId) {
            $exchangeId = 1;
        }

            $returnData=static::where('exchange_id',$exchangeId)->where('active',"1")->orderBy('instrument_code', 'asc')->get();

        return $returnData;
    }
}
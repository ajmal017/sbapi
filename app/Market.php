<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 */
class Market extends Model
{
	public static function getMarketByDate($date)
	{
		return self::where("trade_date", $date)->where("data_bank_intraday_batch", "!=", 0)->firstOrFail();
	}

	public static function getMarketBeforeDate($date)
	{
		return self::where("trade_date", "<", $date)->where("data_bank_intraday_batch", "!=", 0)->orderBy("trade_date", "desc")->firstOrFail();
	}

	public function getBatchAttribute()
    {
        return $this->data_bank_intraday_batch;
    }


}
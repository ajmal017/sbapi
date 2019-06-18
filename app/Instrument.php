<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 */
class Instrument extends Model
{
	protected $primaryKey = "instrument_code";

	public function intraday()
	{
		return $this->hasMany(\App\Intraday::class, 'instrument_id', 'id');
	}
}
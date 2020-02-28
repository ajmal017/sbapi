<?php

namespace App;
use App\SectorIntraday;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 */
class Sector extends Model
{
	protected $table = "sector_lists";

	public function intradays()
	{
		return $this->hasMany(SectorIntraday::class, 'sector_list_id');
	}

}
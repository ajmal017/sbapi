<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 */
class Intraday extends Model
{
	protected $table = "data_banks_intradays";

	public function getOpenAttribute(){
		return $this->open_price;
	}
	public function getCloseAttribute(){
		return $this->close_price;
	}
	public function getLowAttribute(){
		return $this->low_price;
	}
	public function getHighAttribute(){
		return $this->high_price;
	}
	public function getVolumeAttribute(){
		return $this->new_volume;
	}
	public function getDateAttribute(){
		return $this->lm_date_time;
	}

	public function setOpenAttribute($value){
		 $this->open_price = $value;
	}
	public function setCloseAttribute($value){
		 $this->close_price = $value;
	}
	public function setLowAttribute($value){
		 $this->low_price = $value;
	}
	public function setHighAttribute($value){
		 $this->high_price = $value;
	}
	public function setVolumeAttribute($value){
		 $this->new_volume = $value;
	}
	public function setDateAttribute($value){
		 $this->lm_date_time = $value;
	}
}
<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use App\PortfolioScrip;

/**
 * 
 */
class Portfolio extends Model
{
    protected $fillable = [
        'user_id', 'portfolio_value' , 'cash_amount' ,
                'portfolio_name' ,
                'broker_fee',
                'broker',
                'email_alert',
    ];    
	public function scrips()
	{
		return $this->hasMany(PortfolioScrip::class)->leftJoin('instruments', 'instruments.id', 'portfolio_scrips.instrument_id')->select('portfolio_scrips.*', 'instruments.instrument_code as code');
	}
}
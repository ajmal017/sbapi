<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 */
class PortfolioScrip extends Model
{
    protected $fillable = [
        'buying_price', 'instrument_id' , 'buying_price' ,
                'commission' ,
                'no_of_shares',
                'buying_date',
                'share_status',
    ];    
}
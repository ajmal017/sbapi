<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WatchlistItem extends Model
{
    protected $fillable = ['instrument_id', 'watchlist_id'];

}

<?php

namespace App;
use App\WatchlistItem;
use Illuminate\Database\Eloquent\Model;

class Watchlist extends Model
{
    protected $fillable = ['name', 'user_id'];

    public function items()
    {
        return $this->hasMany(WatchlistItem::class)->select('watchlist_items.*', 'instruments.instrument_code as code')->leftJoin("instruments", 'instruments.id', 'instrument_id');
    }

}

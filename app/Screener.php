<?php

namespace App;
use App\WatchlistItem;
use Illuminate\Database\Eloquent\Model;

class Screener extends Model
{
    protected $appends = ['symbols'];
    protected $fillable = ['title', 'slug', 'description', 'user_id', 'query', 'featured'];

    public function getSymbolsAttribute()
    {
        
        return [1, 3, 4];
    }
}

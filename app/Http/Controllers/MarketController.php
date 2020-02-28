<?php

namespace App\Http\Controllers;
use App\Market;

class MarketController extends Controller
{
    public function index()
    {
        $pastActiveMarkets = Market::where('data_bank_intraday_batch', ">" , 0)->orderBy('trade_date', 'desc')->with('trades')->take(2)->get();
        return $pastActiveMarkets;
    }
}

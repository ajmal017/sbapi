<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Watchlist;
use Laravel\Lumen\Routing\Controller as BaseController;

class WatchlistController extends Controller
{
    public function index(Request $request)
    {
        $Watchlist = $request->user()->watchlist()->with('items')->get();
        return $Watchlist;
    }
}

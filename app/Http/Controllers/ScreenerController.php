<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Screener;
use Laravel\Lumen\Routing\Controller as BaseController;

class ScreenerController extends Controller
{
    public function index(Request $request)
    {
        $sbscreeners = Screener::where('featured', 1)->get();
        return $sbscreeners;
    }
}

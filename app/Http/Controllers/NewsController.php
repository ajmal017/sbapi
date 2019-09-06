<?php
namespace App\Http\Controllers;
use App\News;
use Illuminate\Http\Request;

use Laravel\Lumen\Routing\Controller as BaseController;

class NewsController extends Controller
{
	public function index(Request $request)
	{
		if($request->has('from')){
			return News::latest()->where('post_date', ">=", $request->from)->get();
		}
		return News::latest()->where('post_date', ">=", \Carbon\Carbon::parse("1 month ago")->format('Y-m-d'))->paginate(5);
	}
}

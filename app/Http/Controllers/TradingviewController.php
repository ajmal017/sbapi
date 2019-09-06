<?php
namespace App\Http\Controllers;
use App\ChartLayout;
use Illuminate\Http\Request;

use Laravel\Lumen\Routing\Controller as BaseController;

class TradingviewController extends Controller
{
	public function index(Request $request)
	{
		 $data =  ChartLayout::select("id", "name", "resolution", "symbol", 'updated_at')->where('user_id', "=", (string) $request->user()->id)->orderBy('updated_at', 'desc')->get();
		 return $data;
	}

	public function show($id)
	{
		$layout =  ChartLayout::find($id);
		$layout->updated_at = \Carbon\Carbon::now();
		$layout->save();
		return $layout;
	}

	public function store(Request $request)
	{
		if(json_decode($request->content) == null ){
             abort(402, "Invalid data");
        }
		$layout = ChartLayout::find($request->id);
		if($layout == null){
			$layout = new ChartLayout();
		}
		$layout->name = $request->name;
		$layout->content = $request->content;
		$layout->symbol = $request->symbol;
		$layout->resolution = $request->resolution;
		$layout->user_id = (string) $request->user()->id;
		$layout->save();
		return $this->response(200, "Status ok");
	}

	public function destroy($id)
	{
		$layout = ChartLayout::find($id);
		if($layout->user_id != app('request')->user()->id){
			abort(401, "Access denied!");
		}
		$layout->delete();
		return $this->response(200, 'Successful');
	}
}

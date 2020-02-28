<?php
namespace App\Http\Controllers;
use App\Sector;
use App\SectorIntraday;
use Illuminate\Http\Request;

use Laravel\Lumen\Routing\Controller as BaseController;

class SectorController extends Controller
{
	public function index(Request $request)
	{
		return Sector::select("id", "name")->whereNotIn("id", [23, 24])->get()->keyBy("id");
	}

	public function history(Request $request, $id)
	{
		$sector = Sector::find($id);
		$date = $request->date?:date('Y-m-d');
		return $sector->intradays()->where('index_date', $date)->get();
	}
}

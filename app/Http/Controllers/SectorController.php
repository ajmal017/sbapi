<?php
namespace App\Http\Controllers;
use App\Sector;
use Illuminate\Http\Request;

use Laravel\Lumen\Routing\Controller as BaseController;

class SectorController extends Controller
{
	public function index(Request $request)
	{
		return Sector::select("id", "name")->whereNotIn("id", [23, 24])->get()->keyBy("id");
	}
}

<?php
namespace App\Http\Controllers;
use App\CorporateAction;
use Illuminate\Http\Request;

use Laravel\Lumen\Routing\Controller as BaseController;

class CorporateActionController extends Controller
{
	public function index($symbol)
	{
		// return CorporateAction::where('instrument_code', $symbol)
		// ->leftJoin('instruments', 'instrument')
		// ->first();
	}
}

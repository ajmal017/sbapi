<?php
namespace App\Http\Controllers;
use App\News;

use Laravel\Lumen\Routing\Controller as BaseController;

class NewsController extends Controller
{
	public function index()
	{
		return News::latest()->paginate(5);
	}
}

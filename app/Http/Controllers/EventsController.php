<?php
namespace App\Http\Controllers;
use App\News;
use Illuminate\Http\Request;

use Laravel\Lumen\Routing\Controller as BaseController;

class EventsController extends Controller
{
	public function index(Request $request)
	{
		$data = [];
			$events =  News::where('post_date', ">=", \Carbon\Carbon::parse("1 month ago")->format('Y-m-d'))->where("details", "like", "%will be held on%")->where("details", "not like", "%Training Program%")->get();
			// $events->filter()
			foreach ($events as $key => $event) {
				$row = new \StdClass();
				preg_match_all("/held on (.*) at (.*) to .* Company for the (.*) on/", $event->details, $matches);
				try {
					$row->instrument = $event->prefix;
					$row->date = $matches[1][0];
					$row->timestamp = \Carbon\Carbon::parse($matches[1][0]." ".$matches[2][0])->getTimestamp();;
					$row->time = $matches[2][0];
					$row->type = $matches[3][0];

					if (strpos($matches[3][0], 'Q1') !== false) {
						$row->type = "Q1";
					}else if (strpos($matches[3][0], 'Q2') 	!== false){
						$row->type = "Q2";
					}else if (strpos($matches[3][0], 'Q3') 	!== false){
						$row->type = "Q3";
					}else {
						$row->type = "AGM";
					}
					
					if($row->timestamp < time()){
						continue;
					}

					$data[] = $row;
				} catch (\Exception $e) {
					// dump($e->getMessage());
				}
			}
			$data = collect($data);
			// dd($data);
			$sorted = $data->sortBy(function ($value, $key)
			{
				return $value->timestamp;
			});	
			// dump($data);

			return $sorted->groupBy("date");
	}
}

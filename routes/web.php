<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/


$router->get('/', function () use ($router) {
    return abort(404);
});

$router->group(['prefix' => 'v1'], function () use ($router) {
	//instruments
	/**
	* params: date || before 
	**/
	$router->get('/instruments', "InstrumentController@all");
	$router->get('/instruments/{code}', "InstrumentController@index");
	// avilable resulations: D, 1M, 5M param = /instruments/{code}/history?resolution=
	$router->get('/instruments/{code}/history', "InstrumentController@history");
	// $router->get('/instruments/{code}/intraday', "InstrumentController@intraday");
	$router->get('/instruments/{code}/chart', "InstrumentController@taChart");

	//sectors
	$router->get('/sectors', "SectorController@index");


	//fundamentals 
	$router->get('/fundamentals', "FundamentalController@index");

	//news 
	$router->get('/news', "NewsController@index");
	//events
	$router->get('/events', "EventsController@index");

	//auth
	$router->get("/auth/login", "UserController@authenticate");
	$router->post("/auth/login", "UserController@authenticate");
	$router->post("/auth/register", "UserController@register");
	$router->post("/auth/reset", "UserController@reset");
	//test routes


	//login protected routes
	$router->group(['middleware' => 'auth'], function () use ($router)
	{
		//user routes
		$router->get("/user", "UserController@index");
		$router->post("/user", "UserController@update");
		$router->post("/auth/logout", "UserController@logout");


		//tradingview routes
		$router->get('/charts', "TradingviewController@index");
		$router->post('/charts', "TradingviewController@store");
		$router->get('/charts/{id}', "TradingviewController@show");
		$router->delete('/charts/{id}', "TradingviewController@destroy");


	});
	$router->get('/test', function ()
	{
		event(new \App\Events\BroadcastEvent());
	});


});


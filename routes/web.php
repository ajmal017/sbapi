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
	// market data
	$router->get('/markets', "MarketController@index");



	//instruments
	/**
	* params: date || before 
	**/
	$router->get('/instruments', "InstrumentController@all");
	$router->get('/se', "InstrumentController@se");
	$router->get('/instruments/{code}', "InstrumentController@index");
	$router->get('/instruments/{code}/high-low', "InstrumentController@highLow");
	$router->get('/instruments/{code}/news', "InstrumentController@news");
	$router->get('/instruments/{code}/history', "InstrumentController@history");// avilable resulations: D, 1M, 5M param = /instruments/{code}/history?resolution=
	// $router->get('/instruments/{code}/intraday', "InstrumentController@intraday");
	$router->get('/instruments/{code}/chart', "InstrumentController@taChart");

	//sectors
	$router->get('/sectors', "SectorController@index");
	$router->get('/sectors/{id}/history', "SectorController@history");// avilable resulations: D, 1M, 5M param = /instruments/{code}/history?resolution=
	// $router->get('/instruments/{code}/intraday', "InstrumentController@intraday");

	//fundamentals 
	$router->get('/fundamentals', "FundamentalController@index");	/*all latest metas for all instrument*/
	$router->get('/fundamentals/{meta}', "FundamentalController@show");	/*specific meta for  all instrument*/
	/*query string params: groupBy=> meta_key/meta_date/code, default: code*/
	$router->get('/instruments/{symbol}/fundamentals/{meta}', "FundamentalController@history");	/*meta history of a specific key&instrument *****coma seperated meta keys******/


	/*Corporate actions*/
	$router->get('/instruments/{symbol}/corporate-actions', "InstrumentController@corporateActions");

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

	//screeners
	$router->get("/screeners", "ScreenerController@index");


	//login protected routes
	$router->group(['middleware' => 'auth'], function () use ($router)
	{
		//user routes
		$router->get("/user", "UserController@index");
		$router->post("/user", "UserController@update");
		$router->post("/auth/logout", "UserController@logout");

		//portfolios
		$router->get("/user/portfolios", "PortfolioController@index");
		$router->post("/user/portfolios", "PortfolioController@create");
		$router->get("/user/portfolios/{id}", "PortfolioController@show");
		$router->post("/user/portfolios/{id}", "PortfolioController@update");
		$router->post("/user/portfolios/{id}/delete", "PortfolioController@delete");

		// watchlist
		$router->get("/user/watchlist", "WatchlistController@index");

		// screeneers
		$router->get("/user/watchlist", "WatchlistController@index");

		//monitor instruments
		$router->get("/user/monitor-instruments", "UserController@monitorInstruments");
		$router->post("/user/monitor-instruments", "UserController@monitorInstrumentsSave");

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


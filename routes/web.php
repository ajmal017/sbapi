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

header("Access-Control-Allow-Origin: *");

$router->get('/', function () use ($router) {
    return abort(404);
});

$router->group(['prefix' => 'v1'], function () use ($router) {
	//instruments
	$router->get('/instrument/{code}', "InstrumentController@index");
	$router->get('/instrument/{code}/intraday', "InstrumentController@intraday");

	//news 
	$router->get('/news', "NewsController@index");

});

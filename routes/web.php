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

$router->get('/', function () {
    $url = route('upcoming_list');
    return view('home', ['url' => $url]);
});


$router->group(['prefix' => 'api'], function () use ($router) {
    $router->get('/movies/upcoming', ['as' => 'upcoming_list', 'uses' => 'MovieController@index']);
	$router->get('/movies/detail', ['as' => 'detail_movie', 'uses' => 'MovieController@show']);
});
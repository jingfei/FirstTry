<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function(){});

/* map */
Route::get('map', 'MapController@map');
Route::get('map2', 'MapController@map2');
Route::get('xml', 'MapController@xml');
Route::get('health', 'MapController@health');

/* login & logout */
Route::post('register', 'LoginController@register');
Route::post('login', 'LoginController@login');
Route::get('logout', 'LoginController@logout');
/******************/


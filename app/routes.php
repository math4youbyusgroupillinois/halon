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

Route::get('/', function()
{
	return View::make('index');
});


Route::group(array('prefix' => 'service'), function() {
  Route::resource('authenticate', 'AuthenticationController');
});

Route::resource('locations', 'LocationsController');

Route::get('users/generate_passwords', array('uses' => 'UsersController@generate_passwords'));
Route::resource('users', 'UsersController');

Route::get('setup/generate_passwords', array('uses' => 'SetupController@generate_passwords'));

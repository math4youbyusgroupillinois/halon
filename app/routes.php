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

Route::resource('admin/locations', 'Admin\LocationsController');

Route::resource('users', 'Admin\UsersController');

Route::resource('locations', 'Admin\LocationsController',
    array('only' => array('index')));

Route::resource('print_jobs', 'PrintJobsController');

Route::get('/dashboard', function() {
  return View::make('dashboard');
});
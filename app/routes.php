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

Route::get('/dashboard', function() {
  return View::make('dashboard');
});

Route::resource('locations', 'LocationsController');

/* Service Routes */
Route::group(array('prefix' => 'service'), function() {
  Route::resource('authenticate', 'AuthenticationController');
});

/* Printer Routes */
Route::group(array('prefix' => 'printer'), function() {
  Route::resource('print_jobs', 'Printer\PrintJobsController',
    array('only' => array('store')));
});

/* Admin Routes */
Route::group(array('prefix' => 'admin'), function() {
  Route::resource('users', 'Admin\UsersController');
});

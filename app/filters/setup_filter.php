<?php

class SetupFilter {

  public function filter()
  {
    // Route::enableFilters();
    // User::truncate();
    // $user = new User();
    // $user->role = 'admin';
    // $user->save();
    // $response = $this->action('GET', 'UsersController@index');
    // $this->assertResponseStatus(307);
    // Route::disableFilters()
    //if(User::where('password', '=', '')->count() > 0){
    return Response::json(array('flash' => 'Initiate Setup'), 307);
    //}
  }

}


<?php

class AuthenticationController extends BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
	  Auth::logout();
    return Response::json(array('flash' => 'You have been successfully logged out'), 200);	
  }

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
	  $credentials = array(
	    'password' =>  Input::get('password'));

		if (Auth::attempt($credentials)) {
	    return Response::json(array('user' => Auth::user()->toArray()), 202);
	  } else {
      return Response::json(array('flash' => 'Incorrect password, please retry'), 401);
	  }
	}
}
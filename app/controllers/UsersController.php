<?php

class UsersController extends \BaseController {

	public function __construct()
  {
		$this->beforeFilter('setupAuth');
		$this->beforeFilter('serviceAuth');
		$this->beforeFilter('serviceCSRF');
  }

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
  	$users = User::all();

    return Response::json($users->toArray(), 200);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
    $user = User::where('id', $id)
    				->take(1)
            ->get();

    return Response::json($user->toArray(), 200);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$user = User::find($id);

    if ( Input::get('password') )
    {
        $user->password = Hash::make(Input::get('password'));
    }

    $user->save();

     return Response::json($user->toArray(), 200);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}


}
<?php

class LocationsController extends SecuredController {
  protected $permitted = 'admin';

	public function __construct()
  {
    parent::__construct();
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
    return Location::all()->toJson();
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
    $attrs = Input::all();
		$location = Location::create($attrs);
    Log::info("The newly created location is: ", $location->toArray());
    Response::json($location->toJson(), 201);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
    $attrs = Input::all();
		$location = Location::find($id);
    if ($location->update($attrs)) 
      Response::json($location->toJson(), 201);
    else
      return Response::json(array('message' => 'Location failed to be updated'), 400);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
    $location = Location::find($id);
    if ($location->delete())
      return Response::json(array('message' => 'Location was deleted'), 200);
    else
      return Response::json(array('message' => 'Location failed to be deleted'), 400);
	}

}
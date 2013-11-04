<?php

namespace Admin;

use \Location;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;

class LocationsController extends \SecuredController {
  protected $permitted = array('admin');

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
    $transformed = Location::allWithLastPrintJob();
    return Response::json($transformed, 200);
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
    if ($location->update($attrs)) {
      Response::json($location->toJson(), 201);
    } else {
      return Response::json(array('message' => 'Location failed to be updated'), 400);
    }
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
    if ($location->delete()) {
      return Response::json(array('message' => 'Location was deleted'), 200);
    } else {
      return Response::json(array('message' => 'Location failed to be deleted'), 400);
    }
	}
}
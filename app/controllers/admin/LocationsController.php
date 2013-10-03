<?php

namespace Admin;

use \Location;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;

class LocationsController extends \SecuredController {
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
    $jobs = Location::with('printJobs')->get();
    $transformed = array();
    foreach ($jobs as $job) {
      $raw = $job->toArray();
      unset($raw['print_jobs']);
      if (!is_null($job->lastPrintJob())) {
        $augment = array_merge((array)$raw, (array)array('last_print_job' => $job->lastPrintJob()->toArray()));
        $transformed = array_merge((array)$transformed, (array)array($augment));  
      } else {
        $transformed = array_merge((array)$transformed, (array)array($raw));
      }
    }
    return Response::json($transformed, 200);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
    if ($this->permit('admin')) {
      $attrs = Input::all();
  		$location = Location::create($attrs);
      Log::info("The newly created location is: ", $location->toArray());
      Response::json($location->toJson(), 201);
    } else {
      return $this->unauthorizedResponse();
    }
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
    if ($this->permit('admin')) {
      $attrs = Input::all();
  		$location = Location::find($id);
      if ($location->update($attrs)) 
        Response::json($location->toJson(), 201);
      else
        return Response::json(array('message' => 'Location failed to be updated'), 400);
    } else {
      return $this->unauthorizedResponse();
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
    if ($this->permit('admin')) {
      $location = Location::find($id);
      if ($location->delete())
        return Response::json(array('message' => 'Location was deleted'), 200);
      else
        return Response::json(array('message' => 'Location failed to be deleted'), 400);
    } else {
      return $this->unauthorizedResponse();
    }
	}
}
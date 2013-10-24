<?php

class PrintJobsController extends \SecuredController {
	protected $permitted = array('admin', 'printer');
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
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
		$attrs = Input::all();
    $jobs = PrintJob::bulkCreate($attrs['items']);
    if ($jobs) {
      PrintJob::bulkEnque($jobs);

      $transform = function($job) {
        return $job->toArray();
      };

      $jobsToArray = array_map($transform, $jobs);

      Log::info("The newly created print job are: ", $jobsToArray);
      return Response::json(array('items' => $jobsToArray), 201);
    } else {
      return Response::json("{}", 400);
    }
    
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
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
		//
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
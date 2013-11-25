<?php

class PrintJobsController extends \SecuredController {
	protected $permitted = array('admin', 'printer');

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
      return Response::json("{}", 200);
    }
    
	}

}
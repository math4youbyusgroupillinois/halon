<?php

use \Location;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;

class LocationsController extends \BaseController {
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
}
<?php

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
    $all = Location::all();
    return Response::json($all, 200);
  }

  /**
   * Upload json file
   *
   * @return Response
   */
  public function import()
  {
    if (!$this->isPermitted('admin')) {
      Log::info("Unauthorized access attempt", array('context' => get_class($this)."#update"));
      return $this->unauthorizedResponse();
    }
    if (Input::hasFile('file')) {
      $file_content = file_get_contents(Input::file('file'));
      $json_content = json_decode($file_content, true);
      if (json_last_error() == JSON_ERROR_NONE) {
        if (isset($json_content["NURSE_UNIT"]["UNIT"])) {
          $locations = $json_content["NURSE_UNIT"]["UNIT"];
          $count = 0;
          foreach ($locations as $loc) {
            if ($loc["DONT_PRT_MAR"] === 0) {
              $display_key = $loc["DISPLAY_KEY"];
              $location = Location::where('display_key', '=', $display_key)->first();
              if ($location === NULL) {
                $location = new Location();
              }
              $location->description = $display_key;
              $location->printer_name = Config::get('app.print_server_name').$loc["PRINTER"];
              $location->phone_number = $loc["PHONE"];
              $location->todays_mar_file_name = 'dt_mar1_'.$loc["DISPLAY_KEY"].'.ps';
              $location->tomorrows_mar_file_name = 'dt_mar2_'.$loc["DISPLAY_KEY"].'.ps';
              $location->display_key = $display_key;
              $location->save();
              $count++;
            }
          }
          return Response::json($count, 201);
        } else {
          return Response::json('Syntax error - bad JSON', 400);
        }
      } else {
        return Response::json('Syntax error - bad JSON', 400);
      }
    } else {
      return Response::json('Location import failed', 400);
    }
  }

  /**
   * Store a newly created resource in storage.
   *
   * @return Response
   */
  public function store()
  {
    if (!$this->isPermitted('admin')) {
      Log::info("Unauthorized access attempt", array('context' => get_class($this)."#store"));
      return $this->unauthorizedResponse();
    }

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
    if (!$this->isPermitted('admin')) {
      Log::info("Unauthorized access attempt", array('context' => get_class($this)."#update"));
      return $this->unauthorizedResponse();
    }

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
    if (!$this->isPermitted('admin')) {
      Log::info("Unauthorized access attempt", array('context' => get_class($this)."#destroy"));
      return $this->unauthorizedResponse();
    }

    $location = Location::find($id);
    if ($location->delete()) {
      return Response::json(array('message' => 'Location was deleted'), 200);
    } else {
      return Response::json(array('message' => 'Location failed to be deleted'), 400);
    }
  }
}
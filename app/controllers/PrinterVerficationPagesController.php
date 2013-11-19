<?php


class PrinterVerficationPagesController extends \SecuredController {
  protected $permitted = array('admin', 'printer');

  /**
   * Store a newly created resource in storage.
   *
   * @return Response
   */
  public function store()
  {
    $pagesByLocation = array();
    $planned = Input::get('pages');
    foreach ($planned as $p) {
      $location = Location::find($p['location_id']);

      if ($location) {
        $dt = new DateTime();
        $ut = $location->description;
        $pt = $location->printer_name;
        array_push($pagesByLocation,
          array($location, new PrinterVerificationPage($dt, $ut, $pt)));
      }
    }

    $written = array();
    foreach ($pagesByLocation as $container) {
      $location = $container[0];
      $page     = $container[1];
      if ($page->write()) {
        array_push($written, 
          array('location_id' => $location->id, 'file_name' => $page->fileName()));
      }    
    }

    Log::info("The newly created printer verification pages are: ", $written);
    return Response::json(array('pages' => $written), 201);    
  }

}
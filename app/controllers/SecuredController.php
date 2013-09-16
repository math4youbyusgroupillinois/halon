<?php

class SecuredController extends BaseController {
  protected $permitted;

  public static function permit($role) {
    $permitted = $role;
  }

  /** 
   * The specified filters will do:
   * 1. CSRF protection
   * 2. Prevent unauthenticated users from accessing controllers
   * 3. Prevent unauthorized users from accessing controller
   */
  public function __construct() {
    $this->beforeFilter('serviceCSRF');
    $this->beforeFilter('authentication');
    
    // This authroization filter resides in the controller
    // because we need to pass the permitted role which is
    // not possible for filters defined in filters.php
    $perm = $this->getPermitted();
    $this->beforeFilter(function() use ($perm) {
      if (!UserAuthorization::permit($perm)) {
        return Response::json(array(
          'flash' => 'You Must Login to Continue'), 401);
      }
    });
  }

  public function getPermitted() {
    if (isset($this->permitted)) return $this->permitted;
  }
}
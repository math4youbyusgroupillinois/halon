<?php

class SecuredController extends BaseController {
  protected $permitted;

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
    $role = $this->getPermitted();
    $unauthorizedResponse = $this->unauthorizedResponse();
    $this->beforeFilter(function() use ($role, $unauthorizedResponse) {
      if (!empty($role) && !UserAuthorization::permit($role)) {
        print $role;
        return $unauthorizedResponse;
      }
    });
  }

  public function getPermitted() {
    if (isset($this->permitted)) return $this->permitted;
  }

  public function permit($role) {
    return UserAuthorization::permit($role);
  }

  public function unauthorizedResponse() {
    return Response::json(array(
        'flash' => 'You Must Login to Continue'), 401);
  }
}
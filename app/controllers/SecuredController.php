<?php

class SecuredController extends BaseController {
  protected $permitted;

  public static function permit($role) {
    $permitted = $role;
  }

  public function __construct() {
    $ctrl = $this;
      $this->beforeFilter(function() use ($ctrl) {
        if (!UserAuthorization::permit($ctrl->getPermitted())) {
          return Response::json(array(
            'flash' => 'You Must Login to Continue'
          ), 401);
        }
        // UserAuthorization::permit($this->getPermitted());
      });
  }

  public function getPermitted() {
    if (isset($this->permitted)) return $this->permitted;
  }
}
<?php
  
class UserAuthorization {
  public static function permit($role) {
    if (empty($role))
      return false;

    $user = Auth::user(); // empty doesn't support expressions
    if (empty($user))
      return false;

    if (in_array(Auth::user()->role, $role)) {
      return true;
    } else {
      return false;
    }
  }
}
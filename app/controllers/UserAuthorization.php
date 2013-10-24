<?php
  
class UserAuthorization {
  public static function permit($role) {
    if (empty($role))
      return false;

    $user = Auth::user(); // empty doesn't support expressions
    if (empty($user))
      return false;

    return in_array(Auth::user()->role, $role);
  }
}
<?php
  
class UserAuthorization {
  public static function permit($role) {
    if (empty($role))
      return false;

    $user = Auth::user(); // empty doesn't support expressions
    if (empty($user))
      return false;

    if (is_array($role)) {
      return in_array(Auth::user()->role, $role);
    } else {
      return Auth::user()->role == $role ;
    }
    
  }
}
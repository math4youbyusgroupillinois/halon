<?php
  
class UserAuthorization {
  public static function permit($role) {
    if (empty($role))
      return false;

    $user = Auth::user(); // empty doesn't support expressions
    if (empty($user))
      return false;

    return $role == Auth::user()->role;
  }
}
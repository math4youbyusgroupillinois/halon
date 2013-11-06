<?php

class Md5 {
  public static $instance;

  public function hash($str) {
    return md5($str);
  }

  public static function instance() {
    if (is_null(self::$instance)) {
      self::$instance = new Md5();
    }
    return self::$instance;
  }
}
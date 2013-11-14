<?php

class Mar {
  private $fileName;

  function __construct($fileName) {
    if (empty($fileName)) {
      throw new Exception("Missing MAR file name");
    }
    $this->fileName = $fileName;
  }

  public function filePath() {
    $path = Config::get('app.mar_path');

    if (empty($path)) {
      throw new Exception("The MAR path is not configured (app.mar_path), please set it in app.php");
    }

    return $path.DIRECTORY_SEPARATOR.basename($this->fileName);
  }
}
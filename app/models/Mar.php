<?php

class Mar {
  private $fileName;

  function __construct($fileName) {
    if (empty($fileName)) {
      throw new Exception("Missing MAR file name");
    }
    $this->fileName = $fileName;
  }

  public function filePath($isMar) {
    $path = NULL;
    if ($isMar) {
      $path = Config::get('app.mar_path');
    } else {
      $path = Printable::defaultBasePath();
    }

    if (empty($path)) {
      throw new Exception("The MAR path is not configured (app.mar_path), please set it in app.php");
    }

    return $path.DIRECTORY_SEPARATOR.basename($this->fileName);
  }
}
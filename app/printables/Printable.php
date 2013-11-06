<?php
  
abstract class Printable {
  //storage_path().'/printables'
  // new Md5Hasher()

  public function __construct($baseDir = NULL, $hasher = NULL) {
    $this->baseDir = ($baseDir == NULL) ? storage_path(). DIRECTORY_SEPARATOR . 'printables' : $baseDir;
    $this->hasher  = ($hasher  == NULL) ? Md5::instance() : $hasher;
  }

  abstract protected function identifier();
  abstract protected function content();

  public function write() {
    if ($this->baseDir && !is_dir($this->baseDir)) {
        mkdir($this->baseDir, 0777, true);
    }

    return FALSE === File::put($this->filePath(), $this->content());
  }

  public function filePath() {
    return $this->baseDir. DIRECTORY_SEPARATOR . $this->fileName();
  }

  public function fileName(){
    return $this->hasher->hash($this->identifier());
  }
  
}
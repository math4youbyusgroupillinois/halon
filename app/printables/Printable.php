<?php
  
abstract class Printable {
  public function __construct($baseDir = NULL, $hasher = NULL) {
    $this->baseDir = ($baseDir == NULL) ? self::defaultBasePath() : $baseDir;
    $this->hasher  = ($hasher  == NULL) ? Md5::instance() : $hasher;
  }

  abstract protected function identifier();
  abstract protected function content();

  public static function defaultBasePath() {
    return storage_path(). DIRECTORY_SEPARATOR . 'printables';
  }

  public function write() {
    if ($this->baseDir && !is_dir($this->baseDir)) {
        mkdir($this->baseDir, 0777, true);
    }
    
    $result = File::put($this->filePath(), $this->content());
    
    return !($result === FALSE); // $result may be a non-Boolean value which evaluates to FALSE. 
  }

  public function filePath() {
    return $this->baseDir. DIRECTORY_SEPARATOR . $this->fileName();
  }

  public function fileName(){
    return $this->hasher->hash($this->identifier());
  }
  
}
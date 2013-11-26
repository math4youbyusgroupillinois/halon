<?php

class Location extends Eloquent {

  /**
   * The database table used by the model.
   *
   * @var string
   */
  protected $table = 'locations';

  protected $fillable = array('description', 'phone_number', 'printer_name', 'todays_mar_file_name', 'tomorrows_mar_file_name');

  protected $appends = array('todays_mar_last_modified_date', 'tomorrows_mar_last_modified_date', 'last_mar_printed', 'last_mar_print_job', 'last_non_mar_print_job', 'short_printer_name');

  public function printJobs() {
    return $this->hasMany('printJob');
  }

  public function lastPrintJobCriteria($isMar) {
    return $this->printJobs()->isMar($isMar)->orderBy('enque_timestamp','desc');
  }

  public function lastMarPrintJob() {
    return $this->lastPrintJobCriteria(true)->first();
  }

  public function lastNonMarPrintJob() {
    return $this->lastPrintJobCriteria(false)->first();
  }

  public function getTodaysMarLastModifiedDateAttribute() {
    $date = NULL;
    if (!empty($this->todays_mar_file_name)) {
      $mar = new Mar($this->todays_mar_file_name);
      $path = $mar->filePath(true);
      if (File::exists($path)) {
        $date = new DateTime();
        $date = $date->setTimestamp(File::lastModified($path));
        $date = $date->format("Y-m-d\TH:i:sO");
      }    
    }
    
    return $date;
  }

  public function getTomorrowsMarLastModifiedDateAttribute() {
    $date = NULL;
    if (!empty($this->tomorrows_mar_file_name)) {
      $mar = new Mar($this->tomorrows_mar_file_name);
      $path = $mar->filePath(true);
      if (File::exists($path)) {
        $date = new DateTime();
        $date = $date->setTimestamp(File::lastModified($path));
        $date = $date->format("Y-m-d\TH:i:sO");
      }
    }
    return $date;
  }

  public function getLastMarPrintedAttribute() {
    $out = NULL;
    $lastFilePrinted = NULL;
    
    if ($this->lastMarPrintJob()) {
      $lastFilePrinted = $this->lastMarPrintJob()->file_name;
    }

    if ($lastFilePrinted != NULL) {
      if ($lastFilePrinted == $this->tomorrows_mar_file_name) {
        $out = "Tomorrow's";
      } elseif ($lastFilePrinted == $this->todays_mar_file_name) {
        $out = "Today's";
      }
    }

    return $out;
  }

  public function getLastMarPrintJobAttribute() {
    // toArray() is needed to work around JSON serialization problem
    // where no attributes are serialized
    $last = $this->lastMarPrintJob();
    return !is_null($last) ? $last->toArray() : null;
  }

  public function getLastNonMarPrintJobAttribute() {
    // toArray() is needed to work around JSON serialization problem
    // where no attributes are serialized
    $last = $this->lastNonMarPrintJob();
    return !is_null($last) ? $last->toArray() : null;
  }

  public function getShortPrinterNameAttribute() {
    $short = $this->printer_name;
    preg_match('@^\\\\\\\\+(.+)\\\\+(.+)@', $short, $matches);
    if (count($matches) == 3) {
      $short = $matches[2];
    }
    return $short;
  }
}
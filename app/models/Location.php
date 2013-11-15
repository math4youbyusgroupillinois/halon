<?php

class Location extends Eloquent {

  /**
   * The database table used by the model.
   *
   * @var string
   */
  protected $table = 'locations';

  protected $fillable = array('description', 'phone_number', 'printer_name', 'todays_mar_file_name', 'tomorrows_mar_file_name');

  protected $appends = array('todays_mar_last_modified_date', 'tomorrows_mar_last_modified_date', 'last_mar_printed');

  public function printJobs() {
    return $this->hasMany('printJob');
  }

  public function lastPrintJob() {
    return $this->printJobs()->orderBy('enque_timestamp','desc')->first();
  }

  public static function allWithLastPrintJob() {
    $locations = Location::with('printJobs')->get();
    $transformed = array();
    foreach ($locations as $loc) {
      $raw = $loc->toArray();
      unset($raw['print_jobs']);
      if (!is_null($loc->lastPrintJob())) {
        $augment = array_merge((array)$raw, (array)array('last_print_job' => $loc->lastPrintJob()->toArray()));
        $transformed = array_merge((array)$transformed, (array)array($augment));  
      } else {
        $transformed = array_merge((array)$transformed, (array)array($raw));
      }
    }
    return $transformed;
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
    
    if ($this->lastPrintJob()) {
      $lastFilePrinted = $this->lastPrintJob()->file_name;
    }

    if ($lastFilePrinted == $this->tomorrows_mar_file_name) {
      $out = "Tomorrow's";
    } elseif ($lastFilePrinted == $this->todays_mar_file_name) {
      $out = "Today's";
    }

    return $out;
  }
}
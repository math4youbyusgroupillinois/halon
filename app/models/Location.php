<?php

class Location extends Eloquent {

  /**
   * The database table used by the model.
   *
   * @var string
   */
  protected $table = 'locations';

  protected $fillable = array('description', 'phone_number', 'printer_name', 'todays_mar_file_name', 'tomorrows_mar_file_name');

  protected $appends = array('todays_mar_file_last_modified_date', 'tomorrows_mar_file_last_modified_date');

  public function printJobs() {
    return $this->hasMany('printJob');
  }

  public function lastPrintJob() {
    return $this->printJobs()->orderBy('enque_timestamp','desc')->first();
  }

  public static function allWithLastPrintJob() {
    $jobs = Location::with('printJobs')->get();
    $transformed = array();
    foreach ($jobs as $job) {
      $raw = $job->toArray();
      unset($raw['print_jobs']);
      if (!is_null($job->lastPrintJob())) {
        $augment = array_merge((array)$raw, (array)array('last_print_job' => $job->lastPrintJob()->toArray()));
        $transformed = array_merge((array)$transformed, (array)array($augment));  
      } else {
        $transformed = array_merge((array)$transformed, (array)array($raw));
      }
    }
    return $transformed;
  }

  public function getTodaysMarFileLastModifiedDate() {
    $date = NULL;
    if (!empty($this->todays_mar_file_name)) {
      $mar = new Mar($this->todays_mar_file_name);
      $date = File::lastModified($mar->filePath());
    }
    
    return $date;
  }

  public function getTomorrowsMarFileLastModifiedDate() {
    $date = NULL;
    if (!empty($this->tomorrows_mar_file_name)) {
      $mar = new Mar($this->tomorrows_mar_file_name);
      $date = File::lastModified($mar->filePath());
    }
    return $date;
  }
}
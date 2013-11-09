<?php

class Location extends Eloquent {

  /**
   * The database table used by the model.
   *
   * @var string
   */
  protected $table = 'locations';

  protected $fillable = array('description', 'phone_number', 'printer_name', 'todays_mar_file_name');

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

}
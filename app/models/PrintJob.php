<?php

class PrintJob extends Eloquent {

  /**
   * The database table used by the model.
   *
   * @var string
   */
  protected $table = 'print_jobs';

  protected $fillable = array('file_name', 'printer_name', 'location_id');

  protected $appends = array('is_enque_successful');

  public static function bulkCreate($bulkJobsAttrs) {
    $created = DB::transaction(function() use ($bulkJobsAttrs) {
      $fn = function($attrs) {
        return PrintJob::create($attrs);
      };

      return array_map($fn, $bulkJobsAttrs);
    });
    return $created;
  }

  /**
   * 
   */
  public static function bulkEnque($jobs) {
    array_walk($jobs, function(&$job) {
      $job->enque_failure_message = null;
      $job->enque_timestamp = null;
      try {
        $r = Printer::enque($job->printer_name, $job->getFilePath());
        if ($r === FALSE) {
          $job->enque_failure_message = "Failed to print '$job->file_name' to the printer '$job->printer_name'";
        }
      } catch(Northwestern\Printer\PrinterException $e) {
        $job->enque_failure_message = $e->getMessage();
      }

      $job->enque_timestamp = new DateTime();
      $job->save();
    });
    return $jobs;
  }

  public function getFilePath() {
    $path = Config::get('app.mar_path');
    if (empty($path)) {
      throw new Exception("The MAR path is not configured (app.mar_path), please set it in app.php");
    }

    return $path.DIRECTORY_SEPARATOR.basename($this->file_name);
  }

  public function getIsEnqueSuccessfulAttribute() {
    if (is_null($this->attributes['enque_timestamp'])) {
      return;
    }
    return is_null($this->attributes['enque_failure_message']);
  }
}
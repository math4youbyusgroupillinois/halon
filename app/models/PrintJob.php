<?php

class PrintJob extends Eloquent {

  /**
   * The database table used by the model.
   *
   * @var string
   */
  protected $table = 'print_jobs';

  protected $fillable = array('file_name', 'printer_name');

  public static function bulkCreate($bulkJobsAttrs) {
    $created = DB::transaction(function() use ($bulkJobsAttrs) {
      $fn = function($attrs) {
        return PrintJob::create($attrs);
      };

      return array_map($fn, $bulkJobsAttrs);
    });
    return $created;
  }

  public static function bulkEnque($jobs) {
    array_walk($jobs, function(&$job) {
      $result = Printer::enque($job->printer_name, $job->getFilePath());
      $job->enque_status = $result;
      $job->enque_timestamp = new DateTime();
      $job->save();
    });
    return $jobs;
  }

  public function getFilePath() {
    $path = Config::get('app.mar_path');
    if (empty($path)) {
      throw new Exception("The MAR path is not configured, please set it in app.php");
    }

    return $path.DIRECTORY_SEPARATOR.basename($this->file_name);
  }
}
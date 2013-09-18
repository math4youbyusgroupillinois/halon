<?php

class PrintJob extends Eloquent {

  /**
   * The database table used by the model.
   *
   * @var string
   */
  protected $table = 'print_jobs';

  protected $fillable = array('file_path', 'printer_name');

  public static function bulkCreate($bulkJobsAttrs) {
    $created = DB::transaction(function() use ($bulkJobsAttrs){
      $created = array();

      foreach ($bulkJobsAttrs as $attrs) {
        array_push($created, PrintJob::create($attrs));
      }
      return $created;
    });
    return $created;
  }
}
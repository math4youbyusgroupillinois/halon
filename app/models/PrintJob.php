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
        $pj = PrintJob::create($attrs);
        Printer::enque($pj->printer_name, $pj->file_path);
        array_push($created, $pj);
      }
      return $created;
    });
    return $created;
  }
}
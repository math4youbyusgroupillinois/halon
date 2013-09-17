<?php

class PrintJob extends Eloquent {

  /**
   * The database table used by the model.
   *
   * @var string
   */
  protected $table = 'print_jobs';

  protected $fillable = array('file_path', 'printer_name');
}
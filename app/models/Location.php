<?php

class Location extends Eloquent {

  /**
   * The database table used by the model.
   *
   * @var string
   */
  protected $table = 'locations';

  protected $fillable = array('description', 'phone_number', 'printer_name', 'mar_file_name');

  public function printJobs() {
    return $this->hasMany('printJob');
  }

  public function lastPrintJob() {
    return $this->printJobs()->orderBy('enque_timestamp','desc')->first();
  }

}
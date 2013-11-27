<?php

class LocationConfiguration extends Eloquent {

  /**
   * The database table used by the model.
   *
   * @var string
   */
  protected $table = 'location_configurations';

  public static function isImportRequired() {
    $import_required = false;
    $last_imported = LocationConfiguration::orderBy('updated_at', 'desc')->first();
    if ($last_imported == null) {
      $import_required = true;
    } else {
      $path = Config::get('app.import_file_path');
      if (File::exists($path)) {
        $date = new DateTime();
        $date = $date->setTimestamp(File::lastModified($path));
        $date = $date->format("Y-m-d H:i:s");
        if ($last_imported->imported_at < $date) {
          $import_required = true;
        }
      }
    }
    return $import_required;
  }
}
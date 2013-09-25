<?php

class LocationsSeeder extends Seeder {
  public function run(){
    DB::table('locations')->delete();

    Location::create(array(
      'description'   => '11th floor',
      'phone_number'  => '312-123-4567',
      'printer_name'  => 'LaserJet XL',
      'mar_file_name' => '11_mar.ps'
    ));

    Location::create(array(
      'description'   => '12th floor',
      'phone_number'  => '312-555-0000',
      'printer_name'  => 'DeskJet 570',
      'mar_file_name' => '12_mar.ps'
    ));

    Location::create(array(
      'description'  => 'NUBIC',
      'phone_number' => '123-321-1230',
      'printer_name' => 'NPI6C6DB9 (HP LaserJet 500 colorMFP M570dn)',
      'mar_file_name'=> 'bell_206.ps'
    ));
  }
}

?>
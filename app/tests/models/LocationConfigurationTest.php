<?php

class LocationConfigurationTest extends TestCase {
  public function setUp() {
    parent::setUp();
    Config::set('app.import_file_path', __DIR__ .'/../fixtures/test-file.json');
  }

  public function testIsImportRequiredForFirstImport() {
    LocationConfiguration::truncate();

    $actual = LocationConfiguration::isImportRequired();
    $this->assertTrue($actual);
  }

  public function testIsImportRequiredForNoFileChanged() {
    $date = $this->getLocationFileTimeStamp();
    $date = $date->format("Y-m-d H:i:s");
    $this->createLocationConfiguration($date);

    $actual = LocationConfiguration::isImportRequired();
    $this->assertFalse($actual);
  }

  public function testIsImportRequiredForFileChanged() {
    $date = $this->getLocationFileTimeStamp();
    // file updated date is 10 days after last import.
    $date->sub(new DateInterval('P10D'));
    $date = $date->format("Y-m-d H:i:s");
    $this->createLocationConfiguration($date);

    $actual = LocationConfiguration::isImportRequired();
    $this->assertTrue($actual);
  }

  public function createLocationConfiguration($date) {
    $location_configuaration = new LocationConfiguration();
    $location_configuaration->imported_at = $date;
    $location_configuaration->save();
  }

  public function getLocationFileTimeStamp() {
    $date = new DateTime();
    $path = Config::get('app.import_file_path');
    $date = $date->setTimestamp(File::lastModified($path));
    return $date;
  }
}
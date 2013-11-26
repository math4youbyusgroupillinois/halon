<?php

class LocationTest extends TestCase {

  public function testSaveAndRetrieve()
  {
    $location = new Location();
    $location->description = '5th floor';
    $location->save();
    $found = Location::where('description', '=', '5th floor')->first();
    $this->assertEquals('5th floor', $found->description);
  }

  public function testPrintJobs()
  {
    $location = $this->createLocation('test floor');

    $date = new DateTime('2013-10-02');
    $this->createPrintJob('uno', 'bar.ps', $location->id, 'Fail', $date->setTime(14,55,59));
    $this->createPrintJob('dos', 'qux.ps', $location->id, 'Success', $date->setTime(14,55,55));

    $pjs = $location->printJobs();
    $this->assertEquals(2, sizeof($pjs->getResults()));
  }

  public function testLastPrintJobPicksByTime()
  {
    $location = $this->createLocation('test floor');

    $date = new DateTime('2013-10-02');
    $this->createPrintJob('uno', 'bar.ps', $location->id, 'Fail', $date->setTime(14,55,59));
    $this->createPrintJob('dos', 'qux.ps', $location->id, 'Success', $date->setTime(14,55,55));

    $pj = $location->lastMarPrintJob();

    $this->assertEquals('Fail', $pj->enque_failure_message);
    $time = $date-> setTime(14, 55, 59);
    $this->assertEquals(date_format($date, 'Y-m-d H:i:s'), $pj->enque_timestamp);
  }

  public function testLastMarPrintJobOnlyPicksMars()
  {
    $location = $this->createLocation('test floor');

    $date = new DateTime('2013-10-02');
    $this->createPrintJob('uno', 'bar.ps', $location->id, 'Fail', $date->setTime(14,55,59), false);
    $this->createPrintJob('dos', 'qux.ps', $location->id, 'Success', $date->setTime(14,55,55));

    $pj = $location->lastMarPrintJob();

    $this->assertEquals('Success', $pj->enque_failure_message);
    $time = $date-> setTime(14, 55, 55);
    $this->assertEquals(date_format($date, 'Y-m-d H:i:s'), $pj->enque_timestamp);
  }

  public function testLastNonMarPrintJobOnlyPicksNonMars()
  {
    $location = $this->createLocation('test floor');

    $date = new DateTime('2013-10-02');
    $this->createPrintJob('uno', 'bar.ps', $location->id, 'Fail', $date->setTime(14,55,59), false);
    $this->createPrintJob('dos', 'qux.ps', $location->id, 'Success', $date->setTime(14,55,55));

    $pj = $location->lastNonMarPrintJob();

    $this->assertEquals('Fail', $pj->enque_failure_message);
    $time = $date-> setTime(14, 55, 59);
    $this->assertEquals(date_format($date, 'Y-m-d H:i:s'), $pj->enque_timestamp);
  }

  public function testTomorrowsMarFileLastModifiedDate() {
    $expected = new DateTime('2013-10-02');
    $expected = $expected->format('U');
    File::shouldReceive('exists')->once()->with('/tmp/halon/bar.ps')->andReturn(true);
    File::shouldReceive('lastModified')->once()->with('/tmp/halon/bar.ps')->andReturn($expected);

    $location = $this->createLocation('test floor');
    $location->tomorrows_mar_file_name = 'bar.ps';
    $actual = $location->getTomorrowsMarLastModifiedDateAttribute();
    $this->assertTrue(substr_count('2013-10-02', 0) != 0, "Expected substring 2013-10-02, but actual is $actual");
  }

  public function testLastMarPrintJob() {
    $location = $this->createLocation('test floor');

    $date = new DateTime('2013-10-02');
    $this->createPrintJob('uno', 'bar.ps', $location->id, 'Fail', $date->setTime(14,55,59));
    $this->createPrintJob('dos', 'qux.ps', $location->id, 'Success', $date->setTime(14,55,55));

    $this->assertEquals('test floor', $location->description);

    $pj = $location->lastMarPrintJob();
    $this->assertNotNull($pj);
    $this->assertEquals('uno', $pj->printer_name);
  }

  public function testLastPrintJobSerialized() {
    $location = $this->createLocation('test floor');

    $date = new DateTime('2013-10-02');
    $this->createPrintJob('uno', 'bar.ps', $location->id, 'Fail', $date);

    $actualSerialized = $location->toArray();

    $this->assertEquals('test floor', $actualSerialized['description']);
    $this->assertFalse(array_key_exists('print_jobs', $actualSerialized));
  }

  public function testGetLastMarPrintedAttributeWhenToday() {
    $location = $this->createLocation('test floor');
    $location->todays_mar_file_name = "qux.ps";

    $date = new DateTime('2013-10-02');
    $this->createPrintJob('uno', 'bar.ps', $location->id, 'Fail', $date->setTime(14,55,59), false);
    $this->createPrintJob('dos', 'qux.ps', $location->id, 'Success', $date->setTime(14,55,55));

    $this->assertEquals("Today's", $location->getLastMarPrintedAttribute());
  }

  public function testGetLastMarPrintedAttributeWhenTomorrow() {
    $location = $this->createLocation('test floor');
    $location->tomorrows_mar_file_name = "qux.ps";

    $date = new DateTime('2013-10-02');
    $this->createPrintJob('uno', 'bar.ps', $location->id, 'Fail', $date->setTime(14,55,59), false);
    $this->createPrintJob('dos', 'qux.ps', $location->id, 'Success', $date->setTime(14,55,55));

    $this->assertEquals("Tomorrow's", $location->getLastMarPrintedAttribute());
  }

  public function testGetLastMarPrintedAttributeWhenNeither() {
    $location = $this->createLocation('test floor');
    $location->todays_mar_file_name = "qux.ps";

    $this->assertNull($location->getLastMarPrintedAttribute());
  }

  public function testGetShortPrinterNameAttributeWithNetworkName() {
    $loc = new Location();

    $loc->printer_name = '\\\\foo\\bar';
    $this->assertEquals('bar', $loc->getShortPrinterNameAttribute());

    $loc->printer_name = '\\\\abc-def-gh1\\baz-huf-gh1';
    $this->assertEquals('baz-huf-gh1', $loc->getShortPrinterNameAttribute());
  }

  public function testGetShortPrinterNameAttributeWithNonNetworkName() {
    $loc = new Location();

    $loc->printer_name = 'moo';
    $this->assertEquals('moo', $loc->getShortPrinterNameAttribute());
  }

  // Helper Methods

  private function createLocation($description)
  {
    $location = new Location();
    $location->description = $description;
    $location->save();
    $found = Location::where('description', '=', $description)->first();
    return $found;
  }

  private function createPrintJob($printer_name, $file_name, $location_id, $message, $timestamp, $mar = true)
  {
    $job = new PrintJob(array('printer_name' => $printer_name, 'file_name' => $file_name, 'location_id' => $location_id, 'mar' => $mar));
    $job->enque_failure_message = $message;
    $job->enque_timestamp = $timestamp;
    $job->save();
    return $job;
  }
}
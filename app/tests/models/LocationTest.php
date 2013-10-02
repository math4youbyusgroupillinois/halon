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

  public function testLastPrintJob()
  {
    $location = $this->createLocation('test floor');

    $date = new DateTime('2013-10-02');
    $this->createPrintJob('uno', 'bar.ps', $location->id, 'Fail', $date->setTime(14,55,59));
    $this->createPrintJob('dos', 'qux.ps', $location->id, 'Success', $date->setTime(14,55,55));

    $pj = $location->lastPrintJob();

    $this->assertEquals('Fail', $pj->enque_failure_message);
    $time = $date-> setTime(14, 55, 59);
    $this->assertEquals(date_format($date, 'Y-m-d H:i:s'), $pj->enque_timestamp);
  }

  private function createLocation($description)
  {
    $location = new Location();
    $location->description = $description;
    $location->save();
    $found = Location::where('description', '=', $description)->first();
    return $found;
  }

  private function createPrintJob($printer_name, $file_name, $location_id, $message, $timestamp)
  {
    $job = new PrintJob(array('printer_name' => $printer_name, 'file_name' => $file_name, 'location_id' => $location_id));
    $job->enque_failure_message = $message;
    $job->enque_timestamp = $timestamp;
    $job->save();
    return $job;
  }
}
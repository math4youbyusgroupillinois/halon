<?php

class PrintJobTest extends TestCase {

  public function testBulkCreateReturnsPrintJobs() {
    PrintJob::truncate();

    $actual = PrintJob::bulkCreate(array(
      array('printer_name' => 'uno', 'file_name' => 'bar.ps'), 
      array('printer_name' => 'dos', 'file_name' => 'qux.ps')));

    $this->assertEquals(2, count($actual));
    $this->assertEquals('bar.ps', $actual[0]->file_name);
    $this->assertEquals('qux.ps', $actual[1]->file_name);
  }

  public function testBulkCreateCreatesPrintJobs() {
    PrintJob::truncate();

    PrintJob::bulkCreate(array(
      array('printer_name' => 'uno', 'file_name' => 'bar.ps'), 
      array('printer_name' => 'dos', 'file_name' => 'qux.ps')));

    $actual = PrintJob::all();
    $this->assertEquals(2, $actual->count());
    $this->assertEquals('bar.ps', $actual[0]->file_name);
    $this->assertEquals('qux.ps', $actual[1]->file_name);
  }

  public function testBulkEnque() {
    Printer::shouldReceive('enque')->once()->with('uno', '/tmp/halon/bar.ps');
    Printer::shouldReceive('enque')->once()->with('dos', '/tmp/halon/qux.ps');

    PrintJob::truncate();

    $jobs = array(
      new PrintJob(array('printer_name' => 'uno', 'file_name' => 'bar.ps')),
      new PrintJob(array('printer_name' => 'dos', 'file_name' => 'qux.ps')));

    PrintJob::bulkEnque($jobs);
  }

  public function testSanity() {
    $loc = Location::create(array());
    $this->assertNotNull($loc->id);

    $loc->printJob()->create(array('file_name' => 'foo', 'printer_name' => 'bar'));
    $pj = $loc->printJob()->getResults();
    $this->assertNotNull($loc->printJob()->getResults()->id);
    $this->assertEquals('foo', $pj->file_name);
    $this->assertEquals('bar', $pj->printer_name);
    $this->assertEquals($loc->id, $pj->location_id);
  }
}
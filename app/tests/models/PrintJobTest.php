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
    $printablePath = Printable::defaultBasePath();
    Printer::shouldReceive('enque')->once()->with('uno', "$printablePath/bar.ps")->andReturn(true);
    Printer::shouldReceive('enque')->once()->with('dos', "$printablePath/qux.ps")->andReturn(true);

    PrintJob::truncate();

    $jobs = array(
      new PrintJob(array('printer_name' => 'uno', 'file_name' => 'bar.ps')),
      new PrintJob(array('printer_name' => 'dos', 'file_name' => 'qux.ps')));

    PrintJob::bulkEnque($jobs);
  }

  public function testBulkEnqueWithMARs() {
    Printer::shouldReceive('enque')->once()->with('uno', '/tmp/halon/bar.ps')->andReturn(true);
    Printer::shouldReceive('enque')->once()->with('dos', '/tmp/halon/qux.ps')->andReturn(true);

    PrintJob::truncate();

    $jobs = array(
      new PrintJob(array('printer_name' => 'uno', 'file_name' => 'bar.ps', 'mar' => true)),
      new PrintJob(array('printer_name' => 'dos', 'file_name' => 'qux.ps', 'mar' => true)));

    PrintJob::bulkEnque($jobs);
  }


  public function testBulkEnqueHasFailureMessageWhenPrinterExceptionThrown() {
    Printer::shouldReceive('enque')->once()->with('bad-printer', '/tmp/halon/bar.ps')->andThrow('Northwestern\Printer\PrinterException', 'Massive Failure');

    PrintJob::truncate();

    $jobs = array(
      new PrintJob(array('printer_name' => 'bad-printer', 'file_name' => 'bar.ps', 'mar' => true)));

    $actual = PrintJob::bulkEnque($jobs);
    $this->assertEquals('bad-printer', $actual[0]->printer_name);
    $this->assertEquals('bar.ps',      $actual[0]->file_name);
    $this->assertEquals('Massive Failure', $actual[0]->enque_failure_message);
    $this->assertNotNull($actual[0]->enque_timestamp);
  }

  public function testBulkEnqueHasFailureMessageWhenPrinterReturnsFalse() {
    Printer::shouldReceive('enque')->once()->with('uno', '/tmp/halon/bad-file')->andReturn(false);

    PrintJob::truncate();

    $jobs = array(
      new PrintJob(array('printer_name' => 'uno', 'file_name' => 'bad-file', 'mar' => true)));

    $actual = PrintJob::bulkEnque($jobs);
    $this->assertEquals('uno', $actual[0]->printer_name);
    $this->assertEquals('bad-file',      $actual[0]->file_name);
    $this->assertEquals("Failed to print 'bad-file' to the printer 'uno'", $actual[0]->enque_failure_message);
    $this->assertNotNull($actual[0]->enque_timestamp);
  }

  public function testSanity() {
    $loc = Location::create(array());
    $this->assertNotNull($loc->id);

    $result = $loc->printJobs()->create(array('file_name' => 'foo', 'printer_name' => 'bar'));
    $pj = $loc->lastPrintJob();
    $this->assertNotNull($loc->id);
    $this->assertEquals('foo', $pj->file_name);
    $this->assertEquals('bar', $pj->printer_name);
    $this->assertEquals($loc->id, $pj->location_id);
  }
}
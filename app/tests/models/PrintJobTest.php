<?php

class PrintJobTest extends TestCase {

  public function testBulkCreate() {
    Printer::shouldReceive('enque')->once()->with('uno', 'foo/bar.ps');
    Printer::shouldReceive('enque')->once()->with('dos', 'baz/qux.ps');

    PrintJob::truncate();

    PrintJob::bulkCreate(array(
      array('printer_name' => 'uno', 'file_path' => 'foo/bar.ps'), 
      array('printer_name' => 'dos', 'file_path' => 'baz/qux.ps')));

    $actual = PrintJob::all();
    $this->assertEquals(2, $actual->count());
    $this->assertEquals('foo/bar.ps', $actual[0]->file_path);
    $this->assertEquals('baz/qux.ps', $actual[1]->file_path);
  }
}
<?php

class PrintJobTest extends TestCase {

  public function testBulkCreate() {
    $mock = $this->getMock('TestPrinterDriver');

    $mock->expects($this->at(0))
        ->method('enque')
        ->with($this->equalTo('uno'), $this->equalTo('foo/bar.ps'));

    $mock->expects($this->at(1))
        ->method('enque')
        ->with($this->equalTo('dos'), $this->equalTo('baz/qux.ps'));

    $this->app['printer.driver'] = $mock;

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
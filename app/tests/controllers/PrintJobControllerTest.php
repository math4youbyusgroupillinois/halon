<?php

class PrintJobsControllerTest extends TestCase {
  public function setUp() {
    parent::setUp();

    $user = new User();
    $user->role = 'printer';
    $this->be($user);
  }

  public function testPostSingleJob() {
    PrintJob::truncate();

    $response = $this->action('POST', 'PrintJobsController@store', 
      array('items' => array(
        array('file_path' => 'foo/bar.ps'), 
        array('file_path' => 'baz/qux.ps'))));

    $this->assertResponseStatus(201);

    $actual = PrintJob::all();
    $this->assertEquals(2, $actual->count());
    $this->assertEquals('foo/bar.ps', $actual[0]->file_path);
    $this->assertEquals('baz/qux.ps', $actual[1]->file_path);
  }
}
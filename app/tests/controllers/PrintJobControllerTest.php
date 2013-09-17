<?php

class PrintJobsControllerTest extends TestCase {
  public function setUp() {
    parent::setUp();

    $user = new User();
    $user->role = 'printer';
    $this->be($user);

    PrintJob::truncate();
  }

  public function testPostSingleJob() {
    $response = $this->action('POST', 'PrintJobsController@store', array('file_path' => 'foo/bar.ps'));
    $this->assertResponseStatus(201);

    $actual = PrintJob::where('file_path', '=', 'foo/bar.ps');
    $this->assertEquals(1, $actual->count());
    $this->assertEquals('foo/bar.ps', $actual->first()->file_path);
  }
}
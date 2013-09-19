<?php

class PrintJobsControllerTest extends TestCase {
  public function setUp() {
    parent::setUp();

    $user = new User();
    $user->role = 'printer';
    $this->be($user);
  }

  public function sendSuccessfullRequest() {
    $this->action('POST', 'PrintJobsController@store', 
      array('items' => array(
        array('file_path' => 'foo/bar.ps'), 
        array('file_path' => 'baz/qux.ps'))));

    $this->assertResponseStatus(201);
  }

  public function testPostRespondsWithJSON() {
    PrintJob::truncate();

    $this->sendSuccessfullRequest();
    
    $jsonResponse = json_decode($this->client->getResponse()->getContent(), true);
    $this->assertNotNull($jsonResponse['items'][0]['id']);
    $this->assertNotNull($jsonResponse['items'][1]['id']);
    $this->assertEquals('foo/bar.ps', $jsonResponse['items'][0]['file_path']);
    $this->assertEquals('baz/qux.ps', $jsonResponse['items'][1]['file_path']);
  }
}
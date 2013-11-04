<?php

class PrintJobsControllerTest extends TestCase {
  public function setUp() {
    parent::setUp();

    $user = new User();
    $user->role = 'printer';
    $this->be($user);
  }

  public function sendSuccessfullRequest() {
    $this->action('POST', 'Printer\PrintJobsController@store', 
      array('items' => array(
        array('file_name' => 'foo/bar.ps', 'location_id' => 2), 
        array('file_name' => 'baz/qux.ps', 'location_id' => 4))));

    $this->assertResponseStatus(201);
  }

  public function testPostRespondsWithJSON() {
    PrintJob::truncate();

    $this->sendSuccessfullRequest();
    
    $jsonResponse = json_decode($this->client->getResponse()->getContent(), true);
    $item0 = $jsonResponse['items'][0];
    $item1 = $jsonResponse['items'][1];
    $this->assertNotNull($item0['id']);
    $this->assertNotNull($item1['id']);
    $this->assertEquals(2, $item0['location_id']);
    $this->assertEquals(4, $item1['location_id']);
    $this->assertNotNull($item0['is_enque_successful']);
    $this->assertNotNull($item1['is_enque_successful']);
    $this->assertNotNull($item0['enque_timestamp']);
    $this->assertNotNull($item1['enque_timestamp']);
    $this->assertEquals('foo/bar.ps', $item0['file_name']);
    $this->assertEquals('baz/qux.ps', $item1['file_name']);
  }
}
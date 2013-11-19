<?php

class PrinterVerficationPagesControllerTest extends TestCase {
  public function setUp() {
    parent::setUp();

    $location = new Location();
    $location->id = -10;
    $location->description = '10th floor';
    $location->save();


    $location = new Location();
    $location->id = -20;
    $location->description = '20th floor';
    $location->save();

    $user = new User();
    $user->role = 'printer';
    $this->be($user);
  }

  public function tearDown() {
    Location::truncate();
    parent::tearDown();
  }

  public function testSuccessfullRequest() {
    $this->action('POST', 'PrinterVerficationPagesController@store', 
      array('pages' => array(
        array('location_id' => -10), 
        array('location_id' => -20))));

    $this->assertResponseStatus(201);

    $resp = json_decode($this->client->getResponse()->getContent(), true);

    $pt0 = $resp['pages'][0];
    $pt1 = $resp['pages'][1];

    $this->assertEquals('-10', $pt0['location_id']);
    $this->assertEquals('-20', $pt1['location_id']);

    $this->assertNotNull($pt0['file_name']);
    $this->assertNotNull($pt1['file_name']);
  }

  public function testSuccessfullRequestWithNoPages() {
    $this->action('POST', 'PrinterVerficationPagesController@store', 
      array('pages' => array()));

    $this->assertResponseStatus(201);

    $resp = json_decode($this->client->getResponse()->getContent(), true);

    $this->assertEquals(0, count($resp['pages']));
  }

  public function testSuccessfullRequestWithBadLocation() {
    $this->action('POST', 'PrinterVerficationPagesController@store', 
      array('pages' => array(array('location_id' => -999))));

    $this->assertResponseStatus(201);

    $resp = json_decode($this->client->getResponse()->getContent(), true);

    $this->assertEquals(0, count($resp['pages']));
  }
}
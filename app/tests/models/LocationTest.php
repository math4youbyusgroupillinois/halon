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
}
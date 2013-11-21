<?php
use Symfony\Component\HttpFoundation\File\UploadedFile;
class LocationsControllerTest extends TestCase {
  public function setUp() {
    parent::setUp();

    $user = new User();
    $user->role = 'admin';
    $this->be($user);
  }

  public function testImportSuccessful() {
    $file = new UploadedFile(__DIR__ .'/test-file.json', 'test-file.json', null, null, null, true);
    $res = $this->action('POST', 'LocationsController@import', array(), array(), array('file' => $file));
    $this->assertEquals(2, $res->getContent());
  }
}
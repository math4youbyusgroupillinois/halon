<?php
use Symfony\Component\HttpFoundation\File\UploadedFile;
class LocationsControllerTest extends TestCase {
  public function setUp() {
    parent::setUp();

    $user = new User();
    $user->role = 'admin';
    $this->be($user);
    Config::set('app.print_server_name', '/testserver/');
  }

  public function testImportSuccessful() {
    $res = $this->postFile();
    $this->assertEquals(2, $res->getContent());
    $location = Location::where('display_key', '=', 'CCU')->first();
    $this->assertEquals('CCU', $location->description);
    $this->assertEquals('/testserver/Printer1', $location->printer_name);
    $this->assertEquals('123-4567', $location->phone_number);
    $this->assertEquals('dt_mar1_CCU.dat', $location->todays_mar_file_name);
    $this->assertEquals('dt_mar2_CCU.dat', $location->tomorrows_mar_file_name);
  }

  public function testImportIgnoresDontPrintMar() {
    $res = $this->postFile();
    $this->assertEquals(2, $res->getContent());
  }

  public function testImportOverrideExistingLocation() {
    $location = new Location();
    $location->description = 'Testing';
    $location->display_key = 'CCU';
    $location->printer_name = 'old_printer';
    $location->save();

    $res = $this->postFile();
    $location = Location::where('display_key', '=', 'CCU')->first();
    $this->assertEquals('CCU', $location->description);
    $this->assertEquals('/testserver/Printer1', $location->printer_name);
    $this->assertEquals('123-4567', $location->phone_number);
    $this->assertEquals('dt_mar1_CCU.dat', $location->todays_mar_file_name);
    $this->assertEquals('dt_mar2_CCU.dat', $location->tomorrows_mar_file_name);
  }

  public function testImportFailsWithoutFile() {
    $res = $this->action('POST', 'LocationsController@import', array());
    $this->assertResponseStatus(400);
    $this->assertEquals('"Location import failed"', $res->getContent());
  }

  public function testImportFailsBadJson() {
    $file = new UploadedFile(__DIR__ .'/fixtures/bad-test-file.json', 'test-file.json', null, null, null, true);
    $res = $this->action('POST', 'LocationsController@import', array(), array(), array('file' => $file));
    $this->assertResponseStatus(400);
    $this->assertEquals('"Syntax error - bad JSON"', $res->getContent());
  }

  public function postFile() {
    $file = new UploadedFile(__DIR__ .'/fixtures/test-file.json', 'test-file.json', null, null, null, true);
    $res = $this->action('POST', 'LocationsController@import', array(), array(), array('file' => $file));
    return $res;
  }
}
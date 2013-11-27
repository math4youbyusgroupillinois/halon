<?php
use Symfony\Component\HttpFoundation\File\UploadedFile;
class LocationsControllerTest extends TestCase {
  public function setUp() {
    parent::setUp();

    $user = new User();
    $user->role = 'admin';
    $this->be($user);
    Config::set('app.print_server_name', '/testserver/');
    Config::set('app.import_file_path', __DIR__ .'/../fixtures/test-file.json');
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

  public function testImportFailsWithNoFile() {
    $file = '/fixtures/no-file.json';
    Config::set('app.import_file_path', $file);
    $res = $this->action('POST', 'LocationsController@import');
    $this->assertResponseStatus(400);
    $this->assertEquals('{"message":"\/fixtures\/no-file.json - File does not exist. Please check the file path"}', $res->getContent());
  }

  public function testImportFailsBadJson() {
    Config::set('app.import_file_path', __DIR__ .'/../fixtures/bad-test-file.json');
    $res = $this->action('POST', 'LocationsController@import');
    $this->assertResponseStatus(400);
    $this->assertEquals('{"message":"Syntax error - bad JSON"}', $res->getContent());
  }

  public function postFile() {
    $res = $this->action('POST', 'LocationsController@import');
    return $res;
  }
}
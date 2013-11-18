<?php

class FakeAdminController extends \SecuredController {
  protected $permitted = 'admin';

  public function index() {
    return Response::make("hello", 200);
  }  
}

class FakeLocationController extends \SecuredController {
  public function index() {
    return Response::make("hello", 200);
  }

  public function store() {
    if (!$this->isPermitted('printer')) {
      Log::info("Unauthorized access attempt", array('context' => get_class($this)."#store"));
      return $this->unauthorizedResponse();
    }

    return Response::make("hello", 200);
  }
}

class SecuredControllerTest extends TestCase {
  public function setUp() {
    parent::setUp();
    Route::resource('mr_secure', 'FakeAdminController');
    Route::resource('mr_partial_secure', 'FakeLocationController');
  }

  public function testClassPermitWithAuthorizedUser()
  {
    $admin = new User();
    $admin->role = 'admin';

    $this->be($admin);

    $response = $this->action('GET', 'FakeAdminController@index');
    $this->assertResponseStatus(200);
  }

  public function testClassPermitWithUnauthorizedUser()
  {
    $printer = new User();
    $printer->role = 'printer';

    $this->be($printer);

    $response = $this->action('GET', 'FakeAdminController@index');
    $this->assertResponseStatus(401);
  }

  public function testMethodPermitWithPublicResource()
  {
    $anon = new User();

    $this->be($anon);

    $response = $this->action('GET', 'FakeLocationController@index');
    $this->assertResponseStatus(200);
  }

  public function testMethodPermitWithAuthorizedUser()
  {
    $admin = new User();
    $admin->role = 'printer';

    $this->be($admin);

    $response = $this->action('POST', 'FakeLocationController@store');
    $this->assertResponseStatus(200);
  }

  public function testMethodPermitWithUnauthorizedUser()
  {
    $anon = new User();
   
    $this->be($anon);

    $response = $this->action('POST', 'FakeLocationController@store');
    $this->assertResponseStatus(401);
  }
}
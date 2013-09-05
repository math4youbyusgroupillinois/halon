<?php

class AuthenticationControllerTest extends TestCase {

  public function testLogoutReturnsSuccess()
  {
    $response = $this->action('GET', 'AuthenticationController@index');
    $this->assertResponseStatus(200);
  }

  public function testAuthenticateRoute() {
    $response = $this->call('GET', 'service/authenticate');
    $this->assertResponseStatus(200);
  }

}
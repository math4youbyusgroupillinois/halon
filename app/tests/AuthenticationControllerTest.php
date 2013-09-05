<?php

class AuthenticationControllerTest extends TestCase {

  public function testLogoutReturnsSuccess()
  {
    $response = $this->action('GET', 'AuthenticationController@index');
    $this->assertResponseStatus(200);
  }

}
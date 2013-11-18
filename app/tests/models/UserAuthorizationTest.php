<?php

class UserAuthorizationTest extends TestCase {
  protected $user;
  protected $credentials;

  public function setUp() {
    parent::setUp();
    $this->user = new User();
    $this->user->role = 'admin';
    $this->user->password = Hash::make('admin');
    $this->user->save();

    $this->credentials = array(
      'role' => 'admin',
      'password' =>  'admin'
    );
  }

  public function testPermitSuccess() {
    Auth::attempt($this->credentials);

    $this->assertTrue(UserAuthorization::permit('admin'));
  }

  public function testPermitFailure() {
    Auth::attempt($this->credentials);

    $this->assertFalse(UserAuthorization::permit('foo'));
  }

  public function testPermitFailureWithNoUser() {
    $this->assertFalse(UserAuthorization::permit('foo'));
  }

  public function testPermitSuccessWithArray() {
    Auth::attempt($this->credentials);

    $this->assertTrue(UserAuthorization::permit(array('admin')));
  }

  public function testPermitFailureWithArray() {
    Auth::attempt($this->credentials);

    $this->assertFalse(UserAuthorization::permit(array('foo')));
  }

  public function testPermitFailureWithNoUserWithArray() {
    $this->assertFalse(UserAuthorization::permit(array('foo')));
  }
}

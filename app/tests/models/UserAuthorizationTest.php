<?php

class UserAuthorizationTest extends TestCase {
  public function testPermitSuccess() {
    $user = new User();
    $user->role = 'admin';
    $user->password = Hash::make('admin');
    $user->save();

    $credentials = array(
      'role' => 'admin',
      'password' =>  'admin'
    );

    Auth::attempt($credentials);

    $this->assertTrue(UserAuthorization::permit('admin'));
  }

  public function testPermitFailure() {
    $user = new User();
    $user->role = 'admin';
    $user->password = Hash::make('admin');
    $user->save();

    $credentials = array(
      'role' => 'admin',
      'password' =>  'admin'
    );

    Auth::attempt($credentials);

    $this->assertFalse(UserAuthorization::permit('foo'));
  }

  public function testPermitFailureWithNoUser() {
    $this->assertFalse(UserAuthorization::permit('foo'));
  }
}

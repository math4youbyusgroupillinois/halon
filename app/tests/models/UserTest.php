<?php

class UserTest extends TestCase {

  public function testUserRole()
  {
    $user = new User();
    $user->role = 'admin';
    $user->password = Hash::make('admin');
    $user->save();
    $found = User::where('role', '=', 'admin')->first();
    $this->assertEquals('admin', $found->role);
  }

  public function testPasswordHashing()
  {
    $user = new User();
    $user->role = 'admin';
    $user->password = Hash::make('admin');
    $user->save();
    $this->assertTrue(Hash::check('admin', $user->password));
  }

}
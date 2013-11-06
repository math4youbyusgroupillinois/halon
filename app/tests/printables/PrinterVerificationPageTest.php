<?php

class PrinterVerificationPageTest extends TestCase {

  public static $page;

  public function setUp() {
    parent::setUp();

    $date    = '11/05/2013 15:22:33';
    $unit    = 'best nurse unit';
    $printer = 'best printer';

    self::$page = new PrinterVerificationPage($date, $unit, $printer);    
  }

  public function testContent() {
    $actual = self::$page->content();

    $this->assertEquals(1, substr_count($actual, '11/05/2013 15:22:33'));
    $this->assertEquals(1, substr_count($actual, 'best nurse unit'));
    $this->assertEquals(1, substr_count($actual, 'best printer'));
  }

  public function testIdentifier() {
    $this->assertNotNull(self::$page->identifier());
  }
}
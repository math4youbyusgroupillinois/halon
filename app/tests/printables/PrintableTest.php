<?php

class FakeHasher {
  public function hash() { return 'fake_page.txt'; }
}

class FakePage extends Printable {
  public function identifier() {
    return "foo";
  }

  public function content() {
    return "hello world";
  }
}

class PrintableTest extends TestCase {
  public static $fakePage;
  public static $baseDir;

  public function setUp() {
    parent::setUp();

    self::$baseDir = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'tmp';
    $hasher  = new FakeHasher();

    self::$fakePage = new FakePage(self::$baseDir, $hasher);
  }

  public function tearDown() {
    parent::tearDown();

    File::deleteDirectory(self::$baseDir);
  }

  public function testPrintableHasIdentifier() {
    $this->assertEquals('foo', self::$fakePage->identifier());
  }

  public function testPrintableHasContent() {
    $this->assertEquals('hello world', self::$fakePage->content());
  }

  public function testPrintableWasWritten() {
    self::$fakePage->write();
    $this->assertTrue(File::exists(self::$baseDir . DIRECTORY_SEPARATOR . 'fake_page.txt'));
  }
}
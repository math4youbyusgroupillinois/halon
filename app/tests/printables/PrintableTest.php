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
  public static $hasher;

  public function setUp() {
    parent::setUp();

    self::$baseDir = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'tmp';
    self::$hasher  = new FakeHasher();

    self::$fakePage = new FakePage(self::$baseDir, self::$hasher);
  }

  public function tearDown() {
    parent::tearDown();

    File::deleteDirectory(self::$baseDir);
  }

  public function testIdentifier() {
    $this->assertEquals('foo', self::$fakePage->identifier());
  }

  public function testContent() {
    $this->assertEquals('hello world', self::$fakePage->content());
  }

  public function testWrite() {
    self::$fakePage->write();
    $this->assertTrue(File::exists(self::$baseDir . DIRECTORY_SEPARATOR . 'fake_page.txt'));
  }

  public function testFileName() {
    $this->assertEquals('fake_page.txt', self::$fakePage->fileName());
  }

  public function testFilePathWithDefaultBasePath() {
    $page = new FakePage(NULL, self::$hasher);
    $expected = storage_path(). DIRECTORY_SEPARATOR . 'printables' . DIRECTORY_SEPARATOR . 'fake_page.txt';
    $this->assertEquals($expected, $page->filePath());
  }
}
<?php

class PrinterVerificationPageTest extends TestCase {

  public function testCreatePrinterVerificationPage() {
    //$date = new DateTime("America/Chicago");
    //$formatted_date = $date->format('m/d/Y H:i:s');

    $date    = '11/05/2013 15:22:33';
    $unit    = 'best nurse unit';
    $printer = 'best printer';

    $page = new PrinterVerificationPage($date, $unit, $printer);
    $actual = $page->content();

    $this->assertEquals(1, substr_count($actual, '11/05/2013 15:22:33'));
    $this->assertEquals(1, substr_count($actual, 'best nurse unit'));
    $this->assertEquals(1, substr_count($actual, 'best printer'));
  }
}
<?php namespace Northwestern\Printer;

/* Constants needed for printer methods */
define("PRINTER_MODE", "0");

/* Global variables for controlling printer stub methods */
$printerOpenReturns = false;
$printerSetOptionReturns = false;
$printerWriteReturns = false;
$printerCloseReturns = false;

/* Stubbed printer methods */
function printer_open($printerName) {
  global $printerOpenReturns;
  return $printerOpenReturns;
}

function printer_set_option($handle, $key, $value) {
  global $printerSetOptionReturns;
  return $printerSetOptionReturns;
}

function printer_write($handle, $contents) {
  global $printerWriteReturns;
  return $printerWriteReturns;
}

function printer_close($handle) {
  global $printerCloseReturns;
  return $printerCloseReturns;
}


class PhpWindowsDriverTest extends \TestCase {

  public function setPrintMethodReturnValues($opts) {
    global $printerOpenReturns, $printerSetOptionReturns, $printerCloseReturns, $printerWriteReturns;
    $printerOpenReturns = $opts['open'];
    $printerSetOptionReturns = $opts['set'];
    $printerWriteReturns = $opts['write'];
    $printerCloseReturns = $opts['close'];
  }

  public function testEnqueReturnsTrueOnSuccess() {
    $this->setPrintMethodReturnValues(array(
      'open'  => true, 
      'set'   => true,
      'write' => true, 
      'close' => true));
    $driver = new PhpWindowsPrinterDriver();
    $result = $driver->enque('foo', dirname(__FILE__).'/valid.txt');
    $this->assertTrue($result);
  }

  /**
   * @expectedException Northwestern\Printer\PrinterException
   */
  public function testEnqueReturnsFalseOnOpenPrinterFailure() {
    $this->setPrintMethodReturnValues(array(
      'open'  => false, 
      'set'   => true,
      'write' => true, 
      'close' => true));
    $driver = new PhpWindowsPrinterDriver();
    $result = $driver->enque('foo', dirname(__FILE__).'/valid.txt');
    $this->fail('Expected a PrinterException to be thrown');
  }

  /**
   * @expectedException Northwestern\Printer\PrinterException
   */
  public function testEnqueReturnsFalseOnSetPrinterOptionsFailure() {
    $this->setPrintMethodReturnValues(array(
      'open'  => true, 
      'set'   => false,
      'write' => true, 
      'close' => true));
    $driver = new PhpWindowsPrinterDriver();
    $result = $driver->enque('foo', dirname(__FILE__).'/valid.txt');
    $this->fail('Expected a PrinterException to be thrown');
  }

  /**
   * @expectedException Northwestern\Printer\PrinterException
   */
  public function testEnqueReturnsFalseOnWritePrinterFailure() {
    $this->setPrintMethodReturnValues(array(
      'open'  => true, 
      'set'   => true,
      'write' => false, 
      'close' => true));
    $driver = new PhpWindowsPrinterDriver();
    $result = $driver->enque('foo', dirname(__FILE__).'/valid.txt');
    $this->fail('Expected a PrinterException to be thrown');
  }

  public function testEnqueReturnsTrueOnClosePrinterFailure() {
    $this->setPrintMethodReturnValues(array(
      'open'  => true, 
      'set'   => true,
      'write' => true, 
      'close' => false));
    $driver = new PhpWindowsPrinterDriver();
    $result = $driver->enque('foo', dirname(__FILE__).'/valid.txt');
    $this->assertTrue($result);
  }

  /**
   * @expectedException Northwestern\Printer\PrinterException
   */
  public function testEnqueReturnsFalseOnReadFailure() {
    $this->setPrintMethodReturnValues(array(
      'open'  => true, 
      'set'   => true,
      'write' => true, 
      'close' => true));
    $driver = new PhpWindowsPrinterDriver();
    $result = $driver->enque('foo', dirname(__FILE__).'/invalid.txt');
    $this->fail('Expected a PrinterException to be thrown');
  }

}
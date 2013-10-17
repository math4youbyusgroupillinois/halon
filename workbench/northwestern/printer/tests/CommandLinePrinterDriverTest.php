<?php namespace Northwestern\Printer;

class CommandLinePrinterDriverTest extends \TestCase {

  public function testGenerateCommand() {
    $driver = new CommandLinePrinterDriver();
    $result = $driver->generateCommand('hp printer', 'valid.txt');
    $this->assertEquals('..\workbench\northwestern\printer\src\Northwestern\Printer\Printer.exe "hp printer" "valid.txt" 2>&1', $result);
  }
}   
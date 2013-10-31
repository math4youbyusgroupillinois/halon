<?php namespace Northwestern\Printer;

class CommandLinePrinterDriverTest extends \TestCase {

  public function testGenerateCommand() {
    $driver = new CommandLinePrinterDriver();
    $result = $driver->generateCommand('hp printer', 'valid.txt');
    $this->assertEquals('..\workbench\northwestern\printer\src\Northwestern\Printer\Printer.exe "hp printer" "valid.txt"', $result);
  }

  public function testGenerateCommandWithNetworkPrinter() {
    $driver = new CommandLinePrinterDriver();
    $result = $driver->generateCommand('\\\\foo\bar', 'valid.txt');
    $this->assertEquals('..\workbench\northwestern\printer\src\Northwestern\Printer\Printer.exe "\\\\foo\bar" "valid.txt"', $result);
  }

  public function testGenerateCommandWithNetworkFile() {
    $driver = new CommandLinePrinterDriver();
    $result = $driver->generateCommand('hp printer', '\\\\foo\bar');
    $this->assertEquals('..\workbench\northwestern\printer\src\Northwestern\Printer\Printer.exe "hp printer" "\\\\foo\bar"', $result);
  }

  public function testGenerateCommandWithEvilCommand() {
    $driver = new CommandLinePrinterDriver();
    $result = $driver->generateCommand('" & format c: & "', 'valid.txt');
    $this->assertEquals('..\workbench\northwestern\printer\src\Northwestern\Printer\Printer.exe " & format c: & " "valid.txt"', $result);    
  }

  public function testGenerateCommandWithEvilCommandToo() {
    $driver = new CommandLinePrinterDriver();
    $result = $driver->generateCommand("' & format c: & '", 'valid.txt');
    $this->assertEquals('..\workbench\northwestern\printer\src\Northwestern\Printer\Printer.exe " & format c: & " "valid.txt"', $result);    
  }
}   
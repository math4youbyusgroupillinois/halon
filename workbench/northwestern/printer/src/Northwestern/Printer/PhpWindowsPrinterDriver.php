<?php namespace Northwestern\Printer;

class Printer {
  public $name;
  public $handle;

  function __construct($name, $handle) {
    $this->name = $name;
    $this->handle = $handle;
  }
}

class File {
  public $path;
  public $contents;

  function __construct($path, $contents) {
    $this->path = $path;
    $this->contents = $contents;
  }
}

class PhpWindowsPrinterDriver {
  /**
   * Enque a print job in the print queue.
   *
   * @var string
   * @var string
   * @throws PrinterException
   */
  public function enque($printerName, $filePath) {
    $print  = $this->openPrinter($printerName);
    $file   = $this->readFile($filePath);
    $result = $this->printFile($print, $file);
    $this->closePrinter($print);

    return $result;   
  }

  private function openPrinter($printerName) {
    $handle = FALSE;
    try {
      // printer_open: Returns a printer handle on success or FALSE on failure. 
      $handle = @printer_open($printerName);
    } catch (Exception $e) {
      throw new PrinterException("An error occurred when opening the printer '".$printerName."'", 0, $e);
    }

    if ($handle === FALSE) {
      throw new PrinterException("An error occurred when opening the printer '".$printerName."'", 0);
    }
    
    $opts_result = FALSE;
    try {
      // printer_set_option: Returns TRUE on success or FALSE on failure.
      $opts_result = @printer_set_option($handle, PRINTER_MODE, "raw");  
    } catch (Exception $e) {
      throw new PrinterException("An error occurred when setting the mode to raw on printer '".$printerName."'", 0, $e);
    }
    
    if (!($opts_result === TRUE)) {
      throw new PrinterException("An error occurred when setting the mode to raw on printer '".$printerName."'", 0);
    }

    return new Printer($printerName, $handle);
  }

  private function readFile($filePath) {
    $file = FALSE;
    try {
      // file_get_contents: Returns the read data or FALSE on failure.
      //                    Throws ErrorException on no such file.
      // throw new \Exception("Error Processing Request", 1);
      
      $file = @file_get_contents($filePath, "r");
    } catch (\Exception $e) {
      print 'hello';
      throw new PrinterException("An error occurred when reading the file '".$filePath."'", 0, $e);
    }

    if ($file === FALSE) {
      throw new PrinterException("An error occurred when reading the file '".$filePath."'", 0);
    }

    return new File($filePath, $file);
  }

  private function printFile($printer, $file) {
    $result = FALSE;
    try {
      // printer_write: Returns TRUE on success or FALSE on failure. 
      $result = @printer_write($printer->handle, $file->contents);
    } catch (Exception $e) {
      throw new PrinterExceptions("An error occurred when printing the file '".$file->path."' to printer '".$printer->name."'", 0, $e);
    }

    if ($result === FALSE) {
      throw new PrinterException("An error occurred when printing the file '".$file->path."' to printer '".$printer->name."'", 0);
    }

    return $result;
  }

  private function closePrinter($printer) {
    try {
      @printer_close($printer->handle);
    } catch (Exception $e) {
      Log::error(new PrinterException("An error occurred when closing the printer '".$printer->name."'", 0, $e));
    }
  }
}
<?php namespace Northwestern\Printer;

class PhpWindowsPrinterDriver {
  public function enque($printerName, $filePath) {
    $handle = printer_open($printerName);
    printer_set_option($handle, PRINTER_MODE, "raw");

    $file = file_get_contents($filePath, "r");
    printer_write($handle, $file);

    printer_close($handle);
  }
}
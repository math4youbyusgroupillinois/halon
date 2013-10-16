<?php namespace Northwestern\Printer;

class CommandLinePrinterDriver implements PrinterDriverInterface {
  /**
   * Enque a print job in the print queue.
   *
   * @var string
   * @var string
   * @throws PrinterException
   */
  public function enque($printerName, $filePath) {
    $command = $this->generateCommand($printerName, $filePath);
    $pwd = shell_exec("cd");
    \Log::info("Current directory: $pwd");
    \Log::info("Print Command: $command");
    $output = shell_exec($command);
    \Log::info("Print Output: $output");
    if (!preg_match("/^Success/", $output)) {
      throw new PrinterException($output);
    }
    return true;
  }

  public function generateCommand($printerName, $filePath) {
    # path is relative to the public directory
    $path = '..\\workbench\\northwestern\\printer\\src\\Northwestern\Printer';
    return "$path\\Printer.exe \"$printerName\" \"$filePath\" 2>&1";
  }
}
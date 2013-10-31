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
    \Log::info("Print Command: $command");

    $output = $this->run($command);

    \Log::info("Print Output: $output");

    if (!preg_match("/^Success/", $output)) {
      throw new PrinterException($output);
    }
    
    return true;
  }

  public function generateCommand($printerName, $filePath) {
    // path is relative to the public directory
    $path = '..\workbench\northwestern\printer\src\Northwestern\Printer';
    $santizedPrinterName = $this->sanitizeArg($printerName);
    $santizedFilePath    = $this->sanitizeArg($filePath);
    return "$path\Printer.exe \"$santizedPrinterName\" \"$santizedFilePath\"";
  }

  public function sanitizeArg($arg) {
    $bad = array('"', "'", '^');
    return str_replace($bad, '', $arg);
  }

  public function run($command) {
    $descriptorspec = array(
      0 => array('pipe', 'r'), // stdin
      1 => array('pipe', 'w'), // stdout
      2 => array('pipe', 'a') // stderr
    );

    $env = array();
    $other_options = array('bypass_shell' => TRUE);
    
    $process = proc_open($command, $descriptorspec, $pipes, NULL, NULL, $other_options);

    if (is_resource($process)) {
      fclose($pipes[0]);

      $output = stream_get_contents($pipes[1]);
      fclose($pipes[1]);

      // $output += stream_get_contents($pipes[2]);
      fclose($pipes[2]);

      // It is important that you close any pipes before calling
      // proc_close in order to avoid a deadlock
      proc_close($process);

      return $output;
    }
  }
}
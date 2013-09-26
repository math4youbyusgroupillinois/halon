<?php namespace Northwestern\Printer;

/**
 * @param String
 * @param String
 * @return TRUE on printing success or FALSE on printing failure
 * @throws PrinterException on exceptional condition
 */
interface PrinterDriverInterface {
    public function enque($printerName, $filePath);
}
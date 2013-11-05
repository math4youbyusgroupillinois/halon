<?php
  
class PrinterVerificationPage {
  public function generate($date, $unit, $printer) {
    $data = array(
      'date_printed' => $date,
      'nurse_unit'   => $unit,
      'printer_name' => $printer
    );

    return View::make('postscripts/NMH_RPT_MAR_TEST_PAGE', $data)->render();
  }
  
}
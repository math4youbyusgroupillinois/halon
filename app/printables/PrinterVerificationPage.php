<?php
  
class PrinterVerificationPage extends Printable {
  public function __construct($date, $unit, $printer) {
    parent::__construct();
    $this->date = $date;
    $this->unit = $unit;
    $this->printer = $printer;
  }

  public function identifier() {
    return $unit.$printer;
  }

  public function content() {
    $data = array(
      'date_printed' => $this->date,
      'nurse_unit'   => $this->unit,
      'printer_name' => $this->printer
    );

    return View::make('postscripts/NMH_RPT_MAR_TEST_PAGE', $data)->render();
  }
}
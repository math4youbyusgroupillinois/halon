<?php
  try
  {
    print "\n1 - Reset 'Admin' password.";
    print "\n2 - Reset 'Printer' password.";
    print "\n3 - Exit.";
    print "\nPlease enter you choice:";
    $stdin = fopen('php://stdin', 'r');
    $choice = trim(fgetc($stdin));
    if ($choice == 1 or $choice == 2) {
      $db_path = __DIR__.'/app/database/production.sqlite';
      $db = new PDO('sqlite:'.$db_path);
      if (trim($choice) == 1) {
        $pwd = "admin";
        $cpwd = crypt("admin");
        $role = "admin";
      } else if (trim($choice) == 2) {
        $pwd = "printer";
        $cpwd = crypt("printer");
        $role = "printer";
      }
      $date = new DateTime("America/Chicago");
      $updated_at = $date->format('Y-m-d H:i:s');
      $q = $db->prepare('UPDATE users SET password = ?, updated_at = ? WHERE role = ?');
      $update = $q->execute(array($cpwd, $updated_at, $role));
      if ($update) {
        print "\n".$role." user password successfully reset to the '".$pwd."'\n";
        $db = NULL;
      } else {
        print "\nPassword update fails.Please try again.";
      }
    } else {
      print "\nExiting.";
      exit;
    }
  }
  catch(PDOException $e)
  {
    print 'Exception : '.$e->getMessage();
  }
?>
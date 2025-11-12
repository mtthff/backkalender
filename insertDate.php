<?php

session_start();

require_once("dbh.class.php");

$connection = new Dbh();

// Funktion zum Schreiben in die Log-Datei
function writeDoerrenLog($name, $email, $backtermin) {
  $logFile = "doerren_log.txt";
  $currentDateTime = date("Y-m-d H:i:s");
  $logEntry = sprintf(
    "[%s] Buchung für Termin: %s | Name: %s | E-Mail: %s\n",
    $currentDateTime,
    $backtermin,
    $name,
    $email
  );
  
  // Schreibe in Log-Datei (append mode)
  file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}

if ( isset($_POST["submitBookingData"]) ) {
  $backgruppe = $_POST["InputBackgruppe"];
  $password = $_POST["InputPassword"];
  $requestedDate = $_POST["requestedDate"];
  $requestedSlot = $_POST["timeslot"];

  // lese aus dem angefragten datum wieder tag, monat und jahr
  $day = date("d", strtotime($requestedDate));
  $year = date("Y", strtotime($requestedDate));
  $month = date("m", strtotime($requestedDate));

  // var_dump($year);
  // var_dump($month);
  // var_dump($backgruppe);

  // pruefe ob backgruppe gewaehlt
  if ( $backgruppe == "0" ) {
    // keine Backgruppe gewaehlt
    echo "<script> alert('Fehler: Bitte Backgruppe w&auml;hlen'); window.location.href = 'index.php?month=" . $month . "&year=" . $year . "&msg=failBackgruppe'; </script>";
  } else {

    // lese Passwort der Backgruppe aus Datenbank
    $sql = "SELECT passwort FROM backgruppen WHERE backgruppeName = ?";
    $stmt = $connection->connect()->prepare($sql);
    $stmt->execute( [$backgruppe] );

    if ( $result = $stmt->fetchAll() ) {
      $passwordFromDb = $result[0]["passwort"];

      // pruefe ob passwort richtig eingegeben wurde
      if ( $password==$passwordFromDb ) {

        // schreibe PW und Gruppe in session-cookie
        $_SESSION["password"] = $password;
        $_SESSION["backgruppe"] = $backgruppe;

        // lese backgruppen type
        $sql = "SELECT type FROM backgruppen WHERE backgruppeName = ?";
        $stmt = $connection->connect()->prepare($sql);
        $stmt->execute( [$backgruppe] );
        $result = $stmt->fetchAll();
        $backkgruppenType = $result[0]["type"];

        // wenn buchung vor dem stichtag oder backgruppentyp nicht "vorstand" dann Fehler
        // stichtag ist 13 Monate in der Vergangenheit
        if ( $month==1) {
          $dateToCompare = strval(intval($year)-2) . "-12-" . $day;
        } else {
          $dateToCompare = strval(intval($year)-1) . "-" . sprintf("%'.02d", intval($month) - 1) . "-" . $day;
        }

        $today = date("Y-m-d");
        if ( $today<$dateToCompare and $backkgruppenType!="vorstand" ) {
          echo "<script> alert('Fehler: Dieser Termin kann erst ab " . $dateToCompare . " gebucht werden.'); window.location.href = 'index_local.php?month=" . $month . "&year=" . $year . "&msg=failToEarly'; </script>";
          exit();
        }

        // pruefe ob termin bereits gebucht wurde
        $sql = "SELECT backtermin FROM backtermine WHERE storniert!='ja' AND slot = ?";
        $stmt = $connection->connect()->prepare($sql);
        $stmt->execute( [$requestedSlot] );

        if ($result = $stmt->fetchAll()) {
          foreach ( $result as $row ) {
            $bookings[] = $row["backtermin"];
          }
        }

        if ( !in_array($requestedDate, $bookings) ) {
          // Termin ist noch frei und wird gebucht

          // Wenn Backgruppe "Dörren" ist, schreibe in Log-Datei
          if ( $backgruppe === "Dörren" && isset($_POST["doerren_name"]) && isset($_POST["doerren_email"]) ) {
            writeDoerrenLog($_POST["doerren_name"], $_POST["doerren_email"], $requestedDate);
          }

          $newQuery = "INSERT INTO backtermine (id,backgruppeName,backtermin,storniert,slot) VALUES (NULL,:backgruppe,:requestedDate,'nein',:slot)";
          $newStmt = $connection->connect()->prepare($newQuery);
          $newStmt->execute( array("backgruppe" => $backgruppe, "requestedDate" => $requestedDate, "slot" => $requestedSlot) );
          if ($newStmt) {
            echo "<script> alert('Backtermin gespeichert'); window.location.href = 'index.php?month=" . $month . "&year=" . $year . "&msg=successInsert'; </script>";
          }

        } else {
          // Termin ist bereits gebucht
          echo "<script> alert('Fehler: Termin ist bereits gebucht'); window.location.href = 'index.php?month=" . $month . "&year=" . $year . "&msg=failInsert'; </script>";
        }

      } else {
        // Passwort wurde falsch eingegeben
        echo "<script> alert('Fehler: falsches Passwort'); window.location.href = 'index.php?month=" . $month . "&year=" . $year . "&msg=failPW'; </script>";
      }
    }
  }

}

?>

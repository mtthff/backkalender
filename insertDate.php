<?php

session_start();

require_once("dbh.class.php");

$connection = new Dbh();

function redirectToCalendar($month, $year, $msg) {
  header("Location: index.php?month=" . $month . "&year=" . $year . "&msg=" . $msg);
  exit();
}

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
  $backgruppe = isset($_POST["InputBackgruppe"]) ? trim($_POST["InputBackgruppe"]) : "0";
  $password = isset($_POST["InputPassword"]) ? $_POST["InputPassword"] : "";
  $requestedDate = isset($_POST["requestedDate"]) ? $_POST["requestedDate"] : "";
  $requestedSlot = isset($_POST["timeslot"]) ? $_POST["timeslot"] : "";

  // lese aus dem angefragten datum wieder tag, monat und jahr
  $requestedTimestamp = strtotime($requestedDate);
  if ( $requestedTimestamp === false ) {
    $day = date("d");
    $year = date("Y");
    $month = date("m");
  } else {
    $day = date("d", $requestedTimestamp);
    $year = date("Y", $requestedTimestamp);
    $month = date("m", $requestedTimestamp);
  }

  if ( $requestedDate === "" || $requestedSlot === "" ) {
    redirectToCalendar($month, $year, "failInsert");
  }

  // var_dump($year);
  // var_dump($month);
  // var_dump($backgruppe);
  // pruefe ob backgruppe gewaehlt
  if ( $backgruppe == "0" ) {
    // keine Backgruppe gewaehlt
    redirectToCalendar($month, $year, "failBackgruppe");
  } else {

    // lese Passwort der Backgruppe aus Datenbank
    $sql = "SELECT id, passwort, type FROM backgruppen WHERE backgruppeName = ?";
    $stmt = $connection->connect()->prepare($sql);
    $stmt->execute( [$backgruppe] );

    if ( $result = $stmt->fetchAll() ) {
      $backgruppeId = $result[0]["id"];
      $passwordFromDb = $result[0]["passwort"]; 
      $backkgruppenType = $result[0]["type"]; 

      // Sonntags duerfen nur die Gruppen mit ID 41, 39 und 25 buchen.
      if ( date('w', strtotime($requestedDate)) == 0 && !in_array(intval($backgruppeId), array(41, 39, 25), true) ) {
        redirectToCalendar($month, $year, "failSundayGroup");
      }

      $isHashedPw = preg_match('/^\$2[aby]\$|^\$argon2/i', $passwordFromDb) === 1;
      $passwordOk = $isHashedPw ? password_verify($password, $passwordFromDb) : ($password == $passwordFromDb);

      // pruefe ob passwort richtig eingegeben wurde
      if ( $passwordOk ) {

        // schreibe PW und Gruppe in session-cookie
        $_SESSION["password"] = $password;
        $_SESSION["backgruppe"] = $backgruppe;
        // wenn buchung vor dem stichtag oder backgruppentyp nicht "vorstand" dann Fehler
        // stichtag ist 13 Monate in der Vergangenheit
        if ( $month==1) {
          $dateToCompare = strval(intval($year)-2) . "-12-" . $day;
        } else {
          $dateToCompare = strval(intval($year)-1) . "-" . sprintf("%'.02d", intval($month) - 1) . "-" . $day;
        }

        $today = date("Y-m-d");
        if ( $today<$dateToCompare and $backkgruppenType!="vorstand" ) {
          redirectToCalendar($month, $year, "failToEarly");
        }

        // pruefe ob termin bereits gebucht wurde
        $sql = "SELECT 1 FROM backtermine WHERE backtermin = ? AND storniert!='ja' AND slot = ? LIMIT 1";
        $stmt = $connection->connect()->prepare($sql);
        $stmt->execute( [$requestedDate, $requestedSlot] );

        if ( !$stmt->fetchColumn() ) {
          // Termin ist noch frei und wird gebucht

          // Wenn Backgruppe "Dörren" ist, schreibe in Log-Datei
          if ( $backgruppe === "Dörren" && isset($_POST["doerren_name"]) && isset($_POST["doerren_email"]) ) {
            writeDoerrenLog($_POST["doerren_name"], $_POST["doerren_email"], $requestedDate);
          }

          $newQuery = "INSERT INTO backtermine (id,backgruppeName,backtermin,storniert,slot) VALUES (NULL,:backgruppe,:requestedDate,'nein',:slot)";
          $newStmt = $connection->connect()->prepare($newQuery);
          $newStmt->execute( array("backgruppe" => $backgruppe, "requestedDate" => $requestedDate, "slot" => $requestedSlot) );
          if ($newStmt) {
            redirectToCalendar($month, $year, "successInsert");
          }

          redirectToCalendar($month, $year, "failInsert");

        } else {
          // Termin ist bereits gebucht
          redirectToCalendar($month, $year, "failInsert");
        }

      } else {
        // Passwort wurde falsch eingegeben
        redirectToCalendar($month, $year, "failPW");
      }

    } else {
      redirectToCalendar($month, $year, "failBackgruppe");
    }
  }

} else {
  header("Location: index.php");
  exit();
}

?>

<?php

session_start();

require_once("dbh.class.php"); 

$connection = new Dbh();

function redirectToCalendar($month, $year, $msg) {
  header("Location: index.php?month=" . $month . "&year=" . $year . "&msg=" . $msg);
  exit();
}

if ( isset($_POST["submitBookingData"]) ) {
  $backgruppe = isset($_POST["InputBackgruppe"]) ? trim($_POST["InputBackgruppe"]) : "";
  $password = isset($_POST["InputPassword"]) ? $_POST["InputPassword"] : "";
  $requestedDate = isset($_POST["requestedDate"]) ? $_POST["requestedDate"] : "";
  $requestedSlot = isset($_POST["timeslot"]) ? $_POST["timeslot"] : "";

  // lese aus dem angefragten datum wieder monat und jahr
  $requestedTimestamp = strtotime($requestedDate);
  if ( $requestedTimestamp === false ) {
    $year = date("Y");
    $month = date("m");
  } else {
    $year = date("Y", $requestedTimestamp);
    $month = date("m", $requestedTimestamp);
  }

  if ( $backgruppe === "" || $requestedDate === "" || $requestedSlot === "" ) {
    redirectToCalendar($month, $year, "failBackgruppe");
  }

  // lese Passwort der Backgruppe aus Datenbank
  $sql = "SELECT passwort FROM backgruppen WHERE backgruppeName = ? LIMIT 1";
  $stmt = $connection->connect()->prepare($sql);
  $stmt->execute( [$backgruppe] );
  $result = $stmt->fetchAll();

  if ( $result ) {
    $passwordFromDb = $result[0]["passwort"]; 
    $isHashedPw = preg_match('/^\$2[aby]\$|^\$argon2/i', $passwordFromDb) === 1;
    $passwordOk = $isHashedPw ? password_verify($password, $passwordFromDb) : ($password == $passwordFromDb);

    // pruefe ob passwort richtig eingegeben wurde
    if ( $passwordOk ) {

      // schreibe PW und Gruppe in session-cookie
      $_SESSION["password"] = $password;
      $_SESSION["backgruppe"] = $backgruppe;

      // beim angefragten backtermin "storniert" auf Wert 1 setzen
      $newQuery = "UPDATE backtermine SET storniert = 'ja' WHERE backtermin = :requestedDate AND slot = :slot AND backgruppeName = :backgruppe AND storniert != 'ja'";
      $newStmt = $connection->connect()->prepare($newQuery);

      if ( $newStmt->execute( array("requestedDate" => $requestedDate, "slot" => $requestedSlot, "backgruppe" => $backgruppe) ) ) {

        if ( $newStmt->rowCount() < 1 ) {
          redirectToCalendar($month, $year, "failDelete");
        }

        $currentDate = new DateTime();
        $requestedDateTime = DateTime::createFromFormat('Y-m-d', $requestedDate);
        $requestedDateFormatted = $requestedDateTime ? $requestedDateTime->format('d.m.Y') : $requestedDate;

        if ( $requestedDateTime ) {
          $interval = $currentDate->diff($requestedDateTime);

          if ($interval->days < 42) { // 6 weeks * 7 days = 42 days
            
              // Mail an Kontakt@...
              $to = "kontakt@backhaus-heumaden.de";
              // $to = "mtthff@gmail.com, olaf.fischer@gmx.de";
              $subject = "Termin storniert";
              $message = "Die Backgruppe $backgruppe hat soeben den Backtermin $requestedDateFormatted mit dem Slot \"$requestedSlot\" storniert.";
              $headers = "From: no-reply@backhaus-heumaden.de";
              mail($to, $subject, $message, $headers);

              // Mail an Backgruppenleiter:innen
              $to2 = "bgleiter@backhaus-heumaden.de";
              // $to2 = "mtthff@gmail.com, matthias.hoffmann@stjg.de, hwr@pilhuhn.de";
              $subject2 = "[Backhaus] Backtermin wurde storniert";
              $message2 = "Hallo zusammen,\n\n";
              $message2 .= "der Backtermin am $requestedDateFormatted mit dem Slot \"$requestedSlot\" wurde soeben storniert.";
              $message2 .= "\n\nTragt euch also gerne im Backkalender dort ein: https://backhaus-heumaden.de/kalender/?month=". $month."&year=". $year;
              $headers2 = "From: no-reply@backhaus-heumaden.de";
              mail($to2, $subject2, $message2, $headers2);
          }
        }

        redirectToCalendar($month, $year, "successDelete");
      }

      redirectToCalendar($month, $year, "failDelete");

    } else {
      redirectToCalendar($month, $year, "failPW");
    }

  } else {
    redirectToCalendar($month, $year, "failBackgruppe");
  }

} else {
  header("Location: index.php");
  exit();
}

?>

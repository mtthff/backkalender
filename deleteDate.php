<?php

require_once("dbh.class.php"); 

$connection = new Dbh();

if ( isset($_POST["submitBookingData"]) ) {
  $backgruppe = $_POST["InputBackgruppe"];
  $password = $_POST["InputPassword"];
  $requestedDate = $_POST["requestedDate"];
  $requestedSlot = $_POST["timeslot"];

  // lese aus dem angefragten datum wieder monat und jahr
  $year = date("Y", strtotime($requestedDate));
  $month = date("m", strtotime($requestedDate));

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

      // beim angefragten backtermin "storniert" auf Wert 1 setzen
      $newQuery = "UPDATE backtermine SET storniert = 'ja' WHERE backtermin = :requestedDate AND slot = :slot";
      $newStmt = $connection->connect()->prepare($newQuery);

      if ( $newStmt->execute( array("requestedDate" => $requestedDate, "slot" => $requestedSlot) ) ) {

        echo "<script> alert('Backtermin storniert'); </script>";
        $currentDate = new DateTime();
        $requestedDateTime = DateTime::createFromFormat('Y-m-d', $requestedDate);
        $requestedDateFormatted = $requestedDateTime->format('d.m.Y');
        $interval = $currentDate->diff($requestedDateTime);

        if ($interval->days < 42) { // 6 weeks * 7 days = 42 days
            $to = "kontakt@backhaus-heumaden.de";
            // $to = "mtthff@gmail.com, olaf.fischer@gmx.de";
            $subject = "Termin storniert";
            $message = "Die Backgruppe $backgruppe hat soeben den Backtermin $requestedDateFormatted mit dem Slot \"$requestedSlot\" storniert.";
            $headers = "From: no-reply@backhaus-heumaden.de";
            mail($to, $subject, $message, $headers);
        }
        header("Location: index.php?month=" . $month . "&year=" . $year . "&msg=successDelete");
      }

    } else {
      echo "<script> alert('Fehler: falsches Passwort'); </script>";
      header("Location: index.php?month=" . $month . "&year=" . $year . "&msg=failPW");
    }
  }

}

?>

<?php

session_start();

function redirectToCalendar($month, $year, $msg = "") {
  $location = "index.php?month=" . $month . "&year=" . $year;
  if ( $msg !== "" ) {
    $location .= "&msg=" . urlencode($msg);
  }
  header("Location: " . $location);
  exit();
}

$month = isset($_POST["month"]) ? $_POST["month"] : date("m");
$year = isset($_POST["year"]) ? $_POST["year"] : date("Y");
$msg = isset($_POST["msg"]) ? $_POST["msg"] : "";

if ( isset($_POST["acceptCookie"]) ) {
  $_SESSION["einv"] = "einverstanden";
  redirectToCalendar($month, $year, $msg);
}

if ( isset($_POST["deleteCookie"]) ) {
  session_destroy();
  redirectToCalendar($month, $year, $msg);
}

header("Location: index.php");
exit();

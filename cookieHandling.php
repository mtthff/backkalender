<?php

session_start();

// echo var_dump($_POST);

if ( isset($_POST["acceptCookie"]) ) {
  $_SESSION["einv"] = "einverstanden";
  echo "<script> window.location.href = 'index.php?month=" . $_POST["month"] . "&year=" . $_POST["year"] . "&msg=" . $_POST["msg"] . "'; </script>";
}

if ( isset($_POST["deleteCookie"]) ) {
  session_destroy();
  echo "<script> window.location.href = 'index.php?month=" . $_POST["month"] . "&year=" . $_POST["year"] . "&msg=" . $_POST["msg"] . "'; </script>";
}

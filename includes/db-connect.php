<?php
// ==========================================================================
// DATABASE CONNECTION
// Every page that needs the database will include this file
// ==========================================================================

$db_host = "127.0.0.1";
$db_user = "root";
$db_pass = "";
$db_name = "lec_database";

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
  die("Database connection failed: " . $conn->connect_error);
}
?>
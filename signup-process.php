<?php
session_start();
require "includes/db-connect.php";

$full_name = $_POST['full_name'];
$email = $_POST['email'];
$whatsapp_country = $_POST['whatsapp_country'];
$whatsapp_local = substr(preg_replace('/\D/', '', $_POST['whatsapp_number']), 0, 9);
$whatsapp_number = $whatsapp_country . $whatsapp_local;
$password = $_POST['password'];

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO students (full_name, email, whatsapp_number, password) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $full_name, $email, $whatsapp_number, $hashed_password);

if ($stmt->execute()) {
  $new_id = $stmt->insert_id;
  unset($_SESSION['admin_id'], $_SESSION['admin_name']);
  $_SESSION['student_id'] = $new_id;
  $_SESSION['student_name'] = $full_name;
  $stmt->close();
  $conn->close();
  header("Location: student/dashboard.php");
  exit;
} else {
  $duplicate = ($conn->errno === 1062);
  $stmt->close();
  $conn->close();
  if ($duplicate) {
    header("Location: index.html?signup_error=duplicate");
  } else {
    header("Location: index.html?signup_error=general");
  }
  exit;
}
?>

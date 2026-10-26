<?php
session_start();
require "includes/db-connect.php";

$full_name = $_POST['full_name'];
$email = $_POST['email'];
$whatsapp_country = $_POST['whatsapp_country'];
$whatsapp_local = preg_replace('/\D/', '', $_POST['whatsapp_number']);
$whatsapp_number = $whatsapp_country . $whatsapp_local;
$password = $_POST['password'];

// Check if this email is already registered
$stmt = $conn->prepare("SELECT id FROM students WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
  $stmt->close();
  $conn->close();
  header("Location: index.html?signup_error=duplicate");
  exit;
}
$stmt->close();
$conn->close();

// Generate a 6-digit code
$otp = strval(random_int(100000, 999999));

// Hold the signup details temporarily (not yet saved as a real account)
$_SESSION['pending_signup'] = [
  'full_name' => $full_name,
  'email' => $email,
  'whatsapp_number' => $whatsapp_number,
  'password' => password_hash($password, PASSWORD_DEFAULT),
  'otp' => $otp,
  'otp_time' => time(),
];

require "includes/send-otp-email.php";
$sent = sendOtpEmail($email, $full_name, $otp);

if (!$sent) {
  header("Location: index.html?signup_error=general");
  exit;
}

header("Location: otp-verify.php");
exit;
?>
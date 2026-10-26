<?php
require "includes/db-connect.php";
require "includes/send-mail.php";

$full_name = trim($_POST['full_name']);
$email = trim($_POST['email']);
$whatsapp_number = trim($_POST['whatsapp_number']);
$message = trim($_POST['message']);

if ($full_name === '' || $email === '' || $message === '') {
  header("Location: index.html?contact_error=1#contact");
  exit;
}

$stmt = $conn->prepare("INSERT INTO contact_messages (full_name, email, whatsapp_number, message) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $full_name, $email, $whatsapp_number, $message);
$stmt->execute();
$stmt->close();
$conn->close();

$notify_body = "New contact form submission:\n\nName: $full_name\nEmail: $email\nWhatsApp: $whatsapp_number\n\nMessage:\n$message";
sendMail('arshadhshadh@gmail.com', 'Level Up Educational Centre', 'New Contact Form Submission - LEC Website', $notify_body);

header("Location: index.html?contact_success=1#contact");
exit;
?>
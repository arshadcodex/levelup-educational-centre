<?php
require __DIR__ . "/mail-config.php";
require __DIR__ . "/PHPMailer/Exception.php";
require __DIR__ . "/PHPMailer/PHPMailer.php";
require __DIR__ . "/PHPMailer/SMTP.php";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendOtpEmail($toEmail, $toName, $otp) {
  $mail = new PHPMailer(true);
  try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
    $mail->Password = SMTP_PASSWORD;
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('arshadhshadh@gmail.com', 'Level Up Educational Centre');
    $mail->addAddress($toEmail, $toName);

    $mail->Subject = 'Your Verification Code - Level Up Educational Centre';
    $mail->Body = "Hi $toName,\n\nYour verification code is: $otp\n\nThis code expires in 60 seconds.\n\nIf you did not request this, you can ignore this email.";

    $mail->send();
    return true;
  } catch (Exception $e) {
    return false;
  }
}
?>
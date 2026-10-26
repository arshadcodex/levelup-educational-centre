<?php
session_start();
require "includes/db-connect.php";

$email = $_POST['email'];
$password = $_POST['password'];

$stmt = $conn->prepare("SELECT id, full_name, password FROM admins WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
  $admin = $result->fetch_assoc();

  if (password_verify($password, $admin['password'])) {
    unset($_SESSION['student_id'], $_SESSION['student_name']);
    $_SESSION['admin_id'] = $admin['id'];
    $_SESSION['admin_name'] = $admin['full_name'];
    $stmt->close();
    $conn->close();
    header("Location: admin/dashboard.php");
    exit;
  } else {
    $stmt->close();
    $conn->close();
    header("Location: index.html?login_error=wrong_password");
    exit;
  }
}

$stmt = $conn->prepare("SELECT id, full_name, password, status FROM students WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
  $student = $result->fetch_assoc();

  if (password_verify($password, $student['password'])) {
    if ($student['status'] === 'blocked') {
      $stmt->close();
      $conn->close();
      header("Location: index.html?login_error=blocked");
      exit;
    }

    unset($_SESSION['admin_id'], $_SESSION['admin_name']);
    $_SESSION['student_id'] = $student['id'];
    $_SESSION['student_name'] = $student['full_name'];
    $stmt->close();
    $conn->close();
    header("Location: student/dashboard.php");
    exit;
  } else {
    $stmt->close();
    $conn->close();
    header("Location: index.html?login_error=wrong_password");
    exit;
  }
} else {
  $stmt->close();
  $conn->close();
  header("Location: index.html?login_error=notfound");
  exit;
}
?>
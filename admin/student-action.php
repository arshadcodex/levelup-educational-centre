<?php
session_start();
require "../includes/db-connect.php";

if (!isset($_SESSION['admin_id'])) {
  header("Location: ../index.html");
  exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($id > 0) {
  if ($action === 'block') {
    $stmt = $conn->prepare("UPDATE students SET status = 'blocked' WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
  } elseif ($action === 'unblock') {
    $stmt = $conn->prepare("UPDATE students SET status = 'active' WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
  } elseif ($action === 'delete') {
    $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
  }
}

$conn->close();
header("Location: dashboard.php");
exit;
?>
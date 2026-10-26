<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
  header("Location: ../index.html");
  exit;
}
require "../includes/db-connect.php";

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = $conn->prepare("SELECT full_name, email, whatsapp_number, profile_picture, created_at FROM students WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$stmt->close();
$conn->close();

if (!$student) {
  header("Location: dashboard.php");
  exit;
}

$photo = $student['profile_picture'] ? "../images/uploads/" . $student['profile_picture'] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($student['full_name']); ?> - Student Profile</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/dashboard.css">
</head>
<body>
  <div class="otp-verify-wrap">
    <div class="otp-verify-box" style="max-width: 420px;">
      <a href="dashboard.php" style="display:inline-block; margin-bottom: 16px; color: var(--color-primary); text-decoration:none; font-size:0.85rem;">&larr; Back to Dashboard</a>

      <?php if ($photo): ?>
        <img src="<?php echo htmlspecialchars($photo); ?>" alt="" style="width:120px; height:120px; border-radius:50%; object-fit:cover; margin: 0 auto 16px; display:block; border: 3px solid var(--color-primary);">
      <?php else: ?>
        <div style="width:120px; height:120px; border-radius:50%; background: var(--color-primary); color:#fff; font-size:2.5rem; font-weight:700; display:flex; align-items:center; justify-content:center; margin: 0 auto 16px;">
          <?php echo strtoupper(substr($student['full_name'], 0, 1)); ?>
        </div>
      <?php endif; ?>

      <h1 style="text-align:center;"><?php echo htmlspecialchars($student['full_name']); ?></h1>

      <div style="text-align:left; margin-top: 20px; display:flex; flex-direction:column; gap:14px;">
        <div>
          <div style="font-size:0.75rem; color: var(--color-text-muted); text-transform:uppercase; letter-spacing:0.5px;">Email</div>
          <div style="font-size:1rem; color: var(--color-text);"><?php echo htmlspecialchars($student['email']); ?></div>
        </div>
        <div>
          <div style="font-size:0.75rem; color: var(--color-text-muted); text-transform:uppercase; letter-spacing:0.5px;">WhatsApp Number</div>
          <div style="font-size:1rem; color: var(--color-text);"><?php echo $student['whatsapp_number'] ? htmlspecialchars($student['whatsapp_number']) : '—'; ?></div>
        </div>
        <div>
          <div style="font-size:0.75rem; color: var(--color-text-muted); text-transform:uppercase; letter-spacing:0.5px;">Joined</div>
          <div style="font-size:1rem; color: var(--color-text);"><?php echo date("d M Y", strtotime($student['created_at'])); ?></div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
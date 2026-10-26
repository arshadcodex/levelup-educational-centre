<?php
session_start();
require "../includes/db-connect.php";

if (!isset($_SESSION['student_id'])) {
  header("Location: ../index.html");
  exit;
}

$stmt = $conn->prepare("SELECT full_name, email, created_at, profile_picture FROM students WHERE id = ?");
$stmt->bind_param("i", $_SESSION['student_id']);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$photo_url = $student['profile_picture'] ? "../images/uploads/" . $student['profile_picture'] : null;

$nav_items = [
  ['key' => 'dashboard', 'label' => 'Dashboard',   'icon' => '&#128202;', 'link' => 'dashboard.php'],
  ['key' => 'classes',   'label' => 'My Classes',  'icon' => '&#128218;', 'link' => '../shared/coming-soon.php?page=My Classes&key=classes'],
  ['key' => 'payments',  'label' => 'My Payments', 'icon' => '&#128179;', 'link' => '../shared/coming-soon.php?page=My Payments&key=payments'],
  ['key' => 'profile',   'label' => 'Profile',     'icon' => '&#128100;', 'link' => '../shared/profile.php'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Dashboard - Level Up Educational Centre</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/dashboard.css">
</head>
<body class="dash-body">

  <aside class="dash-sidebar">
    <div class="dash-sidebar-logo">
      <img src="../images/logo.png" alt="LEC" id="logoImg">
      <span>Level Up<br>Educational Centre</span>
    </div>
    <nav class="dash-nav">
      <?php foreach ($nav_items as $item): ?>
        <a href="<?php echo $item['link']; ?>" class="<?php echo ($item['key'] === 'dashboard') ? 'active' : ''; ?>">
          <?php echo $item['icon']; ?> <?php echo $item['label']; ?>
        </a>
      <?php endforeach; ?>
    </nav>
    <div class="dash-sidebar-footer">
      <a href="../index.html">&larr; View Website</a><br><br>
      <a href="logout.php">&#128682; Log Out</a>
    </div>
   </aside>
  <div class="dash-sidebar-overlay" id="dashSidebarOverlay"></div>

  <div class="dash-main">
        <div class="dash-topbar">
      <button class="dash-mobile-toggle" id="dashMobileToggle" aria-label="Toggle menu">&#9776;</button>
      <h1>My Dashboard</h1>
            <div class="dash-topbar-user">
        <span><?php echo htmlspecialchars($student['full_name']); ?></span>
        <a href="../shared/profile.php" class="dash-avatar">
          <?php if ($photo_url): ?>
            <img src="<?php echo $photo_url; ?>" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
          <?php else: ?>
            <?php echo strtoupper(substr($student['full_name'], 0, 1)); ?>
          <?php endif; ?>
        </a>
      </div>
    </div>

    <div class="dash-content">

      <div class="dash-stats">
        <div class="dash-stat-card">
          <div class="label">Enrolled Classes</div>
          <div class="value">0</div>
        </div>
        <div class="dash-stat-card">
          <div class="label">Member Since</div>
          <div class="value" style="font-size: 1.1rem;"><?php echo date("d M Y", strtotime($student['created_at'])); ?></div>
        </div>
      </div>

      <div class="dash-card" style="margin-bottom: 24px;">
        <div class="dash-card-header">
          <h2>My Profile</h2>
        </div>
        <table class="dash-table">
          <tbody>
            <tr>
              <td style="color: var(--color-text-muted); width: 160px;">Full Name</td>
              <td><?php echo htmlspecialchars($student['full_name']); ?></td>
            </tr>
            <tr>
              <td style="color: var(--color-text-muted);">Email</td>
              <td><?php echo htmlspecialchars($student['email']); ?></td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="dash-card">
        <div class="dash-card-header">
          <h2>My Classes</h2>
          <span class="subject-tag-soon" style="padding: 4px 12px; border-radius: 999px; font-size: 0.78rem;">Coming Soon</span>
        </div>
        <div style="padding: 32px 24px; text-align: center; color: var(--color-text-muted);">
          Your enrolled classes, schedule, and progress will appear here once class requests are approved by the admin.
        </div>
      </div>

    </div>
  </div>

  <div class="lightbox-overlay" id="logoLightbox">
    <img src="../images/logo.png" alt="Level Up Educational Centre" class="lightbox-img">
  </div>

  <script src="../js/script.js"></script>
</body>
</html>
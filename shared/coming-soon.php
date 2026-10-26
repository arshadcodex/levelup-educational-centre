<?php
session_start();

$is_admin = isset($_SESSION['admin_id']);
$is_student = isset($_SESSION['student_id']);

if (!$is_admin && !$is_student) {
  header("Location: ../index.html");
  exit;
}

$name = $is_admin ? $_SESSION['admin_name'] : $_SESSION['student_name'];

require "../includes/db-connect.php";
$table = $is_admin ? "admins" : "students";
$user_id = $is_admin ? $_SESSION['admin_id'] : $_SESSION['student_id'];
$photoResult = $conn->query("SELECT profile_picture FROM $table WHERE id = " . intval($user_id));
$userPhoto = $photoResult->fetch_assoc()['profile_picture'];
$photo_url = $userPhoto ? "../images/uploads/" . $userPhoto : null;
$logout_link = $is_admin ? "../admin/logout.php" : "../student/logout.php";
$dashboard_link = $is_admin ? "../admin/dashboard.php" : "../student/dashboard.php";

$current_key = isset($_GET['key']) ? $_GET['key'] : '';
$page_title = isset($_GET['page']) ? htmlspecialchars($_GET['page']) : "This Section";

if ($is_admin) {
  $nav_items = [
    ['key' => 'dashboard', 'label' => 'Dashboard', 'icon' => '&#128202;', 'link' => $dashboard_link],
    ['key' => 'students',  'label' => 'Students',  'icon' => '&#128101;', 'link' => '../shared/coming-soon.php?page=Students&key=students'],
    ['key' => 'classes',   'label' => 'Classes',   'icon' => '&#128218;', 'link' => '../shared/coming-soon.php?page=Classes&key=classes'],
    ['key' => 'settings',  'label' => 'Settings',  'icon' => '&#9881;',   'link' => '../shared/coming-soon.php?page=Settings&key=settings'],
  ];
} else {
  $nav_items = [
    ['key' => 'dashboard', 'label' => 'Dashboard',   'icon' => '&#128202;', 'link' => $dashboard_link],
    ['key' => 'classes',   'label' => 'My Classes',  'icon' => '&#128218;', 'link' => '../shared/coming-soon.php?page=My Classes&key=classes'],
    ['key' => 'payments',  'label' => 'My Payments', 'icon' => '&#128179;', 'link' => '../shared/coming-soon.php?page=My Payments&key=payments'],
    ['key' => 'profile',   'label' => 'Profile',     'icon' => '&#128100;', 'link' => '../shared/profile.php'],
  ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $page_title; ?> - Level Up Educational Centre</title>
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
        <a href="<?php echo $item['link']; ?>" class="<?php echo ($item['key'] === $current_key) ? 'active' : ''; ?>">
          <?php echo $item['icon']; ?> <?php echo $item['label']; ?>
        </a>
      <?php endforeach; ?>
    </nav>
    <div class="dash-sidebar-footer">
      <a href="../index.html">&larr; View Website</a><br><br>
      <a href="<?php echo $logout_link; ?>">&#128682; Log Out</a>
    </div>
   </aside>
  <div class="dash-sidebar-overlay" id="dashSidebarOverlay"></div>

  <div class="dash-main">
        <div class="dash-topbar">
      <button class="dash-mobile-toggle" id="dashMobileToggle" aria-label="Toggle menu">&#9776;</button>
      <h1><?php echo $page_title; ?></h1>
            <div class="dash-topbar-user">
        <span><?php echo htmlspecialchars($name); ?></span>
        <a href="../shared/profile.php" class="dash-avatar">
          <?php if ($photo_url): ?>
            <img src="<?php echo $photo_url; ?>" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
          <?php else: ?>
            <?php echo strtoupper(substr($name, 0, 1)); ?>
          <?php endif; ?>
        </a>
      </div>
    </div>

    <div class="dash-content">
      <div class="dash-card">
        <div style="padding: 60px 24px; text-align: center;">
          <div style="font-size: 2.5rem; margin-bottom: 16px;">&#128679;</div>
                    <h2 style="margin-bottom: 10px;"><?php echo $page_title; ?></h2>
          <p style="color: var(--color-text-muted); max-width: 400px; margin: 0 auto;">
            This is launching in our next update — we're working on it behind the scenes. Stay tuned!
          </p>
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
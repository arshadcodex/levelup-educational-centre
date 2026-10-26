<?php
session_start();
require "../includes/db-connect.php";

$is_admin = isset($_SESSION['admin_id']);
$is_student = isset($_SESSION['student_id']);

if (!$is_admin && !$is_student) {
  header("Location: ../index.html");
  exit;
}

$table = $is_admin ? "admins" : "students";
$id = $is_admin ? $_SESSION['admin_id'] : $_SESSION['student_id'];
$dashboard_link = $is_admin ? "../admin/dashboard.php" : "../student/dashboard.php";
$logout_link = $is_admin ? "../admin/logout.php" : "../student/logout.php";

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

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_photo'])) {
  $file = $_FILES['profile_photo'];

  if ($file['error'] === UPLOAD_ERR_OK) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $max_size = 2 * 1024 * 1024;

    if (!in_array($file['type'], $allowed_types)) {
      $error = "Please upload a JPG, PNG, GIF, or WEBP image.";
    } elseif ($file['size'] > $max_size) {
      $error = "That image is too large. Please choose one under 2MB.";
    } else {
      $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
      $new_filename = $table . "_" . $id . "_" . time() . "." . $extension;
      $destination = "../images/uploads/" . $new_filename;

      if (move_uploaded_file($file['tmp_name'], $destination)) {
        $stmt = $conn->prepare("UPDATE $table SET profile_picture = ? WHERE id = ?");
        $stmt->bind_param("si", $new_filename, $id);
        $stmt->execute();
        $success = "Profile photo updated!";
      } else {
        $error = "Something went wrong uploading the file. Please try again.";
      }
    }
  } else {
    $error = "Upload failed. Please try again.";
  }
}

$stmt = $conn->prepare("SELECT * FROM $table WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$photo_url = $user['profile_picture'] ? "../images/uploads/" . $user['profile_picture'] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Profile - Level Up Educational Centre</title>
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
        <a href="<?php echo $item['link']; ?>" class="<?php echo ($item['key'] === 'profile') ? 'active' : ''; ?>">
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
      <h1>My Profile</h1>
      <div class="dash-topbar-user">
        <span><?php echo htmlspecialchars($user['full_name']); ?></span>
        <a href="profile.php" class="dash-avatar">
          <?php if ($photo_url): ?>
            <img src="<?php echo $photo_url; ?>" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
          <?php else: ?>
            <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
          <?php endif; ?>
        </a>
      </div>
    </div>

    <div class="dash-content">

      <?php if ($error): ?>
        <div style="background:#fee2e2; color:#b91c1c; padding:14px 18px; border-radius:8px; margin-bottom:20px;"><?php echo $error; ?></div>
      <?php endif; ?>

      <?php if ($success): ?>
        <div style="background:#dcfce7; color:#15803d; padding:14px 18px; border-radius:8px; margin-bottom:20px;"><?php echo $success; ?></div>
      <?php endif; ?>

      <div class="dash-card" style="padding: 36px; text-align: center; margin-bottom: 24px;">

        <div style="width:120px; height:120px; border-radius:50%; margin:0 auto 20px; overflow:hidden; background:var(--color-primary-light); display:flex; align-items:center; justify-content:center; font-size:2.4rem; font-weight:700; color:var(--color-primary);">
          <?php if ($photo_url): ?>
            <img src="<?php echo $photo_url; ?>" style="width:100%; height:100%; object-fit:cover;">
          <?php else: ?>
            <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
          <?php endif; ?>
        </div>

        <form action="profile.php" method="POST" enctype="multipart/form-data" style="display:inline-block;">
          <label for="profile_photo" class="btn btn-outline" style="cursor:pointer; display:inline-block;">
            Choose Photo
          </label>
          <input type="file" id="profile_photo" name="profile_photo" accept="image/*" style="display:none;" onchange="this.form.submit()">
        </form>

        <p style="color: var(--color-text-muted); font-size: 0.82rem; margin-top: 10px;">
          JPG, PNG, GIF or WEBP. Max 2MB.
        </p>

      </div>

      <div class="dash-card">
        <div class="dash-card-header">
          <h2>Account Details</h2>
        </div>
        <table class="dash-table">
          <tbody>
            <tr>
              <td style="color: var(--color-text-muted); width: 160px;">Full Name</td>
              <td><?php echo htmlspecialchars($user['full_name']); ?></td>
            </tr>
            <tr>
              <td style="color: var(--color-text-muted);">Email</td>
              <td><?php echo htmlspecialchars($user['email']); ?></td>
            </tr>
            <?php if ($is_student): ?>
            <tr>
              <td style="color: var(--color-text-muted);">WhatsApp Number</td>
              <td><?php echo !empty($user['whatsapp_number']) ? htmlspecialchars($user['whatsapp_number']) : '—'; ?></td>
            </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <?php if ($is_student): ?>
      <div style="text-align:center; margin-top: 20px;">
        <a href="../student/edit-profile.php" class="btn btn-primary" style="display:inline-block; text-decoration:none;">Edit Profile</a>
      </div>
      <?php endif; ?>

    </div>
  </div>

  <div class="lightbox-overlay" id="logoLightbox">
    <img src="../images/logo.png" alt="Level Up Educational Centre" class="lightbox-img">
  </div>

  <script src="../js/script.js"></script>
</body>
</html>
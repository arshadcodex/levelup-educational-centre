<?php
session_start();
require "../includes/db-connect.php";

if (!isset($_SESSION['admin_id'])) {
  header("Location: ../index.html");
  exit;
}

$totalResult = $conn->query("SELECT COUNT(*) AS total FROM students");
$totalStudents = $totalResult->fetch_assoc()['total'];

$weekResult = $conn->query("SELECT COUNT(*) AS total FROM students WHERE created_at >= NOW() - INTERVAL 7 DAY");
$newThisWeek = $weekResult->fetch_assoc()['total'];
$photoResult = $conn->query("SELECT profile_picture FROM admins WHERE id = " . intval($_SESSION['admin_id']));
$adminPhoto = $photoResult->fetch_assoc()['profile_picture'];
$photo_url = $adminPhoto ? "../images/uploads/" . $adminPhoto : null;

$students = $conn->query("SELECT id, full_name, email, whatsapp_number, status, created_at FROM students ORDER BY created_at DESC");$messages = $conn->query("SELECT full_name, email, whatsapp_number, message, created_at FROM contact_messages ORDER BY created_at DESC");

$nav_items = [
  ['key' => 'dashboard', 'label' => 'Dashboard', 'icon' => '&#128202;', 'link' => 'dashboard.php'],
  ['key' => 'students',  'label' => 'Students',  'icon' => '&#128101;', 'link' => '../shared/coming-soon.php?page=Students&key=students'],
  ['key' => 'classes',   'label' => 'Classes',   'icon' => '&#128218;', 'link' => '../shared/coming-soon.php?page=Classes&key=classes'],
  ['key' => 'settings',  'label' => 'Settings',  'icon' => '&#9881;',   'link' => '../shared/coming-soon.php?page=Settings&key=settings'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - Level Up Educational Centre</title>
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
      <h1>Admin Dashboard</h1>
            <div class="dash-topbar-user">
        <span><?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
        <a href="../shared/profile.php" class="dash-avatar">
          <?php if ($photo_url): ?>
            <img src="<?php echo $photo_url; ?>" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
          <?php else: ?>
            <?php echo strtoupper(substr($_SESSION['admin_name'], 0, 1)); ?>
          <?php endif; ?>
        </a>
      </div>
    </div>

    <div class="dash-content">

      <div class="dash-stats">
        <div class="dash-stat-card">
          <div class="label">Total Students</div>
          <div class="value"><?php echo $totalStudents; ?></div>
        </div>
        <div class="dash-stat-card">
          <div class="label">New This Week</div>
          <div class="value"><?php echo $newThisWeek; ?></div>
        </div>
      </div>

      <div class="dash-card">
        <div class="dash-card-header">
          <h2>All Students</h2>
        </div>
        <table class="dash-table">
          <thead>
            <tr>
                            <th>Name</th>
<th>Email</th>
<th>WhatsApp</th>
<th>Status</th>
<th>Joined</th>
<th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $students->fetch_assoc()): ?>
              <tr>
                <td>
                  <div class="dash-table-user">
                    <div class="dash-table-avatar"><?php echo strtoupper(substr($row['full_name'], 0, 1)); ?></div>
                    <?php echo htmlspecialchars($row['full_name']); ?>
                  </div>
                </td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
<td><?php echo $row['whatsapp_number'] ? htmlspecialchars($row['whatsapp_number']) : '—'; ?></td>
<td>
  <?php if ($row['status'] === 'blocked'): ?>
    <span class="status-badge status-blocked">Blocked</span>
  <?php else: ?>
    <span class="status-badge status-active">Active</span>
  <?php endif; ?>
</td>
<td><?php echo date("d M Y", strtotime($row['created_at'])); ?></td>
<td class="action-cell">
  <a href="view-student.php?id=<?php echo $row['id']; ?>" class="btn-view-student">View</a>
  <?php if ($row['status'] === 'blocked'): ?>
    <a href="student-action.php?action=unblock&id=<?php echo $row['id']; ?>" class="btn-unblock">Unblock</a>
  <?php else: ?>
    <a href="student-action.php?action=block&id=<?php echo $row['id']; ?>" class="btn-block" onclick="return confirm('Block this student? They will not be able to log in until unblocked.');">Block</a>
  <?php endif; ?>
  <a href="student-action.php?action=delete&id=<?php echo $row['id']; ?>" class="btn-delete" onclick="return confirm('Permanently delete this student? This cannot be undone.');">Delete</a>
</td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>

      <div class="dash-card" style="margin-top: 24px;">
        <div class="dash-card-header">
          <h2>Contact Messages</h2>
        </div>
        <table class="dash-table">
          <thead>
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>WhatsApp</th>
              <th>Message</th>
              <th>Received</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($messages->num_rows === 0): ?>
              <tr>
                <td colspan="5" style="text-align:center; color: var(--color-text-muted); padding: 24px;">No messages yet.</td>
              </tr>
            <?php else: ?>
              <?php while ($msg = $messages->fetch_assoc()): ?>
                <tr>
                  <td>
                    <div class="dash-table-user">
                      <div class="dash-table-avatar"><?php echo strtoupper(substr($msg['full_name'], 0, 1)); ?></div>
                      <?php echo htmlspecialchars($msg['full_name']); ?>
                    </div>
                  </td>
                  <td><?php echo htmlspecialchars($msg['email']); ?></td>
                  <td><?php echo $msg['whatsapp_number'] ? htmlspecialchars($msg['whatsapp_number']) : '—'; ?></td>
                  <td style="max-width: 280px; white-space: normal;"><?php echo nl2br(htmlspecialchars($msg['message'])); ?></td>
                  <td><?php echo date("d M Y, g:i A", strtotime($msg['created_at'])); ?></td>
                </tr>
              <?php endwhile; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

    </div>
  </div>

  <div class="lightbox-overlay" id="logoLightbox">
    <img src="../images/logo.png" alt="Level Up Educational Centre" class="lightbox-img">
  </div>

  <script src="../js/script.js"></script>
</body>
</html>
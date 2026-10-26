<?php
session_start();
if (!isset($_SESSION['student_id'])) {
  header("Location: ../index.html");
  exit;
}
require "../includes/db-connect.php";

$student_id = $_SESSION['student_id'];

$stmt = $conn->prepare("SELECT full_name, email, whatsapp_number FROM students WHERE id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$stmt->close();

function splitWhatsapp($full) {
  $codes = ['+971', '+94', '+91', '+65', '+60', '+44', '+1'];
  foreach ($codes as $code) {
    if (strpos($full, $code) === 0) {
      return [$code, substr($full, strlen($code))];
    }
  }
  return ['+94', ''];
}

list($current_code, $current_number) = splitWhatsapp($student['whatsapp_number']);

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $full_name = trim($_POST['full_name']);
  $email = trim($_POST['email']);
  $whatsapp_country = $_POST['whatsapp_country'];
  $whatsapp_local = substr(preg_replace('/\D/', '', $_POST['whatsapp_number']), 0, 9);
  $whatsapp_number = $whatsapp_country . $whatsapp_local;

  if ($full_name === '' || $email === '' || $whatsapp_local === '') {
    $error = "Please fill in all fields.";
  } else {
    $check = $conn->prepare("SELECT id FROM students WHERE email = ? AND id != ?");
    $check->bind_param("si", $email, $student_id);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
      $error = "That email is already used by another account.";
    }
    $check->close();

    if ($error === "") {
      $update = $conn->prepare("UPDATE students SET full_name = ?, email = ?, whatsapp_number = ? WHERE id = ?");
      $update->bind_param("sssi", $full_name, $email, $whatsapp_number, $student_id);
      $update->execute();
      $update->close();

      $_SESSION['student_name'] = $full_name;
      $student['full_name'] = $full_name;
      $student['email'] = $email;
      $student['whatsapp_number'] = $whatsapp_number;
      list($current_code, $current_number) = splitWhatsapp($whatsapp_number);

      $success = "Your profile has been updated.";
    }
  }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Profile - Level Up Educational Centre</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/dashboard.css">
</head>
<body>
  <div class="otp-verify-wrap">
    <div class="otp-verify-box" style="max-width: 440px; text-align: left;">
      <a href="../shared/profile.php" style="display:inline-block; margin-bottom: 16px; color: var(--color-primary); text-decoration:none; font-size:0.85rem;">&larr; Back to Profile</a>
      <h1 style="text-align:center;">Edit Profile</h1>

      <?php if ($error): ?>
        <div class="otp-verify-error"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>
      <?php if ($success): ?>
        <div style="background:#e6f7ec; color:#1e7e34; padding:10px 14px; border-radius:8px; font-size:0.85rem; margin-bottom:18px;"><?php echo htmlspecialchars($success); ?></div>
      <?php endif; ?>

      <form method="POST" style="display:flex; flex-direction:column; gap:14px;">
        <input type="text" name="full_name" value="<?php echo htmlspecialchars($student['full_name']); ?>" placeholder="Full name" required style="padding:12px; border:1px solid var(--color-border); border-radius:10px;">
        <input type="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" placeholder="Email address" required style="padding:12px; border:1px solid var(--color-border); border-radius:10px;">

        <div class="modal-phone-row">
          <div class="phone-code-select">
            <img id="whatsappFlag" src="https://flagcdn.com/24x18/<?php
              $flag_map = ['+971' => 'ae', '+94' => 'lk', '+91' => 'in', '+65' => 'sg', '+60' => 'my', '+44' => 'gb', '+1' => 'us'];
              echo $flag_map[$current_code] ?? 'lk';
            ?>.png" alt="" class="phone-flag-icon">
            <select name="whatsapp_country" id="whatsappCountry">
              <option value="+94" data-flag="lk" <?php echo $current_code === '+94' ? 'selected' : ''; ?>>+94</option>
              <option value="+91" data-flag="in" <?php echo $current_code === '+91' ? 'selected' : ''; ?>>+91</option>
              <option value="+60" data-flag="my" <?php echo $current_code === '+60' ? 'selected' : ''; ?>>+60</option>
              <option value="+65" data-flag="sg" <?php echo $current_code === '+65' ? 'selected' : ''; ?>>+65</option>
              <option value="+44" data-flag="gb" <?php echo $current_code === '+44' ? 'selected' : ''; ?>>+44</option>
              <option value="+971" data-flag="ae" <?php echo $current_code === '+971' ? 'selected' : ''; ?>>+971</option>
              <option value="+1" data-flag="us" <?php echo $current_code === '+1' ? 'selected' : ''; ?>>+1</option>
            </select>
          </div>
          <input type="tel" name="whatsapp_number" value="<?php echo htmlspecialchars($current_number); ?>" placeholder="WhatsApp number" maxlength="9" required>
        </div>

        <button type="submit" class="btn btn-primary">Save Changes</button>
      </form>
    </div>
  </div>

  <script>
    const whatsappCountry = document.getElementById('whatsappCountry');
    const whatsappFlag = document.getElementById('whatsappFlag');
    if (whatsappCountry && whatsappFlag) {
      whatsappCountry.addEventListener('change', function () {
        const code = this.options[this.selectedIndex].getAttribute('data-flag');
        whatsappFlag.src = `https://flagcdn.com/24x18/${code}.png`;
      });
    }
    const whatsappNumberInput = document.querySelector('input[name="whatsapp_number"]');
    if (whatsappNumberInput) {
      whatsappNumberInput.addEventListener('input', function () {
        this.value = this.value.replace(/\D/g, '').slice(0, 9);
      });
    }
    const editProfileForm = document.querySelector('form[method="POST"]');
if (editProfileForm) {
  editProfileForm.addEventListener('submit', function () {
    const btn = editProfileForm.querySelector('button[type="submit"]');
    if (btn) {
      btn.disabled = true;
      btn.textContent = 'Saving Changes...';
    }
  });
}
  </script>
</body>
</html>
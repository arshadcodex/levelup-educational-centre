<?php
session_start();

if (!isset($_SESSION['pending_signup'])) {
  header("Location: index.html");
  exit;
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['resend'])) {
    require "includes/send-otp-email.php";
    $otp = strval(random_int(100000, 999999));
    $_SESSION['pending_signup']['otp'] = $otp;
    $_SESSION['pending_signup']['otp_time'] = time();
    sendOtpEmail($_SESSION['pending_signup']['email'], $_SESSION['pending_signup']['full_name'], $otp);
    $error = "A new code has been sent.";
  } else {
    $entered = $_POST['otp_code'];
    $pending = $_SESSION['pending_signup'];
    $expired = (time() - $pending['otp_time']) > 60;

    if ($expired) {
      $error = "That code expired. Please request a new one.";
    } elseif ($entered !== $pending['otp']) {
      $error = "Incorrect code. Please try again.";
    } else {
      require "includes/db-connect.php";

      $stmt = $conn->prepare("INSERT INTO students (full_name, email, whatsapp_number, password) VALUES (?, ?, ?, ?)");
      $stmt->bind_param("ssss", $pending['full_name'], $pending['email'], $pending['whatsapp_number'], $pending['password']);
      $stmt->execute();
      $new_id = $stmt->insert_id;
      $stmt->close();
      $conn->close();

      unset($_SESSION['pending_signup']);
      unset($_SESSION['admin_id'], $_SESSION['admin_name']);
      $_SESSION['student_id'] = $new_id;
      $_SESSION['student_name'] = $pending['full_name'];

      header("Location: student/dashboard.php");
      exit;
    }
  }
}

$seconds_left = 60 - (time() - $_SESSION['pending_signup']['otp_time']);
if ($seconds_left < 0) $seconds_left = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Verify Your Email - Level Up Educational Centre</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <div class="otp-verify-wrap">
    <div class="otp-verify-box">
      <img src="images/logo.png" alt="Level Up Educational Centre" class="otp-logo">
      <h1>Verify Your Email</h1>
      <p class="otp-verify-sub">We sent a 6-digit code to<br><strong><?php echo htmlspecialchars($_SESSION['pending_signup']['email']); ?></strong></p>

      <?php if ($error): ?>
        <div class="otp-verify-error"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>

      <form method="POST">
        <input type="text" name="otp_code" placeholder="000000" maxlength="6" required inputmode="numeric" pattern="[0-9]*">
        <button type="submit" class="btn btn-primary">Verify</button>
      </form>

      <p id="countdown" class="otp-countdown"></p>

      <form method="POST" id="resendForm" style="display:none;">
        <input type="hidden" name="resend" value="1">
        <button type="submit" class="otp-resend-btn">Resend Code</button>
      </form>
    </div>
  </div>

  <script>
    let secondsLeft = <?php echo $seconds_left; ?>;
    const countdownEl = document.getElementById('countdown');
    const resendForm = document.getElementById('resendForm');

    function tick() {
      if (secondsLeft > 0) {
        countdownEl.textContent = "Code expires in " + secondsLeft + " seconds";
        secondsLeft--;
        setTimeout(tick, 1000);
      } else {
        countdownEl.textContent = "Code expired.";
        resendForm.style.display = "block";
      }
    }
    tick();
  </script>
</body>
</html>
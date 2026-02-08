<?php
require_once __DIR__ . "/../config/db.php";

$msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $reason = trim($_POST["reason"]);

    // find user by email (patient only)
    $stmt = $conn->prepare("SELECT id FROM users WHERE email=? AND role='patient' LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows !== 1) {
        $msg = "❌ Email not found (patient account).";
    } else {
        $u = $res->fetch_assoc();
        $user_id = (int)$u["id"];

        // create request
        $stmt2 = $conn->prepare("INSERT INTO password_reset_requests (user_id, reason) VALUES (?, ?)");
        $stmt2->bind_param("is", $user_id, $reason);
        $stmt2->execute();

        // notify patient in system too (optional)
        $note = "Password reset request received. Admin will reset your password soon.";
        $stmt3 = $conn->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
        $stmt3->bind_param("is", $user_id, $note);
        $stmt3->execute();

        $msg = "✅ Request submitted! Admin will reset your password.";
    }
}
?>




<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Forgot password</title>
  <link rel="stylesheet" href="../assests/app.css">
</head>
<body>

<div class="nav">
  <div class="container">
    <div class="nav-inner">
      <a class="brand" href="../index.php">
        <span class="logo" aria-hidden="true"></span>
        <span>My Project</span>
      </a>
      <div class="nav-links">
        <a href="../index.php">Home</a>
        <a class="btn btn-ghost" href="signin.php">Sign in</a>
      </div>
    </div>
  </div>
</div>

<div class="auth-wrap">
  <div class="card" style="max-width: 560px; margin: 34px auto;">
    <h1 class="auth-title" style="font-size:24px;">Forgot password</h1>
    <p class="auth-subtitle">Enter your email and a short reason. Admin will reset your password.</p>

    <?php if (!empty($msg)) : ?>
      <div class="alert" style="border-color: rgba(37,99,235,.25); background: rgba(37,99,235,.08); color:#1E3A8A;">
        <?php echo htmlspecialchars($msg); ?>
      </div>
    <?php endif; ?>

    <form method="POST">
      <label class="label" for="email">Email</label>
      <input class="input" type="email" id="email" name="email" placeholder="you@example.com" required>

      <label class="label" for="reason">Reason</label>
      <textarea class="input" id="reason" name="reason" rows="4" placeholder="Example: I forgot my password" required></textarea>

      <div class="auth-row">
        <a class="helper-link" href="signin.php">Back to sign in</a>
        <button class="btn btn-primary" type="submit">Submit request</button>
      </div>
    </form>
  </div>
</div>

</body>
</html>


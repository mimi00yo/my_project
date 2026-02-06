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
<!DOCTYPE html>
<html>
<head>
  <title>Forgot Password</title>
  <style>
    body { font-family: Arial; padding: 25px; max-width: 520px; margin: auto; }
    input, textarea { width: 100%; padding: 10px; margin: 10px 0; }
    button { padding: 10px 16px; }
    .msg { margin: 10px 0; }
  </style>
</head>
<body>

  <h2>Forgot Password</h2>
  <p>Enter your registered email. Admin will reset your password.</p>

  <p class="msg"><?php echo htmlspecialchars($msg); ?></p>

  <form method="POST">
    <label>Email</label>
    <input type="email" name="email" required placeholder="your@email.com">

    <label>Reason (optional)</label>
    <textarea name="reason" placeholder="e.g., forgot password"></textarea>

    <button type="submit">Submit Request</button>
  </form>

  <br>
  <a href="signin.php">← Back to Sign in</a>

</body>
</html>


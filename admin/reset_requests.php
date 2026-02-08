<?php
session_start();require_once __DIR__ . "/../config/db.php";

// only admin
if (!isset($_SESSION["user_id"]) || ($_SESSION["role"] ?? "") !== "admin") {
    header("Location: ../public/signin.php");
    exit();
}

$msg = "";

// handle reset action
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $req_id = (int)($_POST["req_id"] ?? 0);
    $new_pass = $_POST["new_password"] ?? "";

    if ($req_id <= 0 || strlen($new_pass) < 4) {
        $msg = "❌ Enter a valid new password (min 4 characters).";
    } else {
        // get user_id from request
        $stmt = $conn->prepare("SELECT user_id FROM password_reset_requests WHERE id=? AND status='pending' LIMIT 1");
        $stmt->bind_param("i", $req_id);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows !== 1) {
            $msg = "❌ Request not found or already done.";
        } else {
            $row = $res->fetch_assoc();
            $user_id = (int)$row["user_id"];

            // update password
            $hash = password_hash($new_pass, PASSWORD_DEFAULT);
            $stmt2 = $conn->prepare("UPDATE users SET password_hash=? WHERE id=? AND role='patient'");
            $stmt2->bind_param("si", $hash, $user_id);
            $stmt2->execute();

            // mark request done
            $stmt3 = $conn->prepare("UPDATE password_reset_requests SET status='done' WHERE id=?");
            $stmt3->bind_param("i", $req_id);
            $stmt3->execute();

            // notify patient
            $note = "Your password has been reset by admin. Please login with your new password.";
            $stmt4 = $conn->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
            $stmt4->bind_param("is", $user_id, $note);
            $stmt4->execute();

            $msg = "✅ Password reset successfully!";
        }
    }
}

// fetch pending requests
$sql = "SELECT r.id, r.reason, r.created_at, u.name, u.email
        FROM password_reset_requests r
        JOIN users u ON r.user_id = u.id
        WHERE r.status='pending'
        ORDER BY r.created_at DESC";
$requests = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
  <title>Password Reset Requests</title>
  <style>
    body { font-family: Arial; padding: 25px; }
    .card { border:1px solid #ddd; border-radius:10px; padding:15px; margin-bottom:12px; }
    .small { color:#666; font-size:13px; }
    input { width:100%; padding:10px; margin-top:8px; }
    button { padding:10px 14px; margin-top:10px; }
    .msg { margin: 10px 0; }
  </style>
</head>
<body>

<h2>Password Reset Requests (Admin)</h2>
<a href="dashboard.php">← Back to Dashboard</a>

<p class="msg"><?php echo htmlspecialchars($msg); ?></p>

<?php if($requests->num_rows === 0): ?>
  <p>No pending reset requests.</p>
<?php else: ?>
  <?php while($r = $requests->fetch_assoc()): ?>
    <div class="card">
      <div><b>Patient:</b> <?php echo htmlspecialchars($r["name"]); ?> (<?php echo htmlspecialchars($r["email"]); ?>)</div>
      <div class="small"><b>Requested:</b> <?php echo htmlspecialchars($r["created_at"]); ?></div>
      <div class="small"><b>Reason:</b> <?php echo htmlspecialchars($r["reason"] ?: "Not provided"); ?></div>

      <form method="POST">
        <input type="hidden" name="req_id" value="<?php echo (int)$r["id"]; ?>">
        <label>New Password</label>
        <input type="text" name="new_password" placeholder="Enter new password" required>
        <button type="submit">Reset Password</button>
      </form>
    </div>
  <?php endwhile; ?>
<?php endif; ?>

</body>
</html>


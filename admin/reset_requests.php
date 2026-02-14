<?php
session_start();
require_once __DIR__ . "/../config/db.php";

// only admin
if (!isset($_SESSION["user_id"]) || ($_SESSION["role"] ?? "") !== "admin") {
  header("Location: ../public/signin.php");
  exit();
}

$msg = "";
$msgType = ""; // success | error

// handle reset action
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $req_id = (int)($_POST["req_id"] ?? 0);
  $new_pass = $_POST["new_password"] ?? "";

  if ($req_id <= 0 || strlen($new_pass) < 4) {
    $msg = "Enter a valid new password (min 4 characters).";
    $msgType = "error";
  } else {
    // get user_id from request
    $stmt = $conn->prepare("SELECT user_id FROM password_reset_requests WHERE id=? AND status='pending' LIMIT 1");
    $stmt->bind_param("i", $req_id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows !== 1) {
      $msg = "Request not found or already handled.";
      $msgType = "error";
    } else {
      $row = $res->fetch_assoc();
      $user_id = (int)$row["user_id"];

      // update password
      $hash = password_hash($new_pass, PASSWORD_DEFAULT);
      $stmt2 = $conn->prepare("UPDATE users SET password_hash=? WHERE id=? AND role='patient'");
      $stmt2->bind_param("si", $hash, $user_id);
      $stmt2->execute();
      $stmt2->close();

      // mark request done
      $stmt3 = $conn->prepare("UPDATE password_reset_requests SET status='done' WHERE id=?");
      $stmt3->bind_param("i", $req_id);
      $stmt3->execute();
      $stmt3->close();

      // notify patient
      $note = "Your password has been reset by admin. Please login with your new password.";
      $stmt4 = $conn->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
      $stmt4->bind_param("is", $user_id, $note);
      $stmt4->execute();
      $stmt4->close();

      $msg = "Password reset successfully!";
      $msgType = "success";
    }

    $stmt->close();
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
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Password Reset Requests</title>
  <link rel="stylesheet" href="../assests/app.css">
</head>
<body>

<header class="admin-nav">
  <div class="container admin-nav-inner">
    <a class="admin-brand" href="dashboard.php">
      <img src="../assests/images/pms.png" alt="PMS Logo" class="logo">
      <span>PMS</span>
      <span class="admin-badge">Admin</span>
    </a>

    <nav class="admin-links" aria-label="Admin navigation">
      <a class="admin-link" href="dashboard.php">Dashboard</a>
      <a class="admin-link" href="approve_patients.php">Approve Patients</a>
      <a class="admin-link" href="manage_appointments.php">Appointments</a>
      <a class="admin-link" href="upload_report.php">Upload Report</a>
      <a class="admin-link is-active" href="reset_requests.php">Password Resets</a>
      <a class="btn btn-ghost" href="../public/logout.php">Logout</a>
    </nav>
  </div>
</header>

<main class="container admin-page">
  <div class="admin-head">
    <div>
      <h1 class="admin-title">Password Reset Requests</h1>
      <p class="admin-sub">Set a new password for patients who requested a reset.</p>
    </div>

    <div class="admin-head-actions">
      <a class="btn btn-ghost" href="dashboard.php">← Back to Dashboard</a>
    </div>
  </div>

  <?php if (!empty($msg)): ?>
    <div class="pr-alert <?php echo ($msgType === "success") ? "success" : "error"; ?>" role="status" aria-live="polite">
      <?php echo htmlspecialchars($msg); ?>
    </div>
  <?php endif; ?>

  <?php if(!$requests || $requests->num_rows === 0): ?>
    <div class="admin-empty" style="margin:0;">
      <div class="admin-empty-ico" aria-hidden="true">🔑</div>
      <div>
        <h3 style="margin:0 0 4px;">No pending reset requests</h3>
        <p class="muted" style="margin:0;">New requests will appear here.</p>
      </div>
    </div>
  <?php else: ?>

    <section class="pr-list" aria-label="Password reset requests list">
      <?php while($r = $requests->fetch_assoc()): ?>
        <article class="pr-card">
          <div class="pr-top">
            <div class="pr-user">
              <div class="pr-ico" aria-hidden="true">👤</div>
              <div>
                <div class="pr-name"><?php echo htmlspecialchars($r["name"]); ?></div>
                <div class="pr-email muted"><?php echo htmlspecialchars($r["email"]); ?></div>
              </div>
            </div>

            <div class="pr-meta">
              <span class="pill pill-pending">Pending</span>
              <div class="muted pr-time"><?php echo htmlspecialchars($r["created_at"]); ?></div>
            </div>
          </div>

          <div class="pr-reason">
            <span class="pr-label">Reason</span>
            <div class="pr-value"><?php echo htmlspecialchars($r["reason"] ?: "Not provided"); ?></div>
          </div>

          <form method="POST" class="pr-form">
            <input type="hidden" name="req_id" value="<?php echo (int)$r["id"]; ?>">

            <div class="pr-form-grid">
              <div class="field" style="margin:0;">
                <label class="label" for="np<?php echo (int)$r["id"]; ?>">New password</label>
                <input class="input" id="np<?php echo (int)$r["id"]; ?>" type="text" name="new_password"
                       placeholder="Enter new password (min 4 chars)" minlength="4" required>
              </div>

              <div class="pr-actions">
                <button class="btn btn-primary" type="submit">Reset Password</button>
              </div>
            </div>

            <div class="muted pr-hint">
              Tip: Share the password securely. Patient will receive a notification in the portal.
            </div>
          </form>
        </article>
      <?php endwhile; ?>
    </section>

  <?php endif; ?>

</main>
</body>
</html>



<?php
session_set_cookie_params([
  "lifetime" => 0,
  "path" => "/carecloud/",
  "httponly" => true,
  "samesite" => "Lax"
]);

session_start();
require_once "../config/db.php";

$active = "dashboard";
$pageTitle = "Dashboard";

// ✅ only patient can access
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "patient") {
  header("Location: ../public/signin.php");
  exit();
}

$uid = (int)$_SESSION["user_id"];

// ✅ recent notifications
$stmt = $conn->prepare("
  SELECT message, created_at
  FROM notifications
  WHERE user_id=?
  ORDER BY created_at DESC
  LIMIT 10
");
$stmt->bind_param("i", $uid);
$stmt->execute();
$notifs = $stmt->get_result();
$stmt->close();

// ✅ optional quick counts (won't break if tables missing)
$appointmentsCount = null;
$reportsCount = null;

try {
  if ($s = $conn->prepare("SELECT COUNT(*) c FROM appointments WHERE user_id=?")) {
    $s->bind_param("i", $uid);
    $s->execute();
    $appointmentsCount = (int)($s->get_result()->fetch_assoc()['c'] ?? 0);
    $s->close();
  }
  if ($s = $conn->prepare("SELECT COUNT(*) c FROM reports WHERE user_id=?")) {
    $s->bind_param("i", $uid);
    $s->execute();
    $reportsCount = (int)($s->get_result()->fetch_assoc()['c'] ?? 0);
    $s->close();

    
  }
} catch (Throwable $e) {
  // ignore
}

// require_once "nav.php";
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Patient Dashboard</title>
  <link rel="stylesheet" href="../assests/app.css">
</head>
<body>

<main class="container dash2">

  <header class="dash2-head">
    <div class="dash2-title-wrap">
      <h1 class="dash2-title">Hi, <?php echo htmlspecialchars($_SESSION["name"]); ?> 👋</h1>
      <p class="dash2-sub">Manage your appointments, reports, and updates.</p>
    </div>

    <div class="dash2-actions">
      <a class="btn btn-primary" href="request_appointment.php">Request appointment</a>
      <a class="btn btn-ghost" href="../public/logout.php">Logout</a>
    </div>
  </header>

  <section class="dash2-kpis">
    <div class="kpi">
      <div class="kpi-top">
        <span class="kpi-ico" aria-hidden="true">📅</span>
        <span class="kpi-label">Appointments</span>
      </div>
      <div class="kpi-value"><?php echo ($appointmentsCount === null) ? "—" : $appointmentsCount; ?></div>
      <a class="kpi-link" href="my_appointments.php">Open →</a>
    </div>

    <div class="kpi">
      <div class="kpi-top">
        <span class="kpi-ico" aria-hidden="true">📄</span>
        <span class="kpi-label">Reports</span>
      </div>
      <div class="kpi-value"><?php echo ($reportsCount === null) ? "—" : $reportsCount; ?></div>
      <a class="kpi-link" href="reports.php">View →</a>
    </div>

    <div class="kpi">
      <div class="kpi-top">
        <span class="kpi-ico" aria-hidden="true">🔔</span>
        <span class="kpi-label">Notifications</span>
      </div>
      <div class="kpi-value"><?php echo $notifs->num_rows; ?></div>
      <a class="kpi-link" href="notifications.php">All →</a>
    </div>
  </section>

  <section class="dash2-grid">
    <div class="panel2">
      <div class="panel2-head">
        <h2>Quick actions</h2>
        <p class="muted">Shortcuts to common tasks.</p>
      </div>

      <div class="quick-actions">
        <a class="qa" href="request_appointment.php">
          <span class="qa-ico" aria-hidden="true">➕</span>
          <span class="qa-text">
            <strong>Request appointment</strong>
            <span class="muted">Send a new request</span>
          </span>
        </a>

        <a class="qa" href="my_appointments.php">
          <span class="qa-ico" aria-hidden="true">🗓️</span>
          <span class="qa-text">
            <strong>My appointments</strong>
            <span class="muted">Track status & history</span>
          </span>
        </a>

        <a class="qa" href="reports.php">
          <span class="qa-ico" aria-hidden="true">📁</span>
          <span class="qa-text">
            <strong>My reports</strong>
            <span class="muted">View uploaded reports</span>
          </span>
        </a>
      </div>

      <div class="note">
        <strong>Tip:</strong> After you request an appointment, check “My appointments” to see approval status.
      </div>
    </div>

    <div class="panel2">
      <div class="panel2-head">
        <h2>Recent notifications</h2>
        <p class="muted">Latest 10 updates.</p>
      </div>

      <?php if ($notifs->num_rows === 0): ?>
        <div class="empty2">
          <span class="empty2-ico" aria-hidden="true">📭</span>
          <div>
            <strong>No notifications yet</strong>
            <div class="muted" style="margin-top:4px;">Updates will appear here when admin responds.</div>
          </div>
        </div>
      <?php else: ?>
        <div class="notif2">
          <?php while($n = $notifs->fetch_assoc()): ?>
            <div class="notif2-item">
              <span class="notif2-dot" aria-hidden="true"></span>
              <div class="notif2-body">
                <div class="notif2-msg"><?php echo htmlspecialchars($n["message"]); ?></div>
                <div class="notif2-time"><?php echo htmlspecialchars($n["created_at"]); ?></div>
              </div>
            </div>
          <?php endwhile; ?>
        </div>
      <?php endif; ?>
    </div>
  </section>

</main>

<?php
include "footer.php";
?>
</body>
</html>



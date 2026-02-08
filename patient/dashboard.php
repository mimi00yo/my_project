<?php
$active = "dashboard";
$pageTitle = "Dashboard";
require_once "nav.php";

session_set_cookie_params([
  "lifetime" => 0,
  "path" => "/carecloud/",
  "httponly" => true,
  "samesite" => "Lax"
]);


session_start();
require_once "../config/db.php";

// ✅ only patient can access
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "patient") {
    header("Location: ../public/signin.php");
    exit();
}

$uid = $_SESSION["user_id"];

// fetch latest notifications
$stmt = $conn->prepare("SELECT message, created_at FROM notifications WHERE user_id=? ORDER BY created_at DESC LIMIT 10");
$stmt->bind_param("i", $uid);
$stmt->execute();
$notifs = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
  <title>Patient Dashboard</title>
  <style>
    body { font-family: Arial; padding: 25px; }
    .box { border:1px solid #ddd; padding:15px; border-radius:10px; margin-top:15px; }
    .notif { padding:10px; border-bottom:1px solid #eee; }
    .small { color:#666; font-size:13px; }
    a { display:inline-block; margin-right:12px; }
  </style>
</head>
<body>

<h2>Welcome, <?php echo htmlspecialchars($_SESSION["name"]); ?> ✨</h2>

<!-- <a href="../public/logout.php">🚪 Logout</a>
<a href="request_appointment.php">📅 Request Appointment</a>
<a href="my_appointments.php">📋 My Appointments</a>
<a href="reports.php">📄 My Reports</a>
<a href="notifications.php">🔔 Notifications</a> -->
<!-- Next step we will add appointment request link here -->
<div class="cards">
  <div class="card-mini">
    <h3>Upcoming Appointments</h3>
    <p>—</p>
  </div>
  <div class="card-mini">
    <h3>Reports Available</h3>
    <p>—</p>
  </div>
  <div class="card-mini">
    <h3>Notifications</h3>
    <p>—</p>
  </div>
</div>

<div class="section-box">
  <h2 style="margin:0 0 8px; font-size:16px;">Quick Actions</h2>
  <div style="display:flex; gap:10px; flex-wrap:wrap;">
    <a class="btn btn-primary" href="request_appointment.php">Request Appointment</a>
    <a class="btn btn-ghost" href="my_appointments.php">View My Appointments</a>
    <a class="btn btn-ghost" href="reports.php">View Reports</a>
  </div>
</div>

<div class="section-box">
  <h2 style="margin:0 0 8px; font-size:16px;">Tips</h2>
  <p style="margin:0;">
    Use “Request Appointment” to send a request. You will see approval status in “My Appointments”.
  </p>
</div>


<div class="box">
  <h3>Notifications</h3>
  <?php if ($notifs->num_rows === 0): ?>
    <p class="small">No notifications yet.</p>
  <?php else: ?>
    <?php while($n = $notifs->fetch_assoc()): ?>
      <div class="notif">
        <?php echo htmlspecialchars($n["message"]); ?><br>
        <span class="small"><?php echo htmlspecialchars($n["created_at"]); ?></span>
      </div>
    <?php endwhile; ?>
  <?php endif; ?>
</div>

</body>
</html>

<?php include "footer.php"; ?>
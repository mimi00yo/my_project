<?php
session_set_cookie_params([
  "lifetime" => 0,
  "path" => "/carecloud/",
  "httponly" => true,
  "samesite" => "Lax"
]);

session_start();
require_once "../config/db.php";

// ✅ only admin can access
if (!isset($_SESSION["user_id"]) || ($_SESSION["role"] ?? "") !== "admin") {
  header("Location: ../public/signin.php");
  exit();
}

/* =========================
   Dashboard counts (DB data)
   ========================= */

// Total patients (role = patient)
$totalPatients = 0;
$res = $conn->query("SELECT COUNT(*) AS c FROM users WHERE role='patient'");
if ($res) { $totalPatients = (int)$res->fetch_assoc()["c"]; }

// Pending approvals (patients with status = pending)
$pendingApprovals = 0;
$res = $conn->query("SELECT COUNT(*) AS c FROM users WHERE role='patient' AND status='pending'");
if ($res) { $pendingApprovals = (int)$res->fetch_assoc()["c"]; }

// Today's appointments (approved + scheduled today)
$todaysAppointments = 0;
$res = $conn->query("SELECT COUNT(*) AS c FROM appointments WHERE status='approved' AND scheduled_date = CURDATE()");
if ($res) { $todaysAppointments = (int)$res->fetch_assoc()["c"]; }

// Total reports uploaded
$totalReports = 0;
$res = $conn->query("SELECT COUNT(*) AS c FROM reports");
if ($res) { $totalReports = (int)$res->fetch_assoc()["c"]; }
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Admin Dashboard</title>
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
      <a class="admin-link is-active" href="dashboard.php">Dashboard</a>
      <a class="admin-link" href="approve_patients.php">Approve Patients</a>
      <a class="admin-link" href="manage_appointments.php">Appointments</a>
      <a class="admin-link" href="reset_requests.php">Password Resets</a>
      <a class="btn btn-ghost" href="../public/logout.php">Logout</a>
    </nav>
  </div>
</header>

<main class="container admin-page">
  <div class="admin-head">
    <div>
      <h1 class="admin-title">Admin Dashboard</h1>
      <p class="admin-sub">Quick overview of patients, approvals, appointments, and reports.</p>
    </div>

    <div class="admin-head-actions">
      <a class="btn btn-primary" href="approve_patients.php">Review approvals</a>
      <a class="btn btn-ghost" href="manage_appointments.php">Manage appointments</a>
    </div>
  </div>

  <section class="admin-stats" aria-label="Dashboard stats">
    <div class="admin-stat">
      <div class="admin-stat-top">
        <div class="admin-stat-ico" aria-hidden="true">👥</div>
        <div class="admin-stat-meta">
          <div class="admin-stat-title">Total Patients</div>
          <div class="admin-stat-sub">Registered users</div>
        </div>
      </div>
      <div class="admin-stat-value"><?php echo $totalPatients; ?></div>
    </div>

    <div class="admin-stat admin-stat-warn">
      <div class="admin-stat-top">
        <div class="admin-stat-ico" aria-hidden="true">⏳</div>
        <div class="admin-stat-meta">
          <div class="admin-stat-title">Pending Approvals</div>
          <div class="admin-stat-sub">Need your action</div>
        </div>
      </div>
      <div class="admin-stat-value"><?php echo $pendingApprovals; ?></div>
      <a class="admin-stat-link" href="approve_patients.php">Open approvals →</a>
    </div>

    <div class="admin-stat">
      <div class="admin-stat-top">
        <div class="admin-stat-ico" aria-hidden="true">📅</div>
        <div class="admin-stat-meta">
          <div class="admin-stat-title">Today Appointments</div>
          <div class="admin-stat-sub">Scheduled today</div>
        </div>
      </div>
      <div class="admin-stat-value"><?php echo $todaysAppointments; ?></div>
    </div>

    <div class="admin-stat">
      <div class="admin-stat-top">
        <div class="admin-stat-ico" aria-hidden="true">📄</div>
        <div class="admin-stat-meta">
          <div class="admin-stat-title">Reports Uploaded</div>
          <div class="admin-stat-sub">Total reports</div>
        </div>
      </div>
      <div class="admin-stat-value"><?php echo $totalReports; ?></div>
    </div>
  </section>

  <section class="admin-panels">
    <div class="admin-panel">
      <h2>Next steps</h2>
      <p class="muted">Common admin tasks.</p>

      <div class="admin-quick">
        <a class="admin-quick-item" href="approve_patients.php">
          <span class="admin-quick-ico" aria-hidden="true">✅</span>
          <span>
            <strong>Approve patients</strong>
            <span class="muted">Verify & approve pending accounts</span>
          </span>
        </a>

        <a class="admin-quick-item" href="manage_appointments.php">
          <span class="admin-quick-ico" aria-hidden="true">🗓️</span>
          <span>
            <strong>Manage appointments</strong>
            <span class="muted">Approve, schedule, or reject requests</span>
          </span>
        </a>

        <a class="admin-quick-item" href="upload_report.php">
          <span class="admin-quick-ico" aria-hidden="true">⬆️</span>
          <span>
            <strong>Upload reports</strong>
            <span class="muted">Add patient reports and notify them</span>
          </span>
        </a>

        <a class="admin-quick-item" href="reset_requests.php">
  <span class="admin-quick-ico" aria-hidden="true">🔑</span>
  <span>
    <strong>Password resets</strong>
    <span class="muted">Handle forgot-password requests</span>
  </span>
</a>

      </div>
    </div>

    <div class="admin-panel admin-note">
      <h2>Tip</h2>
      <p class="muted" style="margin:0;">
        Keep pending approvals low. Patients can’t request appointments until they’re approved.
      </p>
    </div>
  </section>
</main>

</body>
</html>



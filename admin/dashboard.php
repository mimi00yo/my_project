<?php
session_set_cookie_params([
  "lifetime" => 0,
  "path" => "/carecloud/",
  "httponly" => true,
  "samesite" => "Lax"
]);

session_start();require_once "../config/db.php"; // ✅ connect DB

// ✅ only admin can access
if (!isset($_SESSION["user_id"]) || ($_SESSION["role"] ?? "") !== "admin") {
  header("Location: ../public/signin.php");
  exit();
}

/* =========================
   Dashboard counts (DB data)
   Tables used in your project:
   - users (role, status)
   - appointments (status, scheduled_date)
   - reports
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
$res = $conn->query("SELECT COUNT(*) AS c
                     FROM appointments
                     WHERE status='approved' AND scheduled_date = CURDATE()");
if ($res) { $todaysAppointments = (int)$res->fetch_assoc()["c"]; }

// Total reports uploaded
$totalReports = 0;
$res = $conn->query("SELECT COUNT(*) AS c FROM reports");
if ($res) { $totalReports = (int)$res->fetch_assoc()["c"]; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard</title>

  <!-- ✅ Your main UI file -->
  <link rel="stylesheet" href="../assests/app.css">
</head>
<body>

  <!-- ✅ Admin Navbar (same links as your admin pages) -->
  <div class="topbar-wrap">
    <div class="topbar">
      <div class="brand">CareCloud <span class="badge">Admin</span></div>

      <div class="nav">
        <a href="dashboard.php">Dashboard</a>
        <a href="approve_patients.php">Approve Patients</a>
        <a href="manage_appointments.php">Appointments</a>
        <a class="btn btn-danger" href="../public/logout.php">Logout</a>
      </div>
    </div>
  </div>

  <main class="wrapper">

    <div class="pagehead">
      <div>
        <h1>Admin Dashboard</h1>
        <p>Quick overview of patients, approvals, appointments, and reports.</p>
      </div>
    </div>

    <!-- ✅ Stats Cards (UI from the CSS you pasted) -->
    <div class="stats-grid">

      <div class="card stat-card">
        <div class="stat-left">
          <div class="stat-title">Total Patients</div>
          <div class="stat-value"><?php echo $totalPatients; ?></div>
          <div class="stat-sub">Registered users</div>
        </div>
        <div class="stat-icon">👥</div>
      </div>

      <div class="card stat-card">
        <div class="stat-left">
          <div class="stat-title">Pending Approvals</div>
          <div class="stat-value"><?php echo $pendingApprovals; ?></div>
          <div class="stat-sub">Need your action</div>
        </div>
        <div class="stat-icon">⏳</div>
      </div>

      <div class="card stat-card">
        <div class="stat-left">
          <div class="stat-title">Today Appointments</div>
          <div class="stat-value"><?php echo $todaysAppointments; ?></div>
          <div class="stat-sub">Scheduled today</div>
        </div>
        <div class="stat-icon">📅</div>
      </div>

      <div class="card stat-card">
        <div class="stat-left">
          <div class="stat-title">Reports Uploaded</div>
          <div class="stat-value"><?php echo $totalReports; ?></div>
          <div class="stat-sub">Total reports</div>
        </div>
        <div class="stat-icon">📄</div>
      </div>

    </div>

  </main>

</body>
</html>

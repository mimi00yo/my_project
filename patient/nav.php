<?php
// patient/nav.php
// Set $active before including this file (example: $active = "dashboard";)
if (!isset($active)) { $active = ""; }
if (!isset($pageTitle)) { $pageTitle = "Patient"; }
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?php echo htmlspecialchars($pageTitle); ?></title>
  <link rel="stylesheet" href="../assests/app.css">
</head>
<body>

<div class="dash">
  <!-- SIDEBAR -->
  <aside class="sidebar">
    <a class="brand" href="dashboard.php">
      <span class="logo" aria-hidden="true"></span>
      <span>Patient Panel</span>
    </a>

    <a class="menu <?php echo $active==='dashboard'?'active':''; ?>" href="dashboard.php">🏠 Dashboard</a>
    <a class="menu <?php echo $active==='request'?'active':''; ?>" href="request_appointment.php">📅 Request Appointment</a>
    <a class="menu <?php echo $active==='myapps'?'active':''; ?>" href="my_appointments.php">🗓️ My Appointments</a>
    <a class="menu <?php echo $active==='reports'?'active':''; ?>" href="reports.php">🧾 Reports</a>
    <a class="menu <?php echo $active==='notif'?'active':''; ?>" href="notifications.php">🔔 Notifications</a>

    <div style="height:12px;"></div>
    <a class="menu" href="../public/logout.php">🚪 Logout</a>
  </aside>

  <!-- MAIN -->
  <main class="main">
    <div class="topbar">
      <h1 class="page-title"><?php echo htmlspecialchars($pageTitle); ?></h1>
      <a class="btn btn-ghost" href="../index.php">Go to Home</a>
    </div>


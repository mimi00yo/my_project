<?php

session_set_cookie_params([
  "lifetime" => 0,
  "path" => "/carecloud/",
  "httponly" => true,
  "samesite" => "Lax"
]);


session_start();

// ✅ only admin can access
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../public/signin.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Admin Dashboard</title>
  <style>
    body { font-family: Arial; padding: 30px; }
    a { display:block; margin:10px 0; font-size:18px; }
  </style>
</head>
<body>
  <h2>Welcome Admin: <?php echo htmlspecialchars($_SESSION["name"]); ?></h2>

  <a href="approve_patients.php">✅ Approve Patients</a>
  <a href="manage_appointments.php">📅 Manage Appointments</a>
    <a href="upload_report.php">📄 Upload Report</a>
    <a href="view_reports.php">📋 View Reports</a>
    <a href="reset_requests.php">📂 Password Reset Requests</a>
  <a href="../public/logout.php">🚪 Logout</a>
</body>
</html>


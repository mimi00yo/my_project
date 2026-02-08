
<?php
session_start();require_once "../config/db.php";

// ✅ only patient
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "patient") {
    header("Location: ../public/signin.php");
    exit();
}

$pid = $_SESSION["user_id"];

$stmt = $conn->prepare("SELECT issue, requested_date, scheduled_date, status, admin_message, created_at
                        FROM appointments
                        WHERE patient_id=?
                        ORDER BY created_at DESC");
$stmt->bind_param("i", $pid);
$stmt->execute();
$apps = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
  <title>My Appointments</title>
  <style>
    body { font-family: Arial; padding: 25px; }
    .card { border:1px solid #ddd; border-radius:10px; padding:15px; margin-bottom:15px; }
    .small { color:#666; font-size:13px; }
    .approved { color:green; font-weight:bold; }
    .rejected { color:red; font-weight:bold; }
    .pending { color:orange; font-weight:bold; }
  </style>
</head>
<body>

<h2>My Appointments</h2>
<a href="dashboard.php">← Back to Dashboard</a>
<br><br>

<?php if($apps->num_rows === 0): ?>
  <p>No appointment requests yet.</p>
<?php else: ?>
  <?php while($a = $apps->fetch_assoc()): ?>
    <div class="card">
      <div class="small"><b>Requested on:</b> <?php echo htmlspecialchars($a["created_at"]); ?></div>

<div><b>Your Preferred Date:</b>
<?php echo htmlspecialchars($a["requested_date"] ?? "Not provided"); ?>
</div>

<div><b>Confirmed Appointment Date:</b>
<?php echo htmlspecialchars($a["scheduled_date"] ?? "Waiting for confirmation"); ?>
</div>

      <div><b>Issue:</b> <?php echo nl2br(htmlspecialchars($a["issue"])); ?></div>

      <div>
        <b>Status:</b>
        <span class="<?php echo htmlspecialchars($a["status"]); ?>">
          <?php echo htmlspecialchars($a["status"]); ?>
        </span>
      </div>

      <div class="small"><b>Admin Message:</b> <?php echo htmlspecialchars($a["admin_message"] ?: "None"); ?></div>
    </div>
  <?php endwhile; ?>
<?php endif; ?>

</body>
</html>



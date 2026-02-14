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
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" href="../assests/app.css">
</head>
<body>


<div class="container">


  <div class="topbar">
    <div>
      <h2>My Appointments</h2>
      <p class="subtitle">View your appointment requests and updates</p>
    </div>
    <a class="btn" href="dashboard.php">← Back to Dashboard</a>
  </div>


<?php if($apps->num_rows === 0): ?>
  <div class="empty">No appointment requests yet.</div>
<?php else: ?>


  <div class="list">
  <?php while($a = $apps->fetch_assoc()): ?>
    
    <div class="card">


      <div class="card-top">
        <div class="small"><b>Requested on:</b> <?php echo htmlspecialchars($a["created_at"]); ?></div>


        <div class="status-pill <?php echo htmlspecialchars($a["status"]); ?>">
          <?php echo htmlspecialchars($a["status"]); ?>
        </div>
      </div>


      <div class="grid">
        <div class="field">
          <span class="label">Your Preferred Date</span>
          <div class="value">
            <?php echo htmlspecialchars($a["requested_date"] ?? "Not provided"); ?>
          </div>
        </div>


        <div class="field">
          <span class="label">Confirmed Appointment Date</span>
          <div class="value">
            <?php echo htmlspecialchars($a["scheduled_date"] ?? "Waiting for confirmation"); ?>
          </div>
        </div>
      </div>


      <div class="issue">
        <span class="label">Issue</span>
        <div class="value">
          <?php echo nl2br(htmlspecialchars($a["issue"])); ?>
        </div>
      </div>


      <div class="admin">
        <b>Admin Message:</b>
        <?php echo htmlspecialchars($a["admin_message"] ?: "None"); ?>
      </div>


    </div>


  <?php endwhile; ?>
  </div>


<?php endif; ?>


</div>
</body>
</html>






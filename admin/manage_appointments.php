<?php

session_set_cookie_params([
  "lifetime" => 0,
  "path" => "/carecloud/",
  "httponly" => true,
  "samesite" => "Lax"
]);


session_start();
require_once "../config/db.php";

// ✅ only admin
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../public/signin.php");
    exit();
}

// ✅ handle approve/reject
if (isset($_POST["action"], $_POST["appt_id"])) {
    $action = $_POST["action"];              // approved or rejected
    $apptId = (int)$_POST["appt_id"];
    $scheduled_date = $_POST["scheduled_date"] ?? null;
    $adminMsg = trim($_POST["admin_message"]);

    if ($action === "approved" && empty($scheduled_date)) {
    // don't approve without final date
    header("Location: manage_appointments.php");
    exit();
}


    if ($action === "approved" || $action === "rejected") {

        // get patient_id first
        $stmt = $conn->prepare("SELECT patient_id FROM appointments WHERE id=? LIMIT 1");
        $stmt->bind_param("i", $apptId);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows === 1) {
            $row = $res->fetch_assoc();
            $patientId = (int)$row["patient_id"];

            // update appointment
           
             $stmt2 = $conn->prepare("UPDATE appointments SET status=?, admin_message=?, scheduled_date=? WHERE id=?");
             $stmt2->bind_param("sssi", $action, $adminMsg, $scheduled_date, $apptId);
             $stmt2->execute();



            // notification
           $note = "Your appointment request was $action."
           . ($action === "approved" ? " Appointment date: $scheduled_date." : "")
           . ($adminMsg ? " Message: $adminMsg" : "");



        }
    }

    header("Location: manage_appointments.php");
    exit();
}

// fetch appointments with patient info
$sql = "SELECT a.id, a.issue, a.requested_date, a.scheduled_date, a.status, a.admin_message, a.created_at,
               u.name AS patient_name, u.email AS patient_email
        FROM appointments a
        JOIN users u ON a.patient_id = u.id
        ORDER BY a.created_at DESC";
$appointments = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
  <title>Manage Appointments</title>
  <style>
    body { font-family: Arial; padding: 25px; }
    .card { border:1px solid #ddd; border-radius:10px; padding:15px; margin-bottom:15px; }
    .small { color:#666; font-size:13px; }
    textarea { width:100%; padding:10px; margin-top:10px; }
    button { padding:8px 12px; margin-top:10px; margin-right:10px; }
    .approved { color:green; font-weight:bold; }
    .rejected { color:red; font-weight:bold; }
    .pending { color:orange; font-weight:bold; }
  </style>
</head>
<body>

<h2>Manage Appointments</h2>
<a href="dashboard.php">← Back to Dashboard</a>

<?php while($a = $appointments->fetch_assoc()): ?>
  <div class="card">
    <div><b>Patient:</b> <?php echo htmlspecialchars($a["patient_name"]); ?> (<?php echo htmlspecialchars($a["patient_email"]); ?>)</div>
    <div class="small"><b>Requested on:</b> <?php echo htmlspecialchars($a["created_at"]); ?></div>

<div><b>Preferred Date (Patient Choice):</b>
<?php echo htmlspecialchars($a["requested_date"] ?? "Not provided"); ?>
</div>

<div><b>Final Appointment Date (Confirmed):</b>
<?php echo htmlspecialchars($a["scheduled_date"] ?? "Not scheduled yet"); ?>
</div>


    <div><b>Issue:</b> <?php echo nl2br(htmlspecialchars($a["issue"])); ?></div>

    <div>
      <b>Status:</b>
      <span class="<?php echo htmlspecialchars($a["status"]); ?>">
        <?php echo htmlspecialchars($a["status"]); ?>
      </span>
    </div>

    <?php if ($a["status"] === "pending"): ?>
      <form method="POST">
        <input type="hidden" name="appt_id" value="<?php echo (int)$a["id"]; ?>">

        <label>Final Appointment Date (required for approve)</label>
        <input type="date" name="scheduled_date">

        <label>Admin Message (optional)</label>
        <textarea name="admin_message" placeholder="Write message for patient..."></textarea>

        <button type="submit" name="action" value="approved">Approve</button>
        <button type="submit" name="action" value="rejected">Reject</button>
      </form>
    <?php else: ?>
      <div class="small"><b>Admin Message:</b> <?php echo htmlspecialchars($a["admin_message"] ?: "None"); ?></div>
    <?php endif; ?>
  </div>
<?php endwhile; ?>

</body>
</html>



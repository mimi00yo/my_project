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
if (!isset($_SESSION["user_id"]) || ($_SESSION["role"] ?? "") !== "admin") {
  header("Location: ../public/signin.php");
  exit();
}

// ✅ handle approve/reject
if (isset($_POST["action"], $_POST["appt_id"])) {
  $action = $_POST["action"];              // approved or rejected
  $apptId = (int)$_POST["appt_id"];
  $scheduled_date = $_POST["scheduled_date"] ?? null;
  $adminMsg = trim($_POST["admin_message"] ?? "");

  if ($action === "approved" && empty($scheduled_date)) {
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
      $stmt2->close();

      // ✅ notification (insert into notifications table)
      $note = "Your appointment request was $action."
        . ($action === "approved" ? " Appointment date: $scheduled_date." : "")
        . ($adminMsg ? " Message: $adminMsg" : "");

      $stmt3 = $conn->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
      $stmt3->bind_param("is", $patientId, $note);
      $stmt3->execute();
      $stmt3->close();
    }

    $stmt->close();
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
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Manage Appointments</title>
  <link rel="stylesheet" href="../assests/app.css">
</head>
<body>

<header class="admin-nav">
  <div class="container admin-nav-inner">
    <a class="admin-brand" href="dashboard.php">
      <span class="logo" aria-hidden="true"></span>
      <span>CareCloud</span>
      <span class="admin-badge">Admin</span>
    </a>

    <nav class="admin-links" aria-label="Admin navigation">
      <a class="admin-link" href="dashboard.php">Dashboard</a>
      <a class="admin-link" href="approve_patients.php">Approve Patients</a>
      <a class="admin-link is-active" href="manage_appointments.php">Appointments</a>
      <a class="btn btn-ghost" href="../public/logout.php">Logout</a>
    </nav>
  </div>
</header>

<main class="container admin-page">
  <div class="admin-head">
    <div>
      <h1 class="admin-title">Manage Appointments</h1>
      <p class="admin-sub">Approve, schedule, or reject appointment requests.</p>
    </div>

    <div class="admin-head-actions">
      <a class="btn btn-ghost" href="dashboard.php">← Back to Dashboard</a>
    </div>
  </div>

  <?php if (!$appointments || $appointments->num_rows === 0): ?>
    <div class="admin-empty" style="margin:0;">
      <div class="admin-empty-ico" aria-hidden="true">📅</div>
      <div>
        <h3 style="margin:0 0 4px;">No appointment requests</h3>
        <p class="muted" style="margin:0;">Requests will appear here when patients submit them.</p>
      </div>
    </div>
  <?php else: ?>

    <section class="appt-list" aria-label="Appointments list">
      <?php while($a = $appointments->fetch_assoc()): ?>
        <?php $status = strtolower($a["status"] ?? "pending"); ?>

        <article class="appt-card">
          <div class="appt-top">
            <div class="appt-patient">
              <div class="appt-avatar" aria-hidden="true">👤</div>
              <div>
                <div class="appt-name"><?php echo htmlspecialchars($a["patient_name"]); ?></div>
                <div class="appt-email muted"><?php echo htmlspecialchars($a["patient_email"]); ?></div>
              </div>
            </div>

            <div class="appt-right">
              <span class="pill <?php echo ($status === "approved") ? "pill-approved" : (($status === "rejected") ? "pill-rejected" : "pill-pending"); ?>">
                <?php echo htmlspecialchars($a["status"]); ?>
              </span>
              <div class="appt-date muted">
                Requested: <?php echo htmlspecialchars($a["created_at"]); ?>
              </div>
            </div>
          </div>

          <div class="appt-grid">
            <div class="appt-field">
              <span class="appt-label">Preferred date</span>
              <div class="appt-value"><?php echo htmlspecialchars($a["requested_date"] ?? "Not provided"); ?></div>
            </div>
            <div class="appt-field">
              <span class="appt-label">Final scheduled date</span>
              <div class="appt-value"><?php echo htmlspecialchars($a["scheduled_date"] ?? "Not scheduled yet"); ?></div>
            </div>
          </div>

          <div class="appt-issue">
            <span class="appt-label">Issue</span>
            <div class="appt-value"><?php echo nl2br(htmlspecialchars($a["issue"])); ?></div>
          </div>

          <?php if ($status === "pending"): ?>
            <form class="appt-form" method="POST">
              <input type="hidden" name="appt_id" value="<?php echo (int)$a["id"]; ?>">

              <div class="appt-form-grid">
                <div class="appt-form-field">
                  <label class="appt-label" for="d<?php echo (int)$a["id"]; ?>">Final Appointment Date (required)</label>
                  <input class="input" id="d<?php echo (int)$a["id"]; ?>" type="date" name="scheduled_date" required>
                </div>

                <div class="appt-form-field">
                  <label class="appt-label" for="m<?php echo (int)$a["id"]; ?>">Admin Message (optional)</label>
                  <textarea class="input" id="m<?php echo (int)$a["id"]; ?>" name="admin_message" rows="3" placeholder="Write message for patient..."></textarea>
                </div>
              </div>

              <div class="appt-actions">
                <button class="btn btn-primary" type="submit" name="action" value="approved">Approve</button>
                <button class="btn btn-ghost" type="submit" name="action" value="rejected">Reject</button>
              </div>
            </form>
          <?php else: ?>
            <div class="appt-admin">
              <span class="appt-label">Admin message</span>
              <div class="appt-value"><?php echo htmlspecialchars($a["admin_message"] ?: "None"); ?></div>
            </div>
          <?php endif; ?>

        </article>
      <?php endwhile; ?>
    </section>

  <?php endif; ?>

</main>
</body>
</html>



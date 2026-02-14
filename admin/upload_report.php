<?php
session_start();
require_once __DIR__ . "/../config/db.php";

// only admin
if (!isset($_SESSION["user_id"]) || ($_SESSION["role"] ?? "") !== "admin") {
  header("Location: ../public/signin.php");
  exit();
}

$msg = "";
$msgType = ""; // success | error

// Fetch approved patients for dropdown
$patients = $conn->query("SELECT id, name, email FROM users WHERE role='patient' AND status='approved' ORDER BY name ASC");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $patient_id = (int)($_POST["patient_id"] ?? 0);
  $title = trim($_POST["title"] ?? "");

  if ($patient_id <= 0 || $title === "") {
    $msg = "Please select a patient and enter a report title.";
    $msgType = "error";
  } elseif (!isset($_FILES["report_file"]) || $_FILES["report_file"]["error"] !== UPLOAD_ERR_OK) {
    $msg = "Please choose a file to upload.";
    $msgType = "error";
  } else {
    $fileTmp  = $_FILES["report_file"]["tmp_name"];
    $fileName = $_FILES["report_file"]["name"];
    $fileSize = (int)$_FILES["report_file"]["size"];

    $allowedExt = ["pdf", "jpg", "jpeg", "png"];
    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if (!in_array($ext, $allowedExt, true)) {
      $msg = "Only PDF, JPG, JPEG, PNG files are allowed.";
      $msgType = "error";
    } elseif ($fileSize > 5 * 1024 * 1024) {
      $msg = "File too large. Max size is 5MB.";
      $msgType = "error";
    } else {
      $newName = "report_" . $patient_id . "_" . time() . "." . $ext;

      // folder paths
      $uploadDirFull = __DIR__ . "/../uploads/reports/";   // absolute path
      $filePathRelative = "uploads/reports/" . $newName;   // stored in DB

      if (!is_dir($uploadDirFull)) {
        mkdir($uploadDirFull, 0777, true);
      }

      if (move_uploaded_file($fileTmp, $uploadDirFull . $newName)) {

        // save DB record
        $stmt = $conn->prepare("INSERT INTO reports (patient_id, title, file_path, uploaded_by) VALUES (?, ?, ?, 'admin')");
        $stmt->bind_param("iss", $patient_id, $title, $filePathRelative);

        if ($stmt->execute()) {
          // notify patient
          $note = "A new medical report was uploaded: " . $title;
          $stmt2 = $conn->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
          $stmt2->bind_param("is", $patient_id, $note);
          $stmt2->execute();
          $stmt2->close();

          $msg = "Report uploaded successfully!";
          $msgType = "success";
        } else {
          $msg = "DB error while saving report.";
          $msgType = "error";
        }

        $stmt->close();
      } else {
        $msg = "Upload failed. Check uploads/reports folder permissions.";
        $msgType = "error";
      }
    }
  }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Upload Report</title>
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
      <a class="admin-link" href="manage_appointments.php">Appointments</a>
      <a class="admin-link is-active" href="upload_report.php">Upload Report</a>
      <a class="btn btn-ghost" href="../public/logout.php">Logout</a>
    </nav>
  </div>
</header>

<main class="container admin-page">
  <div class="admin-head">
    <div>
      <h1 class="admin-title">Upload Medical Report</h1>
      <p class="admin-sub">Upload a report and automatically notify the patient.</p>
    </div>

    <div class="admin-head-actions">
      <a class="btn btn-ghost" href="dashboard.php">← Back to Dashboard</a>
    </div>
  </div>

  <section class="up-card">
    <div class="up-card-head">
      <div>
        <h2>Report details</h2>
        <p class="muted">Allowed: PDF/JPG/JPEG/PNG · Max 5MB</p>
      </div>
    </div>

    <div class="up-card-body">

      <?php if (!empty($msg)): ?>
        <div class="up-alert <?php echo ($msgType === "success") ? "success" : "error"; ?>" role="status" aria-live="polite">
          <?php echo htmlspecialchars($msg); ?>
        </div>
      <?php endif; ?>

      <form method="POST" enctype="multipart/form-data" class="up-form">

        <div class="field">
          <label class="label" for="patient_id">Select Patient</label>
          <select class="input" id="patient_id" name="patient_id" required>
            <option value="">-- Select Patient --</option>
            <?php while($p = $patients->fetch_assoc()): ?>
              <option value="<?php echo (int)$p["id"]; ?>">
                <?php echo htmlspecialchars($p["name"] . " (" . $p["email"] . ")"); ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>

        <div class="field">
          <label class="label" for="title">Report Title</label>
          <input class="input" id="title" type="text" name="title" placeholder="e.g., Blood Test Report" required>
        </div>

        <div class="field">
          <label class="label" for="report_file">Choose File</label>
          <input class="input" id="report_file" type="file" name="report_file" accept=".pdf,.jpg,.jpeg,.png" required>
          <div class="muted" style="font-size:12px; margin-top:6px;">
            Tip: Use PDF for multi-page reports.
          </div>
        </div>

        <div class="up-actions">
          <a class="btn btn-ghost" href="dashboard.php">Cancel</a>
          <button class="btn btn-primary" type="submit">Upload Report</button>
        </div>

      </form>
    </div>
  </section>

</main>

</body>
</html>



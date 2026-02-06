<?php
session_start();
require_once __DIR__ . "/../config/db.php";

// only admin
if (!isset($_SESSION["user_id"]) || ($_SESSION["role"] ?? "") !== "admin") {
    header("Location: ../public/signin.php");
    exit();
}

$msg = "";

// Fetch approved patients for dropdown
$patients = $conn->query("SELECT id, name, email FROM users WHERE role='patient' AND status='approved' ORDER BY name ASC");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $patient_id = (int)($_POST["patient_id"] ?? 0);
    $title = trim($_POST["title"] ?? "");

    if ($patient_id <= 0 || $title === "") {
        $msg = "❌ Please select patient and enter report title.";
    } elseif (!isset($_FILES["report_file"]) || $_FILES["report_file"]["error"] !== UPLOAD_ERR_OK) {
        $msg = "❌ Please choose a file to upload.";
    } else {
        $fileTmp  = $_FILES["report_file"]["tmp_name"];
        $fileName = $_FILES["report_file"]["name"];
        $fileSize = (int)$_FILES["report_file"]["size"];

        $allowedExt = ["pdf", "jpg", "jpeg", "png"];
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExt)) {
            $msg = "❌ Only PDF, JPG, JPEG, PNG are allowed.";
        } elseif ($fileSize > 5 * 1024 * 1024) {
            $msg = "❌ File too large. Max 5MB.";
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

                    $msg = "✅ Report uploaded successfully!";
                } else {
                    $msg = "❌ DB error while saving report.";
                }

            } else {
                $msg = "❌ Upload failed. Check uploads/reports folder permissions.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Upload Report</title>
  <style>
    body { font-family: Arial; padding: 25px; }
    label { display:block; margin-top:12px; }
    select, input { width: 100%; padding: 10px; margin-top:6px; }
    button { padding: 10px 16px; margin-top: 15px; }
    .msg { margin-top: 10px; }
  </style>
</head>
<body>

<h2>Upload Medical Report</h2>
<a href="dashboard.php">← Back to Dashboard</a>

<p class="msg"><?php echo htmlspecialchars($msg); ?></p>

<form method="POST" enctype="multipart/form-data">
  <label>Select Patient</label>
  <select name="patient_id" required>
    <option value="">-- Select Patient --</option>
    <?php while($p = $patients->fetch_assoc()): ?>
      <option value="<?php echo (int)$p["id"]; ?>">
        <?php echo htmlspecialchars($p["name"] . " (" . $p["email"] . ")"); ?>
      </option>
    <?php endwhile; ?>
  </select>

  <label>Report Title</label>
  <input type="text" name="title" placeholder="e.g., Blood Test Report" required>

  <label>Choose File (PDF/JPG/PNG)</label>
  <input type="file" name="report_file" required>

  <button type="submit">Upload</button>
</form>

</body>
</html>



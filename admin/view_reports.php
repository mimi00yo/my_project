<?php
session_start();
require_once __DIR__ . "/../config/db.php";

// only admin
if (!isset($_SESSION["user_id"]) || ($_SESSION["role"] ?? "") !== "admin") {
    header("Location: ../public/signin.php");
    exit();
}

// delete report (optional)
if (isset($_GET["delete"])) {
    $rid = (int)$_GET["delete"];

    // get file path first
    $stmt = $conn->prepare("SELECT file_path FROM reports WHERE id=? LIMIT 1");
    $stmt->bind_param("i", $rid);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $row = $res->fetch_assoc();
        $fileRel = $row["file_path"];                 // e.g. uploads/reports/abc.pdf
        $fileAbs = __DIR__ . "/../" . $fileRel;       // absolute path

        // delete db row
        $stmt2 = $conn->prepare("DELETE FROM reports WHERE id=?");
        $stmt2->bind_param("i", $rid);
        $stmt2->execute();

        // delete file from folder (if exists)
        if (file_exists($fileAbs)) {
            @unlink($fileAbs);
        }
    }

    header("Location: view_reports.php");
    exit();
}

// list reports with patient info
$sql = "SELECT r.id, r.title, r.file_path, r.uploaded_by, r.uploaded_at,
               u.name AS patient_name, u.email AS patient_email
        FROM reports r
        JOIN users u ON r.patient_id = u.id
        ORDER BY r.uploaded_at DESC";
$reports = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
  <title>All Reports (Admin)</title>
  <style>
    body { font-family: Arial; padding: 25px; }
    table { width:100%; border-collapse: collapse; margin-top: 12px; }
    th, td { border:1px solid #ddd; padding:10px; text-align:left; }
    th { background:#f3f3f3; }
    a { text-decoration:none; }
    .danger { color: #ef4444; font-weight: bold; }
    .small { color:#666; font-size:13px; }
  </style>
</head>
<body>

<h2>All Uploaded Reports</h2>
<a href="dashboard.php">← Back to Dashboard</a>

<table>
  <tr>
    <th>Patient</th>
    <th>Title</th>
    <th>Uploaded By</th>
    <th>Date</th>
    <th>File</th>
    <th>Action</th>
  </tr>

  <?php while($r = $reports->fetch_assoc()): ?>
    <tr>
      <td>
        <?php echo htmlspecialchars($r["patient_name"]); ?><br>
        <span class="small"><?php echo htmlspecialchars($r["patient_email"]); ?></span>
      </td>
      <td><?php echo htmlspecialchars($r["title"]); ?></td>
      <td><?php echo htmlspecialchars($r["uploaded_by"]); ?></td>
      <td class="small"><?php echo htmlspecialchars($r["uploaded_at"]); ?></td>
      <td>
        <a href="../<?php echo htmlspecialchars($r["file_path"]); ?>" target="_blank">View/Download</a>
      </td>
      <td>
        <a class="danger" href="?delete=<?php echo (int)$r["id"]; ?>"
           onclick="return confirm('Delete this report permanently?');">Delete</a>
      </td>
    </tr>
  <?php endwhile; ?>
</table>

</body>
</html>


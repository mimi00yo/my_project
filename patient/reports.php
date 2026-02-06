<?php
session_start();
require_once "../config/db.php";

// ✅ only patient
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "patient") {
    header("Location: ../public/signin.php");
    exit();
}

$pid = $_SESSION["user_id"];

$stmt = $conn->prepare("SELECT title, file_path, uploaded_by, uploaded_at
                        FROM reports
                        WHERE patient_id=?
                        ORDER BY uploaded_at DESC");
$stmt->bind_param("i", $pid);
$stmt->execute();
$reports = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
  <title>My Medical Reports</title>
  <style>
    body { font-family: Arial; padding: 25px; }
    table { border-collapse: collapse; width: 100%; margin-top: 15px; }
    th, td { border:1px solid #ccc; padding:10px; text-align:left; }
    th { background:#f3f3f3; }
    a { color: blue; }
    .small { color:#666; font-size:13px; }
  </style>
</head>
<body>

<h2>My Medical Reports</h2>
<a href="dashboard.php">← Back to Dashboard</a>

<?php if($reports->num_rows === 0): ?>
  <p>No reports uploaded yet.</p>
<?php else: ?>
  <table>
    <tr>
      <th>Title</th>
      <th>Uploaded By</th>
      <th>Date</th>
      <th>File</th>
    </tr>
    <?php while($r = $reports->fetch_assoc()): ?>
      <tr>
        <td><?php echo htmlspecialchars($r["title"]); ?></td>
        <td><?php echo htmlspecialchars($r["uploaded_by"]); ?></td>
        <td class="small"><?php echo htmlspecialchars($r["uploaded_at"]); ?></td>
        <td>
          <a href="../<?php echo htmlspecialchars($r["file_path"]); ?>" target="_blank">View / Download</a>
        </td>
      </tr>
    <?php endwhile; ?>
  </table>
<?php endif; ?>

</body>
</html>



<?php
session_start();
require_once "../config/db.php";

// ✅ only patient
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "patient") {
  header("Location: ../public/signin.php");
  exit();
}

$pid = (int)$_SESSION["user_id"];

$stmt = $conn->prepare("
  SELECT title, file_path, uploaded_by, uploaded_at
  FROM reports
  WHERE patient_id=?
  ORDER BY uploaded_at DESC
");
$stmt->bind_param("i", $pid);
$stmt->execute();
$reports = $stmt->get_result();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>My Medical Reports</title>
  <link rel="stylesheet" href="../assests/app.css">
</head>
<body>

<main class="container reps-page">
  <div class="reps-topbar">
    <div>
      <h1 class="reps-title">My Medical Reports</h1>
      <p class="reps-subtitle">View and download your uploaded medical reports.</p>
    </div>

    <div class="reps-actions">
      <a class="btn btn-ghost" href="dashboard.php">← Back to Dashboard</a>
    </div>
  </div>

  <?php if($reports->num_rows === 0): ?>
    <div class="reps-empty">
      <div class="reps-empty-ico" aria-hidden="true">📄</div>
      <div>
        <h3 style="margin:0 0 4px;">No reports yet</h3>
        <p class="muted" style="margin:0;">When admin uploads reports, they will appear here.</p>
      </div>
    </div>
  <?php else: ?>

    <div class="reps-card">
      <div class="reps-table-wrap">
        <table class="reps-table">
          <thead>
            <tr>
              <th>Title</th>
              <th>Uploaded by</th>
              <th>Date</th>
              <th>File</th>
            </tr>
          </thead>
          <tbody>
            <?php while($r = $reports->fetch_assoc()): ?>
              <tr>
                <td>
                  <div class="reps-titlecell">
                    <span class="reps-doc" aria-hidden="true">📄</span>
                    <span><?php echo htmlspecialchars($r["title"]); ?></span>
                  </div>
                </td>

                <td><?php echo htmlspecialchars($r["uploaded_by"]); ?></td>

                <td class="reps-date">
                  <?php echo htmlspecialchars($r["uploaded_at"]); ?>
                </td>

                <td>
                  <a class="btn btn-primary btn-sm" href="../<?php echo htmlspecialchars($r["file_path"]); ?>" target="_blank" rel="noopener">
                    View / Download
                  </a>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>

  <?php endif; ?>

</main>

<?php $stmt->close(); ?>
</body>
</html>



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

// ✅ Approve action
if (isset($_GET["approve"])) {
  $id = (int)$_GET["approve"];

  // approve patient
  $stmt = $conn->prepare("UPDATE users SET status='approved' WHERE id=? AND role='patient'");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $stmt->close();

  // add notification for patient
  $msg = "Your account has been approved. You can now login.";
  $stmt2 = $conn->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
  $stmt2->bind_param("is", $id, $msg);
  $stmt2->execute();
  $stmt2->close();

  header("Location: approve_patients.php");
  exit();
}

// fetch patients
$patients = $conn->query("
  SELECT id, name, email, status, created_at
  FROM users
  WHERE role='patient'
  ORDER BY created_at DESC
");
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Approve Patients</title>
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
      <a class="admin-link is-active" href="approve_patients.php">Approve Patients</a>
      <a class="admin-link" href="manage_appointments.php">Appointments</a>
      <a class="btn btn-ghost" href="../public/logout.php">Logout</a>
    </nav>
  </div>
</header>

<main class="container admin-page">
  <div class="admin-head">
    <div>
      <h1 class="admin-title">Approve Patients</h1>
      <p class="admin-sub">Review registrations and approve pending patient accounts.</p>
    </div>

    <div class="admin-head-actions">
      <a class="btn btn-ghost" href="dashboard.php">← Back to Dashboard</a>
    </div>
  </div>

  <section class="admin-table-card">
    <div class="admin-table-top">
      <div class="admin-table-title">
        <h2>Patients</h2>
        <p class="muted">Click approve to activate pending accounts.</p>
      </div>
    </div>

    <?php if(!$patients || $patients->num_rows === 0): ?>
      <div class="admin-empty">
        <div class="admin-empty-ico" aria-hidden="true">👤</div>
        <div>
          <h3 style="margin:0 0 4px;">No patients found</h3>
          <p class="muted" style="margin:0;">New registrations will appear here.</p>
        </div>
      </div>
    <?php else: ?>

      <div class="admin-table-wrap">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Status</th>
              <th style="width: 160px;">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php while($p = $patients->fetch_assoc()): ?>
              <?php $status = strtolower($p["status"] ?? ""); ?>
              <tr>
                <td>
                  <div class="admin-user">
                    <span class="admin-user-ico" aria-hidden="true">👤</span>
                    <div class="admin-user-meta">
                      <div class="admin-user-name"><?php echo htmlspecialchars($p["name"]); ?></div>
                      <div class="admin-user-sub muted">
                        Joined: <?php echo htmlspecialchars($p["created_at"]); ?>
                      </div>
                    </div>
                  </div>
                </td>

                <td><?php echo htmlspecialchars($p["email"]); ?></td>

                <td>
                  <?php if ($status === "pending"): ?>
                    <span class="pill pill-pending">Pending</span>
                  <?php else: ?>
                    <span class="pill pill-approved">Approved</span>
                  <?php endif; ?>
                </td>

                <td>
                  <?php if ($status === "pending"): ?>
                    <a class="btn btn-primary btn-sm" href="?approve=<?php echo (int)$p["id"]; ?>">Approve</a>
                  <?php else: ?>
                    <span class="muted" style="font-weight:900; font-size:12px;">No action</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>

    <?php endif; ?>
  </section>

</main>
</body>
</html>



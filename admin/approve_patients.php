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

// ✅ Approve action
if (isset($_GET["approve"])) {
    $id = (int)$_GET["approve"];

    // approve patient
    $stmt = $conn->prepare("UPDATE users SET status='approved' WHERE id=? AND role='patient'");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    // add notification for patient
    $msg = "Your account has been approved. You can now login.";
    $stmt2 = $conn->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
    $stmt2->bind_param("is", $id, $msg);
    $stmt2->execute();

    header("Location: approve_patients.php");
    exit();
}

// fetch patients
$patients = $conn->query("SELECT id, name, email, phone, status, created_at
                          FROM users
                          WHERE role='patient'
                          ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html>
<head>
  <title>Approve Patients</title>
  <style>
    body { font-family: Arial; padding: 25px; }
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
    th { background: #f3f3f3; }
    a.btn { padding: 6px 10px; background: green; color: white; text-decoration: none; border-radius: 6px; }
  </style>
</head>
<body>

<h2>Approve Patients</h2>
<a href="dashboard.php">← Back to Dashboard</a>

<br><br>

<table>
  <tr>
    <th>Name</th>
    <th>Email</th>
    <th>Phone</th>
    <th>Status</th>
    <th>Action</th>
  </tr>

  <?php while($p = $patients->fetch_assoc()): ?>
    <tr>
      <td><?php echo htmlspecialchars($p["name"]); ?></td>
      <td><?php echo htmlspecialchars($p["email"]); ?></td>
      <td><?php echo htmlspecialchars($p["phone"]); ?></td>
      <td><?php echo htmlspecialchars($p["status"]); ?></td>
      <td>
        <?php if($p["status"] === "pending"): ?>
          <a class="btn" href="?approve=<?php echo $p["id"]; ?>">Approve</a>
        <?php else: ?>
          Approved ✅
        <?php endif; ?>
      </td>
    </tr>
  <?php endwhile; ?>
</table>

</body>
</html>


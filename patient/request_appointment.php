<?php
session_start();require_once "../config/db.php";

// ✅ only patient
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "patient") {
    header("Location: ../public/signin.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $issue = trim($_POST["issue"]);
    $date  = $_POST["requested_date"] ?: null;
    $pid   = $_SESSION["user_id"];

    $stmt = $conn->prepare("INSERT INTO appointments (patient_id, issue, requested_date) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $pid, $issue, $date);

    if ($stmt->execute()) {
        $message = "✅ Appointment request submitted successfully!";
    } else {
        $message = "❌ Something went wrong!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Request Appointment</title>
  <style>
    body { font-family: Arial; padding: 25px; }
    textarea, input { width: 100%; padding: 10px; margin: 10px 0; }
    button { padding: 10px 16px; }
    .msg { margin: 10px 0; color: green; }
  </style>
</head>
<body>

<h2>Request Appointment</h2>
<a href="dashboard.php">← Back to Dashboard</a>

<p class="msg"><?php echo htmlspecialchars($message); ?></p>

<form method="POST">
  <label>Health Issue / Problem</label>
  <textarea name="issue" required placeholder="Describe your health issue..."></textarea>

  <label>Preferred Date (optional)</label>
  <input type="date" name="requested_date">

  <button type="submit">Submit Request</button>
</form>

</body>
</html>


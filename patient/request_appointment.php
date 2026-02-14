<?php
session_start();
require_once "../config/db.php";


// ✅ only patient
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "patient") {
    header("Location: ../public/signin.php");
    exit();
}


$message = "";
$messageType = ""; // "success" | "error"


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $issue = trim($_POST["issue"] ?? "");
    $date  = !empty($_POST["requested_date"]) ? $_POST["requested_date"] : null;
    $pid   = $_SESSION["user_id"];


    // Basic validation (industry standard minimum)
    if (mb_strlen($issue) < 10) {
        $message = "Please describe your issue in a bit more detail (at least 10 characters).";
        $messageType = "error";
    } else {
        $stmt = $conn->prepare("INSERT INTO appointments (patient_id, issue, requested_date) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $pid, $issue, $date);


        if ($stmt->execute()) {
            $message = "Appointment request submitted successfully. We’ll notify you once it’s reviewed.";
            $messageType = "success";
        } else {
            $message = "Something went wrong while submitting your request. Please try again.";
            $messageType = "error";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Request Appointment</title>
  <link rel="stylesheet" href="../assests/app.css">
</head>

  <body>
  <div class="req-page">
    <div class="req-container">

      <div class="req-topbar">
        <div class="req-crumb">
          <a href="dashboard.php" aria-label="Back to Dashboard">← Back to Dashboard</a>
        </div>
      </div>

      <div class="req-card">
        <div class="req-card-header">
          <div>
            <h1 class="req-title">Request Appointment</h1>
            <p class="req-subtitle">Tell us what you’re experiencing and pick a preferred date if you have one.</p>
          </div>
          <div class="req-badge">Patient Portal</div>
        </div>

        <div class="req-card-body">
          <?php if (!empty($message)): ?>
            <div class="req-alert <?php echo ($messageType === "success") ? "success" : "error"; ?>" role="status" aria-live="polite">
              <?php echo htmlspecialchars($message); ?>
            </div>
          <?php endif; ?>

          <form method="POST" class="req-form" novalidate>
            <div class="req-field">
              <label class="req-label" for="issue">Health issue / problem</label>
              <div class="req-hint">Include symptoms, how long you’ve had them, and anything that makes it better/worse.</div>

              <textarea
                class="req-textarea"
                id="issue"
                name="issue"
                required
                minlength="10"
                placeholder="Example: I’ve had a sore throat and fever for 3 days. It gets worse at night..."
              ></textarea>

              <div class="req-counter">
                <span>Minimum <strong>10</strong> characters</span>
                <span id="charCount">0</span>
              </div>
            </div>

            <div class="req-row">
              <div class="req-field">
                <label class="req-label" for="requested_date">Preferred date (optional)</label>
                <div class="req-hint">Leave empty if you’re flexible.</div>
                <input class="req-date" id="requested_date" type="date" name="requested_date" />
              </div>

              <div class="req-field">
                <label class="req-label">Response time</label>
                <div class="req-hint">We’ll review your request and respond with available slots.</div>
                <input class="req-disabled" type="text" value="Typically within 24–48 hours" disabled />
              </div>
            </div>

            <div class="req-actions">
              <a class="btn btn-ghost" href="dashboard.php">Cancel</a>
              <button class="btn btn-primary" type="submit">Submit Request →</button>
            </div>
          </form>
        </div>
      </div>

    </div>
  </div>

  <script>
    const issue = document.getElementById('issue');
    const charCount = document.getElementById('charCount');

    function updateCount(){
      charCount.textContent = `${issue.value.length} characters`;
    }
    issue.addEventListener('input', updateCount);
    updateCount();
  </script>
</body>
</html>






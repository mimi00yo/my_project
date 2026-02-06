<?php
session_start();
require_once __DIR__ . "/../config/db.php";

if (!isset($_SESSION["user_id"]) || ($_SESSION["role"] ?? "") !== "patient") {
    header("Location: ../public/signin.php");
    exit();
}

$uid = $_SESSION["user_id"];

// mark one as read
if (isset($_GET["read"])) {
    $nid = (int)$_GET["read"];
    $stmt = $conn->prepare("UPDATE notifications SET is_read=1 WHERE id=? AND user_id=?");
    $stmt->bind_param("ii", $nid, $uid);
    $stmt->execute();
    header("Location: notifications.php");
    exit();
}

// mark all as read
if (isset($_GET["read_all"])) {
    $stmt = $conn->prepare("UPDATE notifications SET is_read=1 WHERE user_id=?");
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    header("Location: notifications.php");
    exit();
}

$stmt = $conn->prepare("SELECT id, message, is_read, created_at
                        FROM notifications
                        WHERE user_id=?
                        ORDER BY created_at DESC");
$stmt->bind_param("i", $uid);
$stmt->execute();
$notes = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
  <title>Notifications</title>
  <style>
    body { font-family: Arial; padding: 25px; }
    .note { border:1px solid #ddd; border-radius:10px; padding:12px; margin-bottom:10px; }
    .small { color:#666; font-size:13px; }
    .unread { background:#fffbe6; }
    a { text-decoration:none; }
  </style>
</head>
<body>

<h2>Notifications</h2>
<a href="dashboard.php">← Back to Dashboard</a>
&nbsp; | &nbsp;
<a href="?read_all=1">Mark all as read</a>

<br><br>

<?php if($notes->num_rows === 0): ?>
  <p>No notifications.</p>
<?php else: ?>
  <?php while($n = $notes->fetch_assoc()): ?>
    <div class="note <?php echo ($n["is_read"] == 0) ? "unread" : ""; ?>">
      <?php echo htmlspecialchars($n["message"]); ?><br>
      <span class="small"><?php echo htmlspecialchars($n["created_at"]); ?></span>
      <?php if($n["is_read"] == 0): ?>
        <div class="small">
          <a href="?read=<?php echo (int)$n["id"]; ?>">Mark as read</a>
        </div>
      <?php endif; ?>
    </div>
  <?php endwhile; ?>
<?php endif; ?>

</body>
</html>



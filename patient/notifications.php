<?php
session_start();
require_once __DIR__ . "/../config/db.php";

if (!isset($_SESSION["user_id"]) || ($_SESSION["role"] ?? "") !== "patient") {
  header("Location: ../public/signin.php");
  exit();
}

$uid = (int)$_SESSION["user_id"];

// mark one as read
if (isset($_GET["read"])) {
  $nid = (int)$_GET["read"];
  $stmt = $conn->prepare("UPDATE notifications SET is_read=1 WHERE id=? AND user_id=?");
  $stmt->bind_param("ii", $nid, $uid);
  $stmt->execute();
  $stmt->close();
  header("Location: notifications.php");
  exit();
}

// mark all as read
if (isset($_GET["read_all"])) {
  $stmt = $conn->prepare("UPDATE notifications SET is_read=1 WHERE user_id=?");
  $stmt->bind_param("i", $uid);
  $stmt->execute();
  $stmt->close();
  header("Location: notifications.php");
  exit();
}

$stmt = $conn->prepare("
  SELECT id, message, is_read, created_at
  FROM notifications
  WHERE user_id=?
  ORDER BY created_at DESC
");
$stmt->bind_param("i", $uid);
$stmt->execute();
$notes = $stmt->get_result();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Notifications</title>
  <link rel="stylesheet" href="../assests/app.css">
</head>
<body>

<main class="container noti-page">
  <header class="noti-topbar">
    <div>
      <h1 class="noti-title">Notifications</h1>
      <p class="noti-subtitle">Updates from admin about appointments and reports.</p>
    </div>

    <div class="noti-actions">
      <a class="btn btn-ghost" href="dashboard.php">← Back to Dashboard</a>
      <a class="btn btn-primary" href="?read_all=1">Mark all as read</a>
    </div>
  </header>

  <?php if ($notes->num_rows === 0): ?>
    <div class="noti-empty">
      <div class="noti-empty-ico" aria-hidden="true">📭</div>
      <div>
        <h3 style="margin:0 0 4px;">No notifications</h3>
        <p class="muted" style="margin:0;">You’ll see updates here when something changes.</p>
      </div>
    </div>
  <?php else: ?>

    <section class="noti-list" aria-label="Notifications list">
      <?php while($n = $notes->fetch_assoc()): ?>
        <?php $isUnread = ((int)$n["is_read"] === 0); ?>
        <article class="noti-card <?php echo $isUnread ? "is-unread" : ""; ?>">
          <div class="noti-row">
            <div class="noti-dot" aria-hidden="true"></div>

            <div class="noti-body">
              <div class="noti-msg">
                <?php echo htmlspecialchars($n["message"]); ?>
              </div>
              <div class="noti-time">
                <?php echo htmlspecialchars($n["created_at"]); ?>
              </div>
            </div>

            <?php if ($isUnread): ?>
              <a class="btn btn-ghost btn-sm" href="?read=<?php echo (int)$n["id"]; ?>">Mark as read</a>
            <?php else: ?>
              <span class="noti-read">Read</span>
            <?php endif; ?>
          </div>
        </article>
      <?php endwhile; ?>
    </section>

  <?php endif; ?>

</main>

<?php $stmt->close(); ?>
</body>
</html>



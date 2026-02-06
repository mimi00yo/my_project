<?php
date_default_timezone_set("Asia/Kathmandu");

require_once __DIR__ . "/../config/db.php";
$config = require __DIR__ . "/../config/email.php";

require_once __DIR__ . "/../lib/PHPMailer/PHPMailer.php";
require_once __DIR__ . "/../lib/PHPMailer/SMTP.php";
require_once __DIR__ . "/../lib/PHPMailer/Exception.php";

use PHPMailer\PHPMailer\PHPMailer;

$tomorrow = date("Y-m-d", strtotime("+1 day"));

$sql = "SELECT a.scheduled_date, u.name, u.email
        FROM appointments a
        JOIN users u ON a.patient_id = u.id
        WHERE a.status='approved'
          AND a.scheduled_date = ?
          AND u.role='patient'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $tomorrow);
$stmt->execute();
$res = $stmt->get_result();

$count = 0;

while ($row = $res->fetch_assoc()) {
  $mail = new PHPMailer(true);

  try {
    $mail->isSMTP();
    $mail->Host = $config["smtp_host"];
    $mail->SMTPAuth = true;
    $mail->Username = $config["smtp_user"];
    $mail->Password = $config["smtp_pass"];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = $config["smtp_port"];

    $mail->setFrom($config["from_email"], $config["from_name"]);
    $mail->addAddress($row["email"], $row["name"]);

    $mail->Subject = "Appointment Reminder - CareCloud";
    $mail->Body = "Hello {$row["name"]},\n\nReminder: Your appointment is tomorrow ({$row["scheduled_date"]}).\n\nCareCloud PMS";

    $mail->send();
    $count++;

  } catch (Exception $e) {
    // ignore or log error
  }
}

echo "✅ Done. Emails sent: $count";

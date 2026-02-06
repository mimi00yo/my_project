<?php
$config = require __DIR__ . "/../config/email.php";

require_once __DIR__ . "/../lib/PHPMailer/PHPMailer.php";
require_once __DIR__ . "/../lib/PHPMailer/SMTP.php";
require_once __DIR__ . "/../lib/PHPMailer/Exception.php";

use PHPMailer\PHPMailer\PHPMailer;

$mail = new PHPMailer(true);
try {
  $mail->SMTPDebug = 0;
  $mail->Debugoutput = 'html';
  $mail->isSMTP();
  $mail->Host = $config["smtp_host"];
  $mail->SMTPAuth = true;
  $mail->Username = $config["smtp_user"];
  $mail->Password = $config["smtp_pass"];
  $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
  $mail->Port = $config["smtp_port"];

  $mail->setFrom($config["from_email"], $config["from_name"]);
  $mail->addAddress("RECEIVER_EMAIL@gmail.com"); // put your email

  $mail->Subject = "Test Email - CareCloud";
  $mail->Body = "Hello! Test email from CareCloud PMS.";

  $mail->send();
  echo "✅ Email sent!";
} catch (Exception $e) {
  echo "❌ Failed: " . $mail->ErrorInfo;
}


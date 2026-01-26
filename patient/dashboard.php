<?php
session_start();
if (!isset($_SESSION['patient_id'])) {
    header("Location:signin.php");
}
echo "Welcome! Login successful.";
?>

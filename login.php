<?php
session_start();
include("connect.php");

if (!isset($_POST['email'], $_POST['password'])) {
    die("Form not submitted properly");
}

$email = $_POST['email'];
$password = $_POST['password'];

    $sql = "SELECT * FROM patients WHERE email = '$email' LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {

            // store session
            $_SESSION['patient_id'] = $user['patient_id'];
            $_SESSION['name'] = $user['name'];

            // redirect to dashboard
            header("Location: patient/dashboard.php");
            exit();
        } else {
            echo "❌ Incorrect password";
        }
    } else {
        echo "❌ User not found";
    }
?>

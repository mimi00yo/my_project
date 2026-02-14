 <?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "pms_db";

$conn = new mysqli($host, $user, $pass, $db);
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    die("Database connection failed!");
}
?>


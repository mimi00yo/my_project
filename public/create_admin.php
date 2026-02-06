<?php
require_once "../config/db.php";

// ✅ change these
$adminName = "Admin";
$adminEmail = "admin@gmail.com";
$adminPassword = "admin123"; // you can change

$hash = password_hash($adminPassword, PASSWORD_DEFAULT);

// check if admin already exists
$stmt = $conn->prepare("SELECT id FROM users WHERE email=? LIMIT 1");
$stmt->bind_param("s", $adminEmail);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 1) {
    echo "✅ Admin already exists. You can login using: $adminEmail";
    exit;
}

// insert admin
$stmt = $conn->prepare("INSERT INTO users (name, email, phone, password_hash, role, status)
                        VALUES (?, ?, '', ?, 'admin', 'approved')");
$stmt->bind_param("sss", $adminName, $adminEmail, $hash);

if ($stmt->execute()) {
    echo "✅ Admin created successfully!<br>";
    echo "Email: $adminEmail <br>";
    echo "Password: $adminPassword <br><br>";
    echo "Now open: <a href='signin.php'>Sign in</a><br><br>";
    echo "⚠ After login, delete create_admin.php for security.";
} else {
    echo "❌ Error creating admin.";
}


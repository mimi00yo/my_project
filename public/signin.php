<?php

session_set_cookie_params([
  "lifetime" => 0,
  "path" => "/carecloud/",
  "httponly" => true,
  "samesite" => "Lax"
]);


session_start();require_once "../config/db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (!password_verify($password, $user['password_hash'])) {
            $error = "Incorrect password";
        } else if ($user['role'] === 'patient' && $user['status'] !== 'approved') {
            $error = "Your account is pending. Wait for admin approval.";
        } else {
            // ✅ session set
            session_regenerate_id(true);  
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["role"] = $user["role"];
            $_SESSION["name"] = $user["name"];

            // ✅ redirect based on role
            if ($user["role"] === "admin") {
                header("Location: ../admin/dashboard.php");
            } else {
                header("Location: ../patient/dashboard.php");
            }
            exit();
        }
    } else {
        $error = "Email not found";
    }
}
?>




<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Sign in</title>
  <link rel="stylesheet" href="../assests/app.css">
</head>
<body>

<div class="nav">
  <div class="container">
    <div class="nav-inner">
      <a class="brand" href="../index.php">
        <span class="logo" aria-hidden="true"></span>
        <span>My Project</span>
      </a>
      <div class="nav-links">
        <a href="../index.php">Home</a>
        <a class="btn btn-ghost" href="signup.php">Sign up</a>
      </div>
    </div>
  </div>
</div>

<div class="auth-wrap">
  <div class="auth-box">

    <div class="auth-left">
      <img src="signin.png" alt="Welcome">
    </div>

    <div class="auth-right">
      <h1 class="auth-title">Welcome!</h1>
      <p class="auth-subtitle">Sign in to continue to your account.</p>

      <?php if (!empty($error)) : ?>
        <div class="alert"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>

      <form method="POST" >
        <label class="label" for="email">Email</label>
        <input class="input" type="email" id="email" name="email" placeholder="you@example.com" required>

        <label class="label" for="password">Password</label>
        <input class="input" type="password" id="password" name="password" placeholder="••••••••" required>

        <div class="auth-row">
          <a class="helper-link" href="forgotpass.php">Forgot password?</a>
          <button class="btn btn-primary" type="submit">Sign in</button>
        </div>

        <p style="margin:14px 0 0; color: var(--muted); font-size: 14px;">
          Don’t have an account? <a class="helper-link" href="signup.php">Sign up</a>
        </p>
      </form>
    </div>
  </div>
</div>
</body>
</html>


<?php

session_set_cookie_params([
  "lifetime" => 0,
  "path" => "/carecloud/",
  "httponly" => true,
  "samesite" => "Lax"
]);


session_start();
require_once "../config/db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

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

<header class="nav">
  <div class="container nav-inner">
    <a class="brand" href="../index.php">
      <!-- Use your svg logo if you have it -->
      <!-- <img src="../assests/images/logo.svg" class="logo" alt="CareCloud Logo"> -->
        <img src="../assests/images/pms.png" alt="PMS Logo" class="logo">
      <span>PMS</span>
    </a>

    <nav class="nav-links" aria-label="Primary navigation">
      <a class="nav-link" href="../index.php">Home</a>
      <a class="btn btn-ghost" href="signup.php">Sign up</a>
    </nav>
  </div>
</header>

<main class="auth-page">
  <div class="container">
    <section class="auth-box" aria-label="Sign in">
      <div class="auth-left" aria-hidden="true">
        <!-- If image missing, it will still look good -->
        <img src="signin.jpg" alt="">
        <div class="auth-left-overlay">
          <div class="auth-badge">🔒 Secure Access</div>
          <h2>Welcome back</h2>
          <p>Manage appointments, reports and notifications in one place.</p>
        </div>
      </div>

      <div class="auth-right">
        <div class="auth-head">
          <h1 class="auth-title">Sign in</h1>
          <p class="auth-subtitle">Enter your details to continue.</p>
        </div>

        <?php if (!empty($error)) : ?>
          <div class="alert" role="alert">
            <strong>Sign in failed:</strong>
            <?php echo htmlspecialchars($error); ?>
          </div>
        <?php endif; ?>

        <form method="POST" class="auth-form" novalidate>
          <div class="field">
            <label class="label" for="email">Email</label>
            <input class="input" type="email" id="email" name="email"
                   placeholder="you@example.com" autocomplete="email" required>
          </div>

          <div class="field">
            <label class="label" for="password">Password</label>
            <input class="input" type="password" id="password" name="password"
                   placeholder="••••••••" autocomplete="current-password" required>
          </div>

          <div class="auth-row">
            <a class="helper-link" href="forgotpass.php">Forgot password?</a>
            <button class="btn btn-primary" type="submit">Sign in</button>
          </div>

          <div class="auth-divider">
            <span>New here?</span>
          </div>

          <a class="btn btn-ghost btn-block" href="signup.php">Create an account</a>
        </form>

        <!-- <p class="auth-foot">
          By continuing, you agree to our <a class="helper-link" href="#">Terms</a> and
          <a class="helper-link" href="#">Privacy Policy</a>. -->
        </p>
      </div>
    </section>
  </div>
</main>

</body>
</html>



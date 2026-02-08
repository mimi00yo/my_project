<?php
require_once "../config/db.php";
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $pass  = $_POST['password'];

    $hash = password_hash($pass, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (name, email, phone, password_hash, role, status)
                            VALUES (?, ?, '', ?, 'patient', 'pending')");
    $stmt->bind_param("sss", $name, $email, $hash);

    if ($stmt->execute()) {
        header("Location: signin.php");
        exit();
    } else {
        $message = "Error: Email already exists.";
    }
}
?>




<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Sign up</title>
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
        <a class="btn btn-ghost" href="signin.php">Sign in</a>
      </div>
    </div>
  </div>
</div>

<div class="auth-wrap">
  <div class="auth-box">

    <div class="auth-left">
      <img src="signup.png" alt="Create account">
    </div>

    <div class="auth-right">
      <h1 class="auth-title">Create account</h1>
      <p class="auth-subtitle">Sign up as a patient. Admin will approve your account.</p>

      <?php if (!empty($message)) : ?>
        <div class="alert"><?php echo htmlspecialchars($message); ?></div>
      <?php endif; ?>

      <form method="POST">
        <label class="label" for="name">Full name</label>
        <input class="input" type="text" id="name" name="name" placeholder="Your name" required>

        <label class="label" for="email">Email</label>
        <input class="input" type="email" id="email" name="email" placeholder="you@example.com" required>

        <label class="label" for="password">Password</label>
        <input class="input" type="password" id="password" name="password" placeholder="Create a strong password" required>

        <div class="auth-row">
          <a class="helper-link" href="signin.php">Already have an account?</a>
          <button class="btn btn-primary" type="submit">Sign up</button>
        </div>

        <p style="margin:14px 0 0; color: var(--muted); font-size: 14px;">
          By signing up, you agree to basic terms.
        </p>
      </form>
    </div>

  </div>
</div>

</body>
</html>


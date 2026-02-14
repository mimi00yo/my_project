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

<header class="nav">
  <div class="container nav-inner">
    <a class="brand" href="../index.php">
<img src="../assests/images/pms.png" alt="PMS Logo" class="logo">  
      <span> PMS</span>
    </a>

    <nav class="nav-links" aria-label="Primary navigation">
      <a class="nav-link" href="../index.php">Home</a>
      <a class="btn btn-ghost" href="signin.php">Sign in</a>
    </nav>
  </div>
</header>

<main class="auth-page">
  <div class="container">

    <section class="auth-box" aria-label="Create account">

      <div class="auth-left" aria-hidden="true">
        <!-- Change image name if you want -->
        <img src="signup.png" alt="">
        <div class="auth-left-overlay">
          <div class="auth-badge">🩺 Patient System</div>
          <h2>Create your account</h2>
          <p>Register to request appointments, view reports, and receive notifications.</p>
        </div>
      </div>

      <div class="auth-right">
        <div class="auth-head">
          <h1 class="auth-title">Sign up</h1>
          <p class="auth-subtitle">Create an account in less than a minute.</p>
        </div>

        <?php if (!empty($error)) : ?>
          <div class="alert" role="alert">
            <strong>Sign up failed:</strong>
            <?php echo htmlspecialchars($error); ?>
          </div>
        <?php endif; ?>

        <form method="POST" class="auth-form" novalidate>

          <div class="field">
            <label class="label" for="name">Full name</label>
            <input class="input" type="text" id="name" name="name"
                   placeholder="John Doe" autocomplete="name" required>
          </div>

          <div class="field">
            <label class="label" for="email">Email</label>
            <input class="input" type="email" id="email" name="email"
                   placeholder="you@example.com" autocomplete="email" required>
          </div>

          <div class="field">
            <label class="label" for="password">Password</label>
            <input class="input" type="password" id="password" name="password"
                   placeholder="Create a strong password" autocomplete="new-password" required>
          </div>

          <div class="field">
            <label class="label" for="confirm_password">Confirm password</label>
            <input class="input" type="password" id="confirm_password" name="confirm_password"
                   placeholder="Repeat your password" autocomplete="new-password" required>
          </div>

          <!-- If you have role selection, keep it here -->
          <!--
          <div class="field">
            <label class="label" for="role">Role</label>
            <select class="input" id="role" name="role">
              <option value="patient">Patient</option>
              <option value="admin">Admin</option>
            </select>
          </div>
          -->

          <div class="auth-row">
            <a class="helper-link" href="signin.php">Already have an account?</a>
            <button class="btn btn-primary" type="submit">Create account</button>
          </div>

          <div class="auth-divider">
            <span>Or</span>
          </div>

          <a class="btn btn-ghost btn-block" href="signin.php">Go to Sign in</a>
        </form>

        <!-- <p class="auth-foot">
          By signing up, you agree to our <a class="helper-link" href="#">Terms</a> and
          <a class="helper-link" href="#">Privacy Policy</a>.
        </p> -->
      </div>

    </section>
  </div>
</main>

</body>
</html>




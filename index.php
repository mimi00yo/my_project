<?php
// Redirect user to login page
// header("Location: public/signin.php");
// exit();
?>


<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>My Project | Home</title>

  <!-- IMPORTANT: index.php is in root, so CSS path is assests/app.css -->
  <link rel="stylesheet" href="assests/app.css">
</head>
<body>

<div class="nav">
  <div class="container">
    <div class="nav-inner">
      <a class="brand" href="index.php">
        <span class="logo" aria-hidden="true"></span>
        <span>My Project</span>
      </a>

      <div class="nav-links">
        <!-- We'll add About/Contact later as public/about.php and public/contact.php -->
        <a href="index.php">Home</a>

        <a class="btn btn-ghost" href="public/signin.php">Sign in</a>
        <a class="btn btn-primary" href="public/signup.php">Sign up</a>
      </div>
    </div>
  </div>
</div>

<main class="container">
  <section class="hero">
    <div class="hero-grid">
      <div class="card">
        <span class="tag">✨ Appointment System</span>
        <h1>Welcome to My Project</h1>
        <p>
          Sign in to request appointments, view reports, and get notifications.
          Admin can manage appointments and upload reports.
        </p>

        <div style="display:flex; gap:10px; flex-wrap:wrap; margin-top: 12px;">
          <a class="btn btn-primary" href="public/signin.php">Get Started (Sign in)</a>
          <a class="btn btn-ghost" href="public/signup.php">Create Account</a>
        </div>
      </div>

      <div style="display:flex; flex-direction:column; gap:12px;">
        <div class="card">
          <h3 style="margin:0 0 6px;">For Patients</h3>
          <p style="margin:0;">Request appointments, see reports, and notifications.</p>
        </div>
        <div class="card">
          <h3 style="margin:0 0 6px;">For Admin</h3>
          <p style="margin:0;">Approve patients, manage appointments, upload reports.</p>
        </div>
      </div>
    </div>
  </section>

  <section style="padding: 10px 0 50px;">
    <h2 style="margin: 0 0 12px;">What you can do</h2>
    <div class="grid3">
      <div class="feature">
        <h3>Sign in</h3>
        <p>Login to continue to your dashboard.</p>
      </div>
      <div class="feature">
        <h3>Book appointments</h3>
        <p>Patients can request appointments easily.</p>
      </div>
      <div class="feature">
        <h3>Reports</h3>
        <p>Admin uploads reports and patients can view them.</p>
      </div>
    </div>
  </section>

  <footer>
    <div class="footer-row">
      <div>© <?php echo date("Y"); ?> My Project. All rights reserved.</div>
      <div class="nav-links" style="gap:10px;">
        <a href="index.php">Home</a>
        <a href="public/signin.php">Sign in</a>
        <a href="public/signup.php">Sign up</a>
      </div>
    </div>
  </footer>
</main>

</body>
</html>


<?php
// If you later want redirect:
// header("Location: public/signin.php"); exit();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>PMS | Home</title>

  <link rel="stylesheet" href="assests/app.css">
</head>
<body>

<header class="nav">
  <div class="container nav-inner">
    <a class="brand" href="index.php">
        <img src="assests/images/pms.png" alt="PMS Logo" class="logo">
      <span>PMS</span>
    </a>

    <nav class="nav-links" aria-label="Primary navigation">
      <a class="nav-link is-active" href="index.php">Home</a>
      <a class="btn btn-ghost" href="public/signin.php">Sign in</a>
      <a class="btn btn-primary" href="public/signup.php">Sign up</a>
    </nav>
  </div>
</header>

<main>
  <section class="hero">
    <div class="container hero-grid">

      <div class="hero-card">
        <span class="tag">✨Patient Management System</span>

        <h1>Appointments, reports & notifications — in one place.</h1>
        <p class="lead">
          Sign in to request appointments, view reports, and receive updates.
          Admins can manage requests and upload reports securely.
        </p>

        <div class="hero-actions">
          <a class="btn btn-primary" href="public/signin.php">Get Started</a>
          <a class="btn btn-ghost" href="public/signup.php">Create Account</a>
        </div>

        <div class="hero-stats">
          <div class="stat">
            <div class="stat-title">Patients</div>
            <div class="stat-sub">Request & track appointments</div>
          </div>
          <div class="stat">
            <div class="stat-title">Admins</div>
            <div class="stat-sub">Approve & manage schedules</div>
          </div>
          <div class="stat">
            <div class="stat-title">Reports</div>
            <div class="stat-sub">Upload & view anytime</div>
          </div>
        </div>
      </div>

      <aside class="side-stack" aria-label="Highlights">
        <div class="card mini">
          <div class="mini-icon" aria-hidden="true">🩺</div>
          <div>
            <h3>For Patients</h3>
            <p>Request appointments, see reports, and notifications.</p>
          </div>
        </div>

        <div class="card mini">
          <div class="mini-icon" aria-hidden="true">🗂️</div>
          <div>
            <h3>For Admin</h3>
            <p>Approve patients, manage appointments, upload reports.</p>
          </div>
        </div>

        <div class="card mini">
          <div class="mini-icon" aria-hidden="true">🔔</div>
          <div>
            <h3>Notifications</h3>
            <p>Stay updated on approvals, schedules, and reports.</p>
          </div>
        </div>
      </aside>

    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="section-head">
        <h2>What you can do</h2>
        <p class="muted">Simple actions designed for speed and clarity.</p>
      </div>

      <div class="grid3">
        <article class="feature">
          <div class="feature-icon" aria-hidden="true">🔐</div>
          <h3>Sign in</h3>
          <p>Login to continue to your dashboard.</p>
        </article>

        <article class="feature">
          <div class="feature-icon" aria-hidden="true">📅</div>
          <h3>Book appointments</h3>
          <p>Patients can request appointments easily.</p>
        </article>

        <article class="feature">
          <div class="feature-icon" aria-hidden="true">📄</div>
          <h3>Reports</h3>
          <p>Admin uploads reports and patients can view them.</p>
        </article>
      </div>

      <div class="cta">
        <div>
          <h3>Ready to continue?</h3>
          <p class="muted">Sign in or create an account in seconds.</p>
        </div>
        <div class="cta-actions">
          <a class="btn btn-primary" href="public/signin.php">Login & Heal ✨</a>
          <a class="btn btn-ghost" href="public/signup.php">New Here! 🙋‍♀️</a>
        </div>
      </div>
    </div>
  </section>

  <footer class="footer">
    <div class="container footer-row">
      <div>© <?php echo date("Y"); ?> My Project. All rights reserved.</div>
      
    </div>
  </footer>
</main>

</body>
</html>



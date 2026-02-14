
<?php
// public/partials/header.php
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?php echo isset($pageTitle) ? $pageTitle : "My Project"; ?></title>

  <!-- IMPORTANT: public pages are inside /public, so CSS path is ../assests/app.css -->
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
        <!-- You don't have about/contact yet; we'll create later -->
        <a href="../index.php">Home</a>

        <a class="btn btn-ghost" href="signin.php">Sign in</a>
        <a class="btn btn-primary" href="signup.php">Sign up</a>
      </div>
    </div>
  </div>
</div>

<?php $mainclass = isset($mainClass) ? $mainClass : "container"; ?>
<main class="<?php echo $mainclass; ?>">

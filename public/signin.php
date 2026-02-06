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



<!DOCTYPE html>
<html> 
<head>
  <title>Welcome Back!</title>
  <link rel="stylesheet" href="../assets/app.css">

  <style>
    :root {
      --blue: #4345c0;
      --blue-light: #818CF8;
      --gray: #64748B;
      --bg: #FFFFFF;
    }
    * { margin:0; padding:0; box-sizing:border-box; }
    body {
      font-family: 'Inter', sans-serif;
      background: var(--bg);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #1E293B;
    }
    .container {
      width: 1150px;
      max-width: 100%;
      height: 680px;
      background: white;
      border-radius: 32px;
      overflow: hidden;
      display: grid;
      grid-template-columns: 1fr 1fr;
      box-shadow: 0 25px 70px rgba(99, 102, 241, 0.15);
    }

    /* LEFT – Image */
    .left {
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
    }
    .left img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
    }

    /* RIGHT – Form */
    .right {
      padding: 80px 60px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      background: white;
    }
    h1 {
      font-size: 42px;
      font-weight: 700;
      margin-bottom: 12px;
      color: #1E293B;
    }
    .subtitle {
      font-size: 17px;
      color: var(--gray);
      margin-bottom: 48px;
    }
    .input-group {
      margin-bottom: 24px;
    }
    input {
      width: 100%;
      padding: 18px 20px;
      border: 2px solid #E2E8F0;
      border-radius: 16px;
      font-size: 16px;
      transition: all 0.3s;
    }
    input:focus {
      outline: none;
      border-color: var(--blue);
      box-shadow: 0 0 0 5px rgba(99,102,241,0.15);
    }
    .options {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin: 20px 0 32px;
      font-size: 14.5px;
    }
    .remember {
      display: flex;
      align-items: center;
      gap: 10px;
      color: var(--gray);
    }
    .remember input { accent-color: var(--blue); }
    .forgot a {
      color: var(--blue);
      text-decoration: none;
      font-weight: 500;
    }
    .btn {
      width: 100%;
      padding: 18px;
      background: linear-gradient(135deg, var(--blue), var(--blue-light));
      color: white;
      border: none;
      border-radius: 16px;
      font-size: 17px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s;
      box-shadow: 0 10px 30px rgba(99,102,241,0.3);
    }
    .btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 15px 35px rgba(99,102,241,0.4);
    }
    .signup {
      text-align: center;
      margin-top: 32px;
      color: var(--gray);
      font-size: 15px;
    }
    .signup a {
      color: var(--blue);
      font-weight: 600;
      text-decoration: none;
    }

    /* Responsive */
    @media (max-width: 900px) {
      .container {
        grid-template-columns: 1fr;
        height: auto;
      }
      .left {
        height: 300px;
      }
    }
  </style>
</head>
<body>

<div class="container">
  <!-- Left: Image -->
  <div class="left">
    <img src="signin.png" alt="Welcome Illustration">
  </div>

  <!-- Right: Login Form -->
  <div class="right">
    <h1>Welcome!</h1>
    <p class="subtitle">Sign in to continue to your account.</p>
    <p style="color: red;"><?php echo htmlspecialchars($error);?></p>

      <form method="POST">
  <div class="input-group">
    <input type="email" name="email" placeholder="Email Address" required />
  </div>

  <div class="input-group">
    <input type="password" name="password" placeholder="Password" required />
  </div>

      <div class="options">
        <label class="remember">
          <input type="checkbox" name="remember"/>
          Remember me
        </label>
        <div class="forgot">
          <a href="forgotpass.php">Forgot password?</a>
        </div>
      </div>

      <button type="submit" class="btn">Sign in</button>
    </form>

    <p class="signup">
      Don’t have an account? <a href="signup.php">Sign up</a>
    </p>
  </div>
</div>
</body>
</html>




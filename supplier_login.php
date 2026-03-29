<?php
session_start();
if (isset($_SESSION['supplier_id'])) {
    header("Location: supplier_dashboard.php");
    exit();
}
include 'db.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = MD5($_POST['password']);
    $result   = mysqli_query($conn, "SELECT * FROM suppliers WHERE email='$email' AND `password`='$password'");
    if (mysqli_num_rows($result) == 1) {
        $supplier = mysqli_fetch_assoc($result);
        $_SESSION['supplier_id']   = $supplier['id'];
        $_SESSION['supplier_name'] = $supplier['company_name'];
        header("Location: supplier_dashboard.php");
        exit();
    } else {
        $error = "Invalid email or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>RSKF — Supplier Login</title>
  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body {
      font-family:'Segoe UI',Arial,sans-serif;
      background:#0f0f0f; min-height:100vh;
      display:flex; align-items:center; justify-content:center;
    }
    .bg-animation {
      position:fixed; top:0; left:0; width:100%; height:100%; z-index:0;
      background:linear-gradient(135deg, #0f0f0f 0%, #001a2e 50%, #0f0f0f 100%);
    }
    .bg-animation::before {
      content:''; position:absolute; top:-50%; left:-50%;
      width:200%; height:200%;
      background:radial-gradient(ellipse at center, rgba(41,128,185,0.1) 0%, transparent 60%);
      animation:pulse 5s ease-in-out infinite;
    }
    @keyframes pulse { 0%,100%{transform:scale(1);opacity:0.5} 50%{transform:scale(1.1);opacity:1} }

    .login-container {
      position:relative; z-index:1;
      display:flex; width:820px;
      background:#1a1a1a; border-radius:20px; overflow:hidden;
      box-shadow:0 25px 60px rgba(0,0,0,0.5);
      border:1px solid rgba(255,255,255,0.06);
      opacity:0; animation:fadeUp 0.8s ease 0.2s forwards;
    }

    .left-panel {
      width:320px; padding:50px 38px;
      background:linear-gradient(135deg, #1a3a5c, #2980b9);
      display:flex; flex-direction:column; justify-content:center;
      position:relative; overflow:hidden;
    }
    .left-panel::before {
      content:''; position:absolute; top:-30%; right:-30%;
      width:200px; height:200px; border-radius:50%;
      background:rgba(255,255,255,0.05);
    }
    .left-panel::after {
      content:''; position:absolute; bottom:-20%; left:-20%;
      width:150px; height:150px; border-radius:50%;
      background:rgba(255,255,255,0.05);
    }
    .left-panel .brand { font-size:20px; font-weight:800; color:white; line-height:1.3; margin-bottom:8px; position:relative; z-index:1; }
    .left-panel .tagline { font-size:12px; color:rgba(255,255,255,0.7); margin-bottom:35px; position:relative; z-index:1; }
    .role-badge {
      background:rgba(255,255,255,0.15); border-radius:12px;
      padding:20px; position:relative; z-index:1; margin-bottom:30px;
    }
    .role-badge .icon { font-size:36px; margin-bottom:10px; }
    .role-badge h3 { font-size:16px; color:white; font-weight:700; margin-bottom:5px; }
    .role-badge p  { font-size:12px; color:rgba(255,255,255,0.7); line-height:1.6; }
    .left-panel .footer { font-size:11px; color:rgba(255,255,255,0.4); position:relative; z-index:1; }

    .right-panel { flex:1; padding:50px 45px; display:flex; flex-direction:column; justify-content:center; }
    .right-panel h2 { font-size:24px; font-weight:700; color:white; margin-bottom:6px; }
    .right-panel .subtitle { font-size:13px; color:rgba(255,255,255,0.4); margin-bottom:35px; }

    .error-msg {
      background:rgba(231,76,60,0.12); border:1px solid rgba(231,76,60,0.3);
      color:#e74c3c; padding:12px 16px; border-radius:8px;
      margin-bottom:20px; font-size:13px; animation:shake 0.4s ease;
    }
    @keyframes shake {
      0%,100%{transform:translateX(0)} 20%{transform:translateX(-8px)}
      40%{transform:translateX(8px)} 60%{transform:translateX(-4px)} 80%{transform:translateX(4px)}
    }

    .form-group { margin-bottom:20px; }
    .form-group label { display:block; font-size:11px; color:rgba(255,255,255,0.5); margin-bottom:8px; font-weight:600; text-transform:uppercase; letter-spacing:0.5px; }
    .input-wrap { position:relative; }
    .input-wrap .icon { position:absolute; left:14px; top:50%; transform:translateY(-50%); font-size:16px; color:rgba(255,255,255,0.3); }
    .form-group input {
      width:100%; padding:13px 15px 13px 42px;
      background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1);
      border-radius:10px; font-size:14px; color:white; transition:all 0.3s;
    }
    .form-group input::placeholder { color:rgba(255,255,255,0.25); }
    .form-group input:focus { outline:none; border-color:#2980b9; background:rgba(255,255,255,0.08); box-shadow:0 0 0 3px rgba(41,128,185,0.15); }

    .btn-login {
      width:100%; padding:14px;
      background:linear-gradient(135deg, #1a5276, #2980b9);
      color:white; border:none; border-radius:10px;
      font-size:15px; font-weight:600; cursor:pointer; transition:all 0.3s;
    }
    .btn-login:hover { transform:translateY(-2px); box-shadow:0 8px 20px rgba(41,128,185,0.4); }

    .links { margin-top:22px; text-align:center; font-size:13px; color:rgba(255,255,255,0.3); }
    .links a { color:#2980b9; text-decoration:none; font-weight:600; }
    .links a:hover { color:#3498db; }
    .links .divider { margin:0 10px; }

    @keyframes fadeUp { from{opacity:0;transform:translateY(30px)} to{opacity:1;transform:translateY(0)} }
  </style>
</head>
<body>
<div class="bg-animation"></div>
<div class="login-container">
  <div class="left-panel">
    <div class="brand">RSKF Group of<br>Companies Ltd.</div>
    <div class="tagline">Building Materials Management</div>
    <div class="role-badge">
      <div class="icon">🏭</div>
      <h3>Supplier Portal</h3>
      <p>Manage your deliveries and purchase history</p>
    </div>
    <div class="footer">&copy; <?php echo date('Y'); ?> RSKF Group</div>
  </div>
  <div class="right-panel">
    <h2>Supplier Login 🏭</h2>
    <p class="subtitle">Welcome back! Please login to your account.</p>
    <?php if ($error): ?>
      <div class="error-msg">❌ <?php echo $error; ?></div>
    <?php endif; ?>
    <form method="POST">
      <div class="form-group">
        <label>Email Address</label>
        <div class="input-wrap">
          <span class="icon">✉️</span>
          <input type="email" name="email" placeholder="email@company.com" required autofocus>
        </div>
      </div>
      <div class="form-group">
        <label>Password</label>
        <div class="input-wrap">
          <span class="icon">🔒</span>
          <input type="password" name="password" placeholder="Your password" required>
        </div>
      </div>
      <button type="submit" class="btn-login">Login →</button>
    </form>
    <div class="links">
      Don't have an account? <a href="register.php?role=supplier">Register here</a>
      <span class="divider">|</span>
      <a href="customer_login.php">Customer</a>
      <span class="divider">|</span>
      <a href="employee_login.php">Employee</a>
    </div>
  </div>
</div>
</body>
</html>
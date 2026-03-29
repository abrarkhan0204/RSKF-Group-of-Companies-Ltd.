<?php
session_start();
include 'db.php';

$error   = '';
$success = '';
$role    = isset($_GET['role']) ? $_GET['role'] : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    if (!in_array($role, ['customer', 'supplier', 'employee'])) {
        $error = "Invalid role selected!";
    } else {
        $name     = mysqli_real_escape_string($conn, $_POST['full_name']);
        $email    = mysqli_real_escape_string($conn, $_POST['email']);
        $phone    = mysqli_real_escape_string($conn, $_POST['phone'] ?? '');
        $city     = mysqli_real_escape_string($conn, $_POST['city'] ?? '');
        $address  = mysqli_real_escape_string($conn, $_POST['address'] ?? '');
        $password = MD5($_POST['password']);
        $confirm  = MD5($_POST['confirm_password']);

        if (empty($name) || empty($email) || empty($_POST['password'])) {
            $error = "Name, Email and Password are required!";
        } elseif ($password != $confirm) {
            $error = "Passwords do not match!";
        } else {
            if ($role === 'customer') {
                $check = mysqli_query($conn, "SELECT id FROM customers WHERE email='$email'");
                if (mysqli_num_rows($check) > 0) {
                    $error = "This email is already registered!";
                } else {
                    $q = mysqli_query($conn,
                        "INSERT INTO customers (full_name, phone, email, address, city, password)
                         VALUES ('$name','$phone','$email','$address','$city','$password')");
                    if ($q) $success = "customer";
                    else $error = "Error: " . mysqli_error($conn);
                }
            } elseif ($role === 'supplier') {
                $company = mysqli_real_escape_string($conn, $_POST['company_name'] ?? '');
                if (empty($company)) {
                    $error = "Company name is required!";
                } else {
                    $check = mysqli_query($conn, "SELECT id FROM suppliers WHERE email='$email'");
                    if (mysqli_num_rows($check) > 0) {
                        $error = "This email is already registered!";
                    } else {
                        $q = mysqli_query($conn,
                            "INSERT INTO suppliers (company_name, contact_person, phone, email, password, city, address)
                             VALUES ('$company','$name','$phone','$email','$password','$city','$address')");
                        if ($q) $success = "supplier";
                        else $error = "Error: " . mysqli_error($conn);
                    }
                }
            } elseif ($role === 'employee') {
                $cnic = mysqli_real_escape_string($conn, $_POST['cnic'] ?? '');
                $check = mysqli_query($conn, "SELECT id FROM employees WHERE cnic='$cnic'");
                if (mysqli_num_rows($check) == 0) {
                    $error = "Your CNIC is not registered — please contact Admin!";
                } else {
                    $q = mysqli_query($conn, "UPDATE employees SET `password`='$password' WHERE cnic='$cnic'");
                    if ($q) $success = "employee";
                    else $error = "Error: " . mysqli_error($conn);
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>RSKF — Register</title>
  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body {
      font-family:'Segoe UI',Arial,sans-serif;
      background:#0f0f0f; min-height:100vh;
      display:flex; align-items:center; justify-content:center;
      padding:40px 20px; overflow-x:hidden;
    }

    .bg-animation {
      position:fixed; top:0; left:0; width:100%; height:100%; z-index:0;
      background:linear-gradient(135deg, #0f0f0f 0%, #1a0000 50%, #0f0f0f 100%);
    }
    .bg-animation::before {
      content:''; position:absolute; top:-50%; left:-50%;
      width:200%; height:200%;
      background:radial-gradient(ellipse at center, rgba(192,57,43,0.1) 0%, transparent 60%);
      animation:pulse 5s ease-in-out infinite;
    }
    @keyframes pulse { 0%,100%{transform:scale(1);opacity:0.5} 50%{transform:scale(1.1);opacity:1} }

    .wrapper {
      position:relative; z-index:1;
      width:100%; max-width:920px;
      background:#1a1a1a; border-radius:20px; overflow:hidden;
      box-shadow:0 25px 60px rgba(0,0,0,0.5);
      border:1px solid rgba(255,255,255,0.06);
      opacity:0; animation:fadeUp 0.8s ease 0.2s forwards;
    }

    /* Header */
    .top-header {
      background:linear-gradient(135deg, #7b0000, #c0392b);
      padding:25px 40px; display:flex;
      justify-content:space-between; align-items:center;
    }
    .top-header .brand { font-size:18px; font-weight:700; color:white; }
    .top-header .brand span { font-size:12px; opacity:0.7; display:block; margin-top:3px; font-weight:400; }
    .top-header a {
      color:rgba(255,255,255,0.85); font-size:13px;
      text-decoration:none; border:1px solid rgba(255,255,255,0.3);
      padding:8px 18px; border-radius:8px; transition:all 0.2s;
    }
    .top-header a:hover { background:rgba(255,255,255,0.15); }

    .content { padding:35px 40px; }

    /* Role Cards */
    .role-section h2 { font-size:20px; font-weight:700; color:white; margin-bottom:6px; }
    .role-section p  { font-size:13px; color:rgba(255,255,255,0.4); margin-bottom:25px; }

    .role-cards { display:grid; grid-template-columns:repeat(3,1fr); gap:15px; margin-bottom:30px; }
    .role-card {
      border:2px solid rgba(255,255,255,0.08);
      border-radius:14px; padding:24px 20px;
      cursor:pointer; text-align:center;
      background:rgba(255,255,255,0.03);
      transition:all 0.3s;
    }
    .role-card:hover {
      border-color:rgba(192,57,43,0.5);
      background:rgba(192,57,43,0.08);
      transform:translateY(-3px);
    }
    .role-card.selected {
      border-color:#c0392b;
      background:rgba(192,57,43,0.12);
      transform:translateY(-3px);
    }
    .role-card .icon { font-size:36px; margin-bottom:12px; }
    .role-card h3 { font-size:15px; color:white; margin-bottom:5px; font-weight:600; }
    .role-card p  { font-size:12px; color:rgba(255,255,255,0.4); }

    /* Forms */
    .form-section { display:none; }
    .form-section.active { display:block; animation:fadeUp 0.4s ease; }
    .form-section h3 {
      font-size:16px; color:white; font-weight:600;
      margin-bottom:20px; padding-bottom:12px;
      border-bottom:1px solid rgba(255,255,255,0.08);
    }
    .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:15px; }
    .form-group label {
      display:block; font-size:11px; color:rgba(255,255,255,0.5);
      margin-bottom:7px; font-weight:600; text-transform:uppercase; letter-spacing:0.5px;
    }
    .input-wrap { position:relative; }
    .input-wrap .icon { position:absolute; left:13px; top:50%; transform:translateY(-50%); font-size:14px; color:rgba(255,255,255,0.3); }
    .form-group input {
      width:100%; padding:12px 14px 12px 38px;
      background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1);
      border-radius:9px; font-size:13px; color:white; transition:all 0.3s;
    }
    .form-group input::placeholder { color:rgba(255,255,255,0.25); }
    .form-group input:focus {
      outline:none; border-color:#c0392b;
      background:rgba(255,255,255,0.08);
      box-shadow:0 0 0 3px rgba(192,57,43,0.12);
    }
    .span2 { grid-column:span 2; }

    .btn-submit {
      width:100%; padding:14px;
      background:linear-gradient(135deg, #c0392b, #e74c3c);
      color:white; border:none; border-radius:10px;
      font-size:15px; font-weight:600; cursor:pointer;
      margin-top:20px; transition:all 0.3s;
    }
    .btn-submit:hover { transform:translateY(-2px); box-shadow:0 8px 20px rgba(192,57,43,0.4); }

    /* Messages */
    .error-msg {
      background:rgba(231,76,60,0.12); border:1px solid rgba(231,76,60,0.3);
      color:#e74c3c; padding:12px 16px; border-radius:8px;
      margin-bottom:20px; font-size:13px; animation:shake 0.4s ease;
    }
    @keyframes shake {
      0%,100%{transform:translateX(0)} 20%{transform:translateX(-8px)}
      40%{transform:translateX(8px)} 60%{transform:translateX(-4px)} 80%{transform:translateX(4px)}
    }

    .success-box {
      background:rgba(39,174,96,0.1); border:1px solid rgba(39,174,96,0.3);
      border-radius:14px; padding:40px; text-align:center;
      animation:fadeUp 0.6s ease;
    }
    .success-box .check { font-size:60px; margin-bottom:15px; }
    .success-box h3 { font-size:22px; color:white; margin-bottom:8px; }
    .success-box p  { font-size:14px; color:rgba(255,255,255,0.5); margin-bottom:25px; }
    .success-box a {
      background:linear-gradient(135deg,#1e8449,#27ae60);
      color:white; padding:12px 30px; border-radius:10px;
      text-decoration:none; font-size:14px; font-weight:600;
      transition:all 0.2s; display:inline-block;
    }
    .success-box a:hover { transform:translateY(-2px); box-shadow:0 6px 16px rgba(39,174,96,0.4); }

    .note-box {
      background:rgba(243,156,18,0.1); border:1px solid rgba(243,156,18,0.3);
      color:#f39c12; padding:12px 16px; border-radius:8px;
      font-size:13px; margin-bottom:15px;
    }
    .login-link {
      text-align:center; margin-top:25px;
      font-size:13px; color:rgba(255,255,255,0.3);
    }
    .login-link a { color:#c0392b; text-decoration:none; font-weight:600; }
    .login-link a:hover { color:#e74c3c; }

    @keyframes fadeUp { from{opacity:0;transform:translateY(25px)} to{opacity:1;transform:translateY(0)} }
  </style>
</head>
<body>
<div class="bg-animation"></div>
<div class="wrapper">

  <div class="top-header">
    <div class="brand">
      RSKF Group of Companies Ltd.
      <span>Building Materials Management System</span>
    </div>
    <a href="login.php">Already registered? Login →</a>
  </div>

  <div class="content">

    <?php if ($success == 'customer'): ?>
      <div class="success-box">
        <div class="check">✅</div>
        <h3>Account Created Successfully!</h3>
        <p>Your customer account has been created. You can now login.</p>
        <a href="customer_login.php">Go to Customer Login →</a>
      </div>

    <?php elseif ($success == 'supplier'): ?>
      <div class="success-box">
        <div class="check">🏭</div>
        <h3>Supplier Account Created!</h3>
        <p>Your supplier account has been created. You can now login.</p>
        <a href="supplier_login.php">Go to Supplier Login →</a>
      </div>

    <?php elseif ($success == 'employee'): ?>
      <div class="success-box">
        <div class="check">👷</div>
        <h3>Password Set Successfully!</h3>
        <p>Your employee password has been set. You can now login.</p>
        <a href="employee_login.php">Go to Employee Login →</a>
      </div>

    <?php else: ?>

      <div class="role-section">
        <h2>Create an Account</h2>
        <p>Select your role to get started</p>
      </div>

      <?php if ($error): ?>
        <div class="error-msg">❌ <?php echo $error; ?></div>
      <?php endif; ?>

      <div class="role-cards">
        <div class="role-card <?php echo $role=='customer'?'selected':''; ?>"
             onclick="selectRole('customer')">
          <div class="icon">👤</div>
          <h3>Customer</h3>
          <p>Buy products and track your orders</p>
        </div>
        <div class="role-card <?php echo $role=='supplier'?'selected':''; ?>"
             onclick="selectRole('supplier')">
          <div class="icon">🏭</div>
          <h3>Supplier</h3>
          <p>Register your supply company</p>
        </div>
        <div class="role-card <?php echo $role=='employee'?'selected':''; ?>"
             onclick="selectRole('employee')">
          <div class="icon">👷</div>
          <h3>Employee</h3>
          <p>Existing staff — set your password</p>
        </div>
      </div>

      <!-- CUSTOMER FORM -->
      <div class="form-section <?php echo $role=='customer'?'active':''; ?>" id="form-customer">
        <form method="POST" action="register.php">
          <input type="hidden" name="role" value="customer">
          <h3>👤 Customer Registration</h3>
          <div class="form-grid">
            <div class="form-group span2">
              <label>Full Name *</label>
              <div class="input-wrap">
                <span class="icon">👤</span>
                <input type="text" name="full_name" placeholder="Your full name" required>
              </div>
            </div>
            <div class="form-group">
              <label>Email *</label>
              <div class="input-wrap">
                <span class="icon">✉️</span>
                <input type="email" name="email" placeholder="email@example.com" required>
              </div>
            </div>
            <div class="form-group">
              <label>Phone</label>
              <div class="input-wrap">
                <span class="icon">📞</span>
                <input type="text" name="phone" placeholder="0300-1234567">
              </div>
            </div>
            <div class="form-group">
              <label>City</label>
              <div class="input-wrap">
                <span class="icon">📍</span>
                <input type="text" name="city" placeholder="Lahore">
              </div>
            </div>
            <div class="form-group">
              <label>Address</label>
              <div class="input-wrap">
                <span class="icon">🏠</span>
                <input type="text" name="address" placeholder="Street, Area">
              </div>
            </div>
            <div class="form-group">
              <label>Password *</label>
              <div class="input-wrap">
                <span class="icon">🔒</span>
                <input type="password" name="password" placeholder="Create a password" required>
              </div>
            </div>
            <div class="form-group">
              <label>Confirm Password *</label>
              <div class="input-wrap">
                <span class="icon">🔒</span>
                <input type="password" name="confirm_password" placeholder="Repeat your password" required>
              </div>
            </div>
          </div>
          <button type="submit" class="btn-submit">Create Account →</button>
        </form>
      </div>

      <!-- SUPPLIER FORM -->
      <div class="form-section <?php echo $role=='supplier'?'active':''; ?>" id="form-supplier">
        <form method="POST" action="register.php">
          <input type="hidden" name="role" value="supplier">
          <h3>🏭 Supplier Registration</h3>
          <div class="form-grid">
            <div class="form-group span2">
              <label>Company Name *</label>
              <div class="input-wrap">
                <span class="icon">🏭</span>
                <input type="text" name="company_name" placeholder="Your company name" required>
              </div>
            </div>
            <div class="form-group">
              <label>Contact Person *</label>
              <div class="input-wrap">
                <span class="icon">👤</span>
                <input type="text" name="full_name" placeholder="Your full name" required>
              </div>
            </div>
            <div class="form-group">
              <label>Email *</label>
              <div class="input-wrap">
                <span class="icon">✉️</span>
                <input type="email" name="email" placeholder="email@company.com" required>
              </div>
            </div>
            <div class="form-group">
              <label>Phone</label>
              <div class="input-wrap">
                <span class="icon">📞</span>
                <input type="text" name="phone" placeholder="042-1234567">
              </div>
            </div>
            <div class="form-group">
              <label>City</label>
              <div class="input-wrap">
                <span class="icon">📍</span>
                <input type="text" name="city" placeholder="Lahore">
              </div>
            </div>
            <div class="form-group">
              <label>Password *</label>
              <div class="input-wrap">
                <span class="icon">🔒</span>
                <input type="password" name="password" placeholder="Create a password" required>
              </div>
            </div>
            <div class="form-group">
              <label>Confirm Password *</label>
              <div class="input-wrap">
                <span class="icon">🔒</span>
                <input type="password" name="confirm_password" placeholder="Repeat your password" required>
              </div>
            </div>
          </div>
          <button type="submit" class="btn-submit">Create Account →</button>
        </form>
      </div>

      <!-- EMPLOYEE FORM -->
      <div class="form-section <?php echo $role=='employee'?'active':''; ?>" id="form-employee">
        <div class="note-box">
          ⚠️ Only existing employees can set a password. Your CNIC must already be added by the Admin.
        </div>
        <form method="POST" action="register.php">
          <input type="hidden" name="role" value="employee">
          <h3>👷 Employee — Set Your Password</h3>
          <div class="form-grid">
            <div class="form-group span2">
              <label>CNIC *</label>
              <div class="input-wrap">
                <span class="icon">🪪</span>
                <input type="text" name="cnic" placeholder="35201-1234567-1" required>
              </div>
            </div>
            <div class="form-group">
              <label>Password *</label>
              <div class="input-wrap">
                <span class="icon">🔒</span>
                <input type="password" name="password" placeholder="Create a password" required>
              </div>
            </div>
            <div class="form-group">
              <label>Confirm Password *</label>
              <div class="input-wrap">
                <span class="icon">🔒</span>
                <input type="password" name="confirm_password" placeholder="Repeat your password" required>
              </div>
            </div>
          </div>
          <button type="submit" class="btn-submit">Set Password →</button>
        </form>
      </div>

      <div class="login-link">
        Already have an account? <a href="login.php">Login here</a>
      </div>

    <?php endif; ?>
  </div>
</div>

<script>
function selectRole(role) {
    document.querySelectorAll('.role-card').forEach(c => c.classList.remove('selected'));
    document.querySelectorAll('.form-section').forEach(f => f.classList.remove('active'));
    event.currentTarget.classList.add('selected');
    document.getElementById('form-' + role).classList.add('active');
}
<?php if ($role): ?>
document.getElementById('form-<?php echo $role; ?>').scrollIntoView({behavior:'smooth'});
<?php endif; ?>
</script>
</body>
</html>
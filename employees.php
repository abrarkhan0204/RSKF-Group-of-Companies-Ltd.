<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

// Add
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_employee'])) {
    $name     = mysqli_real_escape_string($conn, $_POST['full_name']);
    $cnic     = mysqli_real_escape_string($conn, $_POST['cnic']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $phone    = mysqli_real_escape_string($conn, $_POST['phone']);
    $desig    = mysqli_real_escape_string($conn, $_POST['designation']);
    $dept_id  = (int)$_POST['department_id'];
    $salary   = (float)$_POST['salary'];
    $join_date= $_POST['join_date'];
    $status   = $_POST['status'];
    mysqli_query($conn, "INSERT INTO employees (full_name, cnic, email, phone, designation, department_id, salary, join_date, status)
                         VALUES ('$name','$cnic','$email','$phone','$desig','$dept_id','$salary','$join_date','$status')");
    header("Location: employees.php?success=added");
    exit();
}

// Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM employees WHERE id=$id");
    header("Location: employees.php?success=deleted");
    exit();
}

$departments = mysqli_query($conn, "SELECT * FROM departments ORDER BY name ASC");
$employees   = mysqli_query($conn,
    "SELECT e.*, d.name as dept_name FROM employees e
     LEFT JOIN departments d ON e.department_id=d.id
     ORDER BY e.id ASC");
$total = mysqli_num_rows($employees);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>RSKF — Employees</title>
  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family:'Segoe UI',Arial,sans-serif; background:#0f0f0f; color:white; display:flex; }
    .sidebar { width:260px; min-height:100vh; background:#1a1a1a; position:fixed; top:0; left:0; border-right:1px solid rgba(255,255,255,0.06); }
    .sidebar .logo { padding:28px 24px; border-bottom:1px solid rgba(255,255,255,0.06); }
    .sidebar .logo h2 { font-size:16px; font-weight:700; line-height:1.4; }
    .sidebar .logo span { color:#c0392b; }
    .sidebar .logo p { font-size:11px; color:rgba(255,255,255,0.4); margin-top:4px; }
    .sidebar .admin-box { padding:16px 24px; background:rgba(192,57,43,0.08); border-bottom:1px solid rgba(255,255,255,0.06); display:flex; align-items:center; gap:12px; }
    .admin-box .avatar { width:38px; height:38px; border-radius:50%; background:linear-gradient(135deg,#c0392b,#7b0000); display:flex; align-items:center; justify-content:center; font-size:16px; }
    .admin-box .info .name { font-size:13px; font-weight:600; }
    .admin-box .info .role { font-size:11px; color:rgba(255,255,255,0.4); }
    .sidebar nav { padding:12px 0; }
    .sidebar nav .section-title { padding:14px 24px 6px; font-size:10px; text-transform:uppercase; color:rgba(255,255,255,0.3); letter-spacing:1.5px; }
    .sidebar nav a { display:flex; align-items:center; gap:10px; padding:12px 24px; color:rgba(255,255,255,0.6); text-decoration:none; font-size:13px; border-left:3px solid transparent; transition:all 0.2s; }
    .sidebar nav a:hover { background:rgba(255,255,255,0.05); color:white; border-left-color:rgba(192,57,43,0.5); }
    .sidebar nav a.active { background:rgba(192,57,43,0.12); color:white; border-left-color:#c0392b; }
    .main { margin-left:260px; flex:1; padding:35px; }
    .topbar { display:flex; justify-content:space-between; align-items:center; margin-bottom:30px; opacity:0; animation:fadeDown 0.6s ease 0.1s forwards; }
    .topbar h1 { font-size:24px; font-weight:700; }
    .topbar p  { font-size:13px; color:rgba(255,255,255,0.4); margin-top:3px; }
    .success-msg { background:rgba(39,174,96,0.12); border:1px solid rgba(39,174,96,0.3); color:#2ecc71; padding:12px 16px; border-radius:8px; margin-bottom:22px; font-size:13px; opacity:0; animation:fadeDown 0.5s ease forwards; }
    .form-box { background:#1a1a1a; border:1px solid rgba(255,255,255,0.06); border-radius:14px; padding:25px; margin-bottom:25px; opacity:0; animation:fadeUp 0.6s ease 0.2s forwards; }
    .form-box h3 { font-size:15px; font-weight:600; color:white; margin-bottom:18px; padding-bottom:12px; border-bottom:1px solid rgba(255,255,255,0.06); }
    .form-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:15px; }
    .form-grid label { display:block; font-size:11px; color:rgba(255,255,255,0.4); margin-bottom:6px; text-transform:uppercase; letter-spacing:0.5px; }
    .input-wrap { position:relative; }
    .input-wrap .icon { position:absolute; left:12px; top:50%; transform:translateY(-50%); font-size:14px; color:rgba(255,255,255,0.25); }
    .form-grid input, .form-grid select {
      width:100%; padding:11px 12px 11px 36px;
      background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1);
      border-radius:8px; font-size:13px; color:white; transition:all 0.3s;
    }
    .form-grid select option { background:#1a1a1a; color:white; }
    .form-grid input::placeholder { color:rgba(255,255,255,0.2); }
    .form-grid input:focus, .form-grid select:focus { outline:none; border-color:#c0392b; background:rgba(255,255,255,0.08); }
    .btn-add { margin-top:15px; background:linear-gradient(135deg,#c0392b,#e74c3c); color:white; padding:11px 28px; border:none; border-radius:8px; cursor:pointer; font-size:14px; font-weight:600; transition:all 0.3s; }
    .btn-add:hover { transform:translateY(-2px); box-shadow:0 6px 16px rgba(192,57,43,0.4); }
    .table-box { background:#1a1a1a; border:1px solid rgba(255,255,255,0.06); border-radius:14px; padding:25px; opacity:0; animation:fadeUp 0.6s ease 0.3s forwards; }
    .table-box h3 { font-size:15px; font-weight:600; color:white; margin-bottom:18px; padding-bottom:12px; border-bottom:1px solid rgba(255,255,255,0.06); display:flex; justify-content:space-between; align-items:center; }
    .total-badge { background:rgba(255,255,255,0.08); color:rgba(255,255,255,0.5); padding:3px 12px; border-radius:20px; font-size:12px; font-weight:400; }
    table { width:100%; border-collapse:collapse; font-size:13px; }
    table th { text-align:left; padding:10px 12px; color:rgba(255,255,255,0.3); font-weight:500; font-size:11px; text-transform:uppercase; letter-spacing:0.5px; border-bottom:1px solid rgba(255,255,255,0.06); }
    table td { padding:12px 12px; border-bottom:1px solid rgba(255,255,255,0.04); color:rgba(255,255,255,0.75); }
    table tr:last-child td { border-bottom:none; }
    table tr:hover td { background:rgba(255,255,255,0.03); }
    .badge { padding:3px 10px; border-radius:20px; font-size:11px; font-weight:600; }
    .badge.active   { background:rgba(39,174,96,0.15); color:#2ecc71; }
    .badge.inactive { background:rgba(231,76,60,0.15); color:#e74c3c; }
    .btn-delete { background:rgba(231,76,60,0.15); color:#e74c3c; padding:5px 14px; border:1px solid rgba(231,76,60,0.2); border-radius:6px; cursor:pointer; font-size:12px; text-decoration:none; transition:all 0.2s; }
    .btn-delete:hover { background:rgba(231,76,60,0.25); }
    .empty { text-align:center; padding:40px; color:rgba(255,255,255,0.2); font-size:14px; }
    @keyframes fadeUp   { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:translateY(0)} }
    @keyframes fadeDown { from{opacity:0;transform:translateY(-20px)} to{opacity:1;transform:translateY(0)} }
  </style>
</head>
<body>
<div class="sidebar">
  <div class="logo">
    <h2>RSKF <span>Group</span> of<br>Companies Ltd.</h2>
    <p>Admin Panel</p>
  </div>
  <div class="admin-box">
    <div class="avatar">👑</div>
    <div class="info">
      <div class="name"><?php echo $_SESSION['full_name']; ?></div>
      <div class="role"><?php echo ucfirst($_SESSION['role']); ?></div>
    </div>
  </div>
  <nav>
    <div class="section-title">Main</div>
    <a href="dashboard.php">📊 Dashboard</a>
    <div class="section-title">Manage</div>
    <a href="customers.php">👥 Customers</a>
    <a href="orders.php">🧾 Orders</a>
    <a href="products.php">📦 Products</a>
    <a href="purchases.php">📤 Purchase Orders</a>
    <a href="suppliers.php">🏭 Suppliers</a>
    <a href="employees.php" class="active">👷 Employees</a>
    <div class="section-title">Account</div>
    <a href="logout.php">🚪 Logout</a>
  </nav>
</div>

<div class="main">
  <div class="topbar">
    <div>
      <h1>Employees & Staff</h1>
      <p>Manage your team members</p>
    </div>
  </div>

  <?php if (isset($_GET['success'])): ?>
    <div class="success-msg">
      <?php echo $_GET['success'] == 'added' ? '✅ Employee added successfully!' : '🗑️ Employee deleted.'; ?>
    </div>
  <?php endif; ?>

  <div class="form-box">
    <h3>Add New Employee</h3>
    <form method="POST">
      <div class="form-grid">
        <div>
          <label>Full Name *</label>
          <div class="input-wrap">
            <span class="icon">👤</span>
            <input type="text" name="full_name" placeholder="Muhammad Ali" required>
          </div>
        </div>
        <div>
          <label>CNIC *</label>
          <div class="input-wrap">
            <span class="icon">🪪</span>
            <input type="text" name="cnic" placeholder="35201-1234567-1" required>
          </div>
        </div>
        <div>
          <label>Phone</label>
          <div class="input-wrap">
            <span class="icon">📞</span>
            <input type="text" name="phone" placeholder="0300-1234567">
          </div>
        </div>
        <div>
          <label>Email</label>
          <div class="input-wrap">
            <span class="icon">✉️</span>
            <input type="email" name="email" placeholder="email@rskf.com">
          </div>
        </div>
        <div>
          <label>Designation *</label>
          <div class="input-wrap">
            <span class="icon">💼</span>
            <input type="text" name="designation" placeholder="Sales Manager" required>
          </div>
        </div>
        <div>
          <label>Department *</label>
          <div class="input-wrap">
            <span class="icon">🏢</span>
            <select name="department_id" required style="padding-left:36px">
              <option value="">-- Select --</option>
              <?php while ($d = mysqli_fetch_assoc($departments)) {
                  echo "<option value='{$d['id']}'>{$d['name']}</option>";
              } ?>
            </select>
          </div>
        </div>
        <div>
          <label>Salary (Rs.) *</label>
          <div class="input-wrap">
            <span class="icon">💰</span>
            <input type="number" name="salary" placeholder="50000" required>
          </div>
        </div>
        <div>
          <label>Join Date *</label>
          <div class="input-wrap">
            <span class="icon">📅</span>
            <input type="date" name="join_date" required style="padding-left:36px">
          </div>
        </div>
        <div>
          <label>Status</label>
          <div class="input-wrap">
            <span class="icon">✅</span>
            <select name="status" style="padding-left:36px">
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>
        </div>
      </div>
      <button type="submit" name="add_employee" class="btn-add">+ Add Employee</button>
    </form>
  </div>

  <div class="table-box">
    <h3>All Employees <span class="total-badge"><?php echo $total; ?> total</span></h3>
    <table>
      <tr>
        <th>#</th><th>Name</th><th>CNIC</th><th>Phone</th>
        <th>Designation</th><th>Department</th><th>Salary</th><th>Status</th><th>Action</th>
      </tr>
      <?php
      $i = 1;
      if (mysqli_num_rows($employees) == 0) {
          echo "<tr><td colspan='9' class='empty'>No employees found</td></tr>";
      }
      while ($row = mysqli_fetch_assoc($employees)) {
          $badge = $row['status'] == 'active'
              ? "<span class='badge active'>Active</span>"
              : "<span class='badge inactive'>Inactive</span>";
          echo "<tr>
            <td>{$i}</td>
            <td>{$row['full_name']}</td>
            <td>{$row['cnic']}</td>
            <td>{$row['phone']}</td>
            <td>{$row['designation']}</td>
            <td>{$row['dept_name']}</td>
            <td>Rs. ".number_format($row['salary'],0)."</td>
            <td>{$badge}</td>
            <td>
              <a href='employees.php?delete={$row['id']}'
                 class='btn-delete'
                 onclick='return confirm(\"Delete this employee?\")'>🗑️ Delete</a>
            </td>
          </tr>";
          $i++;
      }
      ?>
    </table>
  </div>
</div>
</body>
</html>
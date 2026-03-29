<?php
session_start();
if (!isset($_SESSION['employee_id'])) {
    header("Location: employee_login.php");
    exit();
}
include 'db.php';

$employee_id = $_SESSION['employee_id'];
$employee    = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT e.*, d.name as dept_name FROM employees e
     LEFT JOIN departments d ON e.department_id=d.id
     WHERE e.id=$employee_id"));

// Colleagues in same department
$colleagues = mysqli_query($conn,
    "SELECT full_name, designation FROM employees
     WHERE department_id={$employee['department_id']} AND id != $employee_id
     ORDER BY full_name ASC");

// Company stats (read only)
$total_products = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM products"))[0];
$total_orders   = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM orders"))[0];
$total_customers= mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM customers"))[0];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>RSKF — Employee Portal</title>
  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family:'Segoe UI',Arial,sans-serif; background:#0f0f0f; color:white; display:flex; }

    .sidebar {
      width:260px; min-height:100vh; background:#1a1a1a;
      position:fixed; top:0; left:0;
      border-right:1px solid rgba(255,255,255,0.06);
    }
    .sidebar .logo { padding:28px 24px; border-bottom:1px solid rgba(255,255,255,0.06); }
    .sidebar .logo h2 { font-size:16px; font-weight:700; line-height:1.4; }
    .sidebar .logo span { color:#27ae60; }
    .sidebar .logo p { font-size:11px; color:rgba(255,255,255,0.4); margin-top:4px; }
    .sidebar .user-box {
      padding:16px 24px; background:rgba(39,174,96,0.08);
      border-bottom:1px solid rgba(255,255,255,0.06);
    }
    .user-box .avatar-row { display:flex; align-items:center; gap:12px; margin-bottom:10px; }
    .user-box .avatar {
      width:48px; height:48px; border-radius:50%;
      background:linear-gradient(135deg,#1e8449,#27ae60);
      display:flex; align-items:center; justify-content:center; font-size:20px;
    }
    .user-box .info .name { font-size:13px; font-weight:600; }
    .user-box .info .role { font-size:11px; color:rgba(255,255,255,0.4); }
    .user-box .dept-badge {
      background:rgba(39,174,96,0.15); border:1px solid rgba(39,174,96,0.2);
      padding:4px 12px; border-radius:20px; font-size:11px; color:#2ecc71;
      display:inline-block;
    }
    .sidebar nav { padding:12px 0; }
    .sidebar nav .section-title { padding:14px 24px 6px; font-size:10px; text-transform:uppercase; color:rgba(255,255,255,0.3); letter-spacing:1.5px; }
    .sidebar nav a { display:flex; align-items:center; gap:10px; padding:12px 24px; color:rgba(255,255,255,0.6); text-decoration:none; font-size:13px; border-left:3px solid transparent; transition:all 0.2s; }
    .sidebar nav a:hover { background:rgba(255,255,255,0.05); color:white; border-left-color:rgba(39,174,96,0.5); }
    .sidebar nav a.active { background:rgba(39,174,96,0.12); color:white; border-left-color:#27ae60; }

    .main { margin-left:260px; flex:1; padding:35px; min-height:100vh; }

    .topbar { display:flex; justify-content:space-between; align-items:center; margin-bottom:35px; opacity:0; animation:fadeDown 0.6s ease 0.1s forwards; }
    .topbar h1 { font-size:24px; font-weight:700; }
    .topbar p  { font-size:13px; color:rgba(255,255,255,0.4); margin-top:3px; }
    .date-badge { background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.08); padding:8px 16px; border-radius:8px; font-size:13px; color:rgba(255,255,255,0.6); }

    /* Welcome Banner */
    .welcome-banner {
      background:linear-gradient(135deg, rgba(30,132,73,0.3), rgba(39,174,96,0.1));
      border:1px solid rgba(39,174,96,0.2); border-radius:12px;
      padding:22px 28px; margin-bottom:25px; display:flex;
      justify-content:space-between; align-items:center;
      opacity:0; animation:fadeUp 0.6s ease 0.2s forwards;
    }
    .welcome-banner .left h2 { font-size:18px; font-weight:700; margin-bottom:5px; }
    .welcome-banner .left p  { font-size:13px; color:rgba(255,255,255,0.5); }
    .welcome-banner .right { text-align:right; }
    .welcome-banner .right .salary { font-size:28px; font-weight:800; color:#2ecc71; }
    .welcome-banner .right .salary-label { font-size:12px; color:rgba(255,255,255,0.4); margin-top:3px; }

    /* Cards */
    .cards { display:grid; grid-template-columns:repeat(3,1fr); gap:18px; margin-bottom:25px; }
    .card { background:#1a1a1a; border:1px solid rgba(255,255,255,0.06); border-radius:12px; padding:22px; opacity:0; animation:fadeUp 0.6s ease forwards; transition:transform 0.2s; }
    .card:hover { transform:translateY(-3px); }
    .card:nth-child(1) { animation-delay:0.2s; }
    .card:nth-child(2) { animation-delay:0.3s; }
    .card:nth-child(3) { animation-delay:0.4s; }
    .card-top { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:15px; }
    .card-icon { width:44px; height:44px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:20px; }
    .icon-green  { background:rgba(39,174,96,0.2); }
    .icon-blue   { background:rgba(41,128,185,0.2); }
    .icon-purple { background:rgba(142,68,173,0.2); }
    .card-num   { font-size:26px; font-weight:800; }
    .card-label { font-size:12px; color:rgba(255,255,255,0.4); margin-top:4px; }

    /* Info Grid */
    .info-grid { display:grid; grid-template-columns:1fr 1fr; gap:18px; margin-bottom:22px; }

    .section-box { background:#1a1a1a; border:1px solid rgba(255,255,255,0.06); border-radius:12px; padding:24px; opacity:0; animation:fadeUp 0.6s ease 0.5s forwards; }
    .section-box h3 { font-size:15px; font-weight:600; margin-bottom:20px; padding-bottom:12px; border-bottom:1px solid rgba(255,255,255,0.06); display:flex; align-items:center; gap:8px; }

    .info-items { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
    .info-item label { font-size:11px; color:rgba(255,255,255,0.3); text-transform:uppercase; letter-spacing:0.5px; display:block; margin-bottom:5px; }
    .info-item p { font-size:14px; color:white; font-weight:500; }

    .status-active   { background:rgba(39,174,96,0.15); color:#2ecc71; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:600; display:inline-block; }
    .status-inactive { background:rgba(231,76,60,0.15); color:#e74c3c; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:600; display:inline-block; }

    /* Colleagues */
    .colleagues-list { display:flex; flex-direction:column; gap:10px; }
    .colleague-item {
      display:flex; align-items:center; gap:12px;
      background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.06);
      border-radius:8px; padding:12px 16px; transition:all 0.2s;
    }
    .colleague-item:hover { background:rgba(255,255,255,0.07); }
    .colleague-avatar { width:36px; height:36px; border-radius:50%; background:linear-gradient(135deg,#1e8449,#27ae60); display:flex; align-items:center; justify-content:center; font-size:14px; flex-shrink:0; }
    .colleague-info .name { font-size:13px; font-weight:600; }
    .colleague-info .desig { font-size:11px; color:rgba(255,255,255,0.4); margin-top:2px; }

    /* Company Stats */
    .company-stats { display:grid; grid-template-columns:repeat(3,1fr); gap:15px; }
    .stat-item { background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.06); border-radius:10px; padding:18px; text-align:center; }
    .stat-item .num   { font-size:28px; font-weight:800; color:#27ae60; }
    .stat-item .label { font-size:12px; color:rgba(255,255,255,0.4); margin-top:5px; }

    .empty { text-align:center; padding:20px; color:rgba(255,255,255,0.2); font-size:13px; }
    .footer { text-align:center; font-size:12px; color:rgba(255,255,255,0.2); margin-top:25px; }

    @keyframes fadeUp   { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:translateY(0)} }
    @keyframes fadeDown { from{opacity:0;transform:translateY(-20px)} to{opacity:1;transform:translateY(0)} }
  </style>
</head>
<body>

<div class="sidebar">
  <div class="logo">
    <h2>RSKF <span>Group</span> of<br>Companies Ltd.</h2>
    <p>Employee Portal</p>
  </div>
  <div class="user-box">
    <div class="avatar-row">
      <div class="avatar">👷</div>
      <div class="info">
        <div class="name"><?php echo $employee['full_name']; ?></div>
        <div class="role"><?php echo $employee['designation']; ?></div>
      </div>
    </div>
    <div class="dept-badge">🏢 <?php echo $employee['dept_name']; ?></div>
  </div>
  <nav>
    <div class="section-title">Menu</div>
    <a href="employee_dashboard.php" class="active">🏠 Dashboard</a>
    <a href="#profile">👤 My Profile</a>
    <a href="#colleagues">👥 My Team</a>
    <a href="#company">📊 Company Stats</a>
    <div class="section-title">Account</div>
    <a href="employee_logout.php">🚪 Logout</a>
  </nav>
</div>

<div class="main">
  <div class="topbar">
    <div>
      <h1>Welcome, <?php echo $employee['full_name']; ?>!</h1>
      <p><?php echo $employee['designation']; ?> — <?php echo $employee['dept_name']; ?> Department</p>
    </div>
    <div class="date-badge">📅 <?php echo date('l, d F Y'); ?></div>
  </div>

  <!-- Welcome Banner -->
  <div class="welcome-banner">
    <div class="left">
      <h2>👋 Good to see you!</h2>
      <p>You have been with RSKF Group since <?php echo date('d M Y', strtotime($employee['join_date'])); ?></p>
    </div>
    <div class="right">
      <div class="salary">Rs. <?php echo number_format($employee['salary'], 0); ?></div>
      <div class="salary-label">Monthly Salary</div>
    </div>
  </div>

  <!-- Cards -->
  <div class="cards">
    <div class="card">
      <div class="card-top"><div class="card-icon icon-green">🏢</div></div>
      <div class="card-num"><?php echo $employee['dept_name']; ?></div>
      <div class="card-label">Department</div>
    </div>
    <div class="card">
      <div class="card-top"><div class="card-icon icon-blue">📅</div></div>
      <div class="card-num"><?php
        $join  = new DateTime($employee['join_date']);
        $now   = new DateTime();
        $diff  = $join->diff($now);
        echo $diff->y > 0 ? $diff->y.' Years' : $diff->m.' Months';
      ?></div>
      <div class="card-label">Experience at RSKF</div>
    </div>
    <div class="card">
      <div class="card-top"><div class="card-icon icon-purple">✅</div></div>
      <div class="card-num">
        <?php if ($employee['status'] == 'active'): ?>
          <span class="status-active">Active</span>
        <?php else: ?>
          <span class="status-inactive">Inactive</span>
        <?php endif; ?>
      </div>
      <div class="card-label">Employment Status</div>
    </div>
  </div>

  <!-- Info + Colleagues -->
  <div class="info-grid">

    <!-- My Profile -->
    <div class="section-box" id="profile">
      <h3>👤 My Information</h3>
      <div class="info-items">
        <div class="info-item"><label>Full Name</label><p><?php echo $employee['full_name']; ?></p></div>
        <div class="info-item"><label>CNIC</label><p><?php echo $employee['cnic']; ?></p></div>
        <div class="info-item"><label>Email</label><p><?php echo $employee['email'] ?: '—'; ?></p></div>
        <div class="info-item"><label>Phone</label><p><?php echo $employee['phone'] ?: '—'; ?></p></div>
        <div class="info-item"><label>Designation</label><p><?php echo $employee['designation']; ?></p></div>
        <div class="info-item"><label>Department</label><p><?php echo $employee['dept_name']; ?></p></div>
        <div class="info-item"><label>Join Date</label><p><?php echo date('d M Y', strtotime($employee['join_date'])); ?></p></div>
        <div class="info-item"><label>Salary</label><p style="color:#2ecc71">Rs. <?php echo number_format($employee['salary'],0); ?></p></div>
      </div>
    </div>

    <!-- Colleagues -->
    <div class="section-box" id="colleagues">
      <h3>👥 My Team — <?php echo $employee['dept_name']; ?></h3>
      <div class="colleagues-list">
        <?php
        if (mysqli_num_rows($colleagues) == 0) {
            echo "<div class='empty'>No other colleagues in this department</div>";
        }
        while ($c = mysqli_fetch_assoc($colleagues)):
        ?>
        <div class="colleague-item">
          <div class="colleague-avatar">👤</div>
          <div class="colleague-info">
            <div class="name"><?php echo $c['full_name']; ?></div>
            <div class="desig"><?php echo $c['designation']; ?></div>
          </div>
        </div>
        <?php endwhile; ?>
      </div>
    </div>

  </div>

  <!-- Company Stats -->
  <div class="section-box" id="company">
    <h3>📊 Company Overview</h3>
    <div class="company-stats">
      <div class="stat-item">
        <div class="num counter" data-target="<?php echo $total_products; ?>">0</div>
        <div class="label">Products in Stock</div>
      </div>
      <div class="stat-item">
        <div class="num counter" data-target="<?php echo $total_orders; ?>">0</div>
        <div class="label">Total Orders</div>
      </div>
      <div class="stat-item">
        <div class="num counter" data-target="<?php echo $total_customers; ?>">0</div>
        <div class="label">Total Customers</div>
      </div>
    </div>
  </div>

  <div class="footer">&copy; <?php echo date('Y'); ?> RSKF Group of Companies Ltd.</div>
</div>

<script>
function animateCounter(el) {
    const target = parseInt(el.dataset.target);
    if (target === 0) { el.textContent = '0'; return; }
    const step = target / (1500 / 16);
    let current = 0;
    const timer = setInterval(() => {
        current += step;
        if (current >= target) { current = target; clearInterval(timer); }
        el.textContent = Math.floor(current);
    }, 16);
}
window.addEventListener('load', () => {
    setTimeout(() => { document.querySelectorAll('.counter').forEach(animateCounter); }, 300);
});
</script>
</body>
</html>
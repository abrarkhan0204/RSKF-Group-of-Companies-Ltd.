<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

$total_customers = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM customers"))[0];
$total_products  = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM products"))[0];
$total_employees = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM employees"))[0];
$total_suppliers = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM suppliers"))[0];
$total_orders    = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM orders"))[0];
$low_stock       = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM products WHERE stock_quantity <= reorder_level"))[0];
$total_revenue   = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(total_amount) FROM orders WHERE payment_status='paid'"))[0];
$pending_orders  = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM orders WHERE payment_status='pending'"))[0];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>RSKF — Admin Dashboard</title>
  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family:'Segoe UI',Arial,sans-serif; background:#0f0f0f; color:white; display:flex; }
    .sidebar { width:260px; min-height:100vh; background:#1a1a1a; position:fixed; top:0; left:0; border-right:1px solid rgba(255,255,255,0.06); }
    .sidebar .logo { padding:28px 24px; border-bottom:1px solid rgba(255,255,255,0.06); }
    .sidebar .logo h2 { font-size:16px; font-weight:700; color:white; line-height:1.4; }
    .sidebar .logo span { color:#c0392b; }
    .sidebar .logo p { font-size:11px; color:rgba(255,255,255,0.4); margin-top:4px; }
    .sidebar .admin-box { padding:16px 24px; background:rgba(192,57,43,0.1); border-bottom:1px solid rgba(255,255,255,0.06); display:flex; align-items:center; gap:12px; }
    .admin-box .avatar { width:38px; height:38px; border-radius:50%; background:linear-gradient(135deg,#c0392b,#7b0000); display:flex; align-items:center; justify-content:center; font-size:16px; }
    .admin-box .info .name { font-size:13px; font-weight:600; }
    .admin-box .info .role { font-size:11px; color:rgba(255,255,255,0.4); }
    .sidebar nav { padding:12px 0; }
    .sidebar nav .section-title { padding:14px 24px 6px; font-size:10px; text-transform:uppercase; color:rgba(255,255,255,0.3); letter-spacing:1.5px; }
    .sidebar nav a { display:flex; align-items:center; gap:10px; padding:12px 24px; color:rgba(255,255,255,0.6); text-decoration:none; font-size:13px; border-left:3px solid transparent; transition:all 0.2s; }
    .sidebar nav a:hover { background:rgba(255,255,255,0.05); color:white; border-left-color:rgba(192,57,43,0.5); }
    .sidebar nav a.active { background:rgba(192,57,43,0.12); color:white; border-left-color:#c0392b; }
    .sidebar nav a .nav-icon { font-size:16px; width:20px; text-align:center; }
    .main { margin-left:260px; flex:1; padding:35px; background:#0f0f0f; min-height:100vh; }
    .topbar { display:flex; justify-content:space-between; align-items:center; margin-bottom:35px; opacity:0; animation:fadeDown 0.6s ease 0.1s forwards; }
    .topbar .left h1 { font-size:24px; font-weight:700; color:white; }
    .topbar .left p  { font-size:13px; color:rgba(255,255,255,0.4); margin-top:3px; }
    .topbar .date-badge { background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.08); padding:8px 16px; border-radius:8px; font-size:13px; color:rgba(255,255,255,0.6); }
    .cards { display:grid; grid-template-columns:repeat(4,1fr); gap:18px; margin-bottom:25px; }
    .card { background:#1a1a1a; border:1px solid rgba(255,255,255,0.06); border-radius:12px; padding:22px; position:relative; overflow:hidden; opacity:0; animation:fadeUp 0.6s ease forwards; transition:transform 0.2s, border-color 0.2s; }
    .card:hover { transform:translateY(-3px); border-color:rgba(192,57,43,0.3); }
    .card:nth-child(1){animation-delay:0.1s} .card:nth-child(2){animation-delay:0.2s}
    .card:nth-child(3){animation-delay:0.3s} .card:nth-child(4){animation-delay:0.4s}
    .card:nth-child(5){animation-delay:0.5s} .card:nth-child(6){animation-delay:0.6s}
    .card:nth-child(7){animation-delay:0.7s} .card:nth-child(8){animation-delay:0.8s}
    .card .card-icon { width:44px; height:44px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:20px; margin-bottom:15px; }
    .card .card-num { font-size:30px; font-weight:800; color:white; line-height:1; margin-bottom:6px; }
    .card .card-label { font-size:12px; color:rgba(255,255,255,0.4); }
    .card .card-glow { position:absolute; bottom:-20px; right:-20px; width:80px; height:80px; border-radius:50%; opacity:0.08; filter:blur(20px); }
    .card.red    .card-icon { background:rgba(192,57,43,0.2); } .card.red    .card-glow { background:#c0392b; }
    .card.blue   .card-icon { background:rgba(41,128,185,0.2); } .card.blue   .card-glow { background:#2980b9; }
    .card.green  .card-icon { background:rgba(39,174,96,0.2); }  .card.green  .card-glow { background:#27ae60; }
    .card.orange .card-icon { background:rgba(243,156,18,0.2); } .card.orange .card-glow { background:#f39c12; }
    .card.purple .card-icon { background:rgba(142,68,173,0.2); } .card.purple .card-glow { background:#8e44ad; }
    .card.teal   .card-icon { background:rgba(26,188,156,0.2); } .card.teal   .card-glow { background:#1abc9c; }
    .card.alert  { border-color:rgba(231,76,60,0.4); background:rgba(231,76,60,0.08); }
    .revenue-card { background:linear-gradient(135deg,#7b0000,#c0392b); border:none; grid-column:span 2; }
    .revenue-card .card-num { font-size:26px; }
    .revenue-card .card-label { color:rgba(255,255,255,0.7); }
    .revenue-card .card-icon { background:rgba(255,255,255,0.15); }

    /* Analytics Banner */
    .analytics-banner {
      background:linear-gradient(135deg,rgba(243,156,18,0.12),rgba(192,57,43,0.08));
      border:1px solid rgba(243,156,18,0.2); border-radius:12px;
      padding:18px 25px; margin-bottom:25px;
      display:flex; justify-content:space-between; align-items:center;
      opacity:0; animation:fadeUp 0.6s ease 0.5s forwards;
    }
    .analytics-banner .left h3 { font-size:15px; font-weight:700; color:#f39c12; margin-bottom:4px; }
    .analytics-banner .left p  { font-size:13px; color:rgba(255,255,255,0.4); }
    .analytics-banner .btn-analytics {
      background:linear-gradient(135deg,#f39c12,#e67e22);
      color:white; padding:10px 24px; border-radius:8px;
      text-decoration:none; font-size:13px; font-weight:700;
      transition:all 0.2s; white-space:nowrap;
    }
    .analytics-banner .btn-analytics:hover { transform:translateY(-2px); box-shadow:0 6px 16px rgba(243,156,18,0.3); }

    .tables-row { display:grid; grid-template-columns:1fr 1fr; gap:18px; margin-bottom:18px; }
    .table-box { background:#1a1a1a; border:1px solid rgba(255,255,255,0.06); border-radius:12px; padding:22px; opacity:0; animation:fadeUp 0.6s ease 0.5s forwards; }
    .table-box h3 { font-size:15px; font-weight:600; color:white; margin-bottom:18px; padding-bottom:12px; border-bottom:1px solid rgba(255,255,255,0.06); display:flex; justify-content:space-between; align-items:center; }
    .table-box h3 a { font-size:12px; color:#c0392b; text-decoration:none; }
    table { width:100%; border-collapse:collapse; font-size:13px; }
    table th { text-align:left; padding:8px 10px; color:rgba(255,255,255,0.3); font-weight:500; font-size:11px; text-transform:uppercase; letter-spacing:0.5px; border-bottom:1px solid rgba(255,255,255,0.06); }
    table td { padding:10px 10px; border-bottom:1px solid rgba(255,255,255,0.04); color:rgba(255,255,255,0.75); }
    table tr:last-child td { border-bottom:none; }
    table tr:hover td { background:rgba(255,255,255,0.03); }
    .badge { padding:3px 10px; border-radius:20px; font-size:11px; font-weight:600; }
    .badge.paid      { background:rgba(39,174,96,0.15); color:#2ecc71; }
    .badge.pending   { background:rgba(243,156,18,0.15); color:#f39c12; }
    .badge.partial   { background:rgba(41,128,185,0.15); color:#3498db; }
    .badge.delivered { background:rgba(26,188,156,0.15); color:#1abc9c; }
    .badge.ok        { background:rgba(39,174,96,0.15); color:#2ecc71; }
    .badge.low       { background:rgba(231,76,60,0.15); color:#e74c3c; }
    .badge.out       { background:rgba(142,68,173,0.15); color:#9b59b6; }
    .footer { text-align:center; font-size:12px; color:rgba(255,255,255,0.2); margin-top:25px; }
    @keyframes fadeUp   { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:translateY(0)} }
    @keyframes fadeDown { from{opacity:0;transform:translateY(-20px)} to{opacity:1;transform:translateY(0)} }
  </style>
</head>
<body>

<div class="sidebar">
  <div class="logo">
    <h2>RSKF <span>Group</span> of<br>Companies Ltd.</h2>
    <p>Admin Management Panel</p>
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
    <a href="dashboard.php" class="active"><span class="nav-icon">📊</span> Dashboard</a>
    <a href="analytics.php"><span class="nav-icon">📈</span> Analytics</a>
    <div class="section-title">Manage</div>
    <a href="customers.php"><span class="nav-icon">👥</span> Customers</a>
    <a href="orders.php"><span class="nav-icon">🧾</span> Orders</a>
    <a href="products.php"><span class="nav-icon">📦</span> Products</a>
    <a href="purchases.php"><span class="nav-icon">📤</span> Purchase Orders</a>
    <a href="suppliers.php"><span class="nav-icon">🏭</span> Suppliers</a>
    <a href="employees.php"><span class="nav-icon">👷</span> Employees</a>
    <div class="section-title">Account</div>
    <a href="logout.php"><span class="nav-icon">🚪</span> Logout</a>
  </nav>
</div>

<div class="main">
  <div class="topbar">
    <div class="left">
      <h1>Dashboard</h1>
      <p>Welcome back, <?php echo $_SESSION['full_name']; ?>! Here's what's happening.</p>
    </div>
    <div class="date-badge">📅 <?php echo date('l, d F Y'); ?></div>
  </div>

  <!-- Stats Cards -->
  <div class="cards">
    <div class="card blue">
      <div class="card-icon">👥</div>
      <div class="card-num counter" data-target="<?php echo $total_customers; ?>">0</div>
      <div class="card-label">Total Customers</div>
      <div class="card-glow"></div>
    </div>
    <div class="card green">
      <div class="card-icon">📦</div>
      <div class="card-num counter" data-target="<?php echo $total_products; ?>">0</div>
      <div class="card-label">Total Products</div>
      <div class="card-glow"></div>
    </div>
    <div class="card purple">
      <div class="card-icon">👷</div>
      <div class="card-num counter" data-target="<?php echo $total_employees; ?>">0</div>
      <div class="card-label">Total Employees</div>
      <div class="card-glow"></div>
    </div>
    <div class="card orange">
      <div class="card-icon">🏭</div>
      <div class="card-num counter" data-target="<?php echo $total_suppliers; ?>">0</div>
      <div class="card-label">Total Suppliers</div>
      <div class="card-glow"></div>
    </div>
    <div class="card teal">
      <div class="card-icon">🧾</div>
      <div class="card-num counter" data-target="<?php echo $total_orders; ?>">0</div>
      <div class="card-label">Total Orders</div>
      <div class="card-glow"></div>
    </div>
    <div class="card red alert">
      <div class="card-icon">⚠️</div>
      <div class="card-num counter" data-target="<?php echo $low_stock; ?>">0</div>
      <div class="card-label">Low Stock Alerts</div>
      <div class="card-glow"></div>
    </div>
    <div class="card orange">
      <div class="card-icon">⏳</div>
      <div class="card-num counter" data-target="<?php echo $pending_orders; ?>">0</div>
      <div class="card-label">Pending Orders</div>
      <div class="card-glow"></div>
    </div>
    <div class="card revenue-card">
      <div class="card-icon">💰</div>
      <div class="card-num">Rs. <?php echo number_format($total_revenue ?? 0, 0); ?></div>
      <div class="card-label">Total Revenue (Paid Orders)</div>
    </div>
  </div>

  <!-- Analytics Banner -->
  <div class="analytics-banner">
    <div class="left">
      <h3>📈 View Detailed Analytics</h3>
      <p>Sales trends, revenue charts, supplier performance, stock analysis and more</p>
    </div>
    <a href="analytics.php" class="btn-analytics">Open Analytics →</a>
  </div>

  <!-- Tables -->
  <div class="tables-row">
    <div class="table-box">
      <h3>Recent Customers <a href="customers.php">View All →</a></h3>
      <table>
        <tr><th>#</th><th>Name</th><th>City</th><th>Phone</th></tr>
        <?php
        $result = mysqli_query($conn, "SELECT * FROM customers ORDER BY id DESC LIMIT 5");
        $i = 1;
        if (mysqli_num_rows($result) == 0) {
            echo "<tr><td colspan='4' style='text-align:center;padding:20px;color:rgba(255,255,255,0.3)'>No customers yet</td></tr>";
        }
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr><td>{$i}</td><td>{$row['full_name']}</td><td>{$row['city']}</td><td>{$row['phone']}</td></tr>";
            $i++;
        }
        ?>
      </table>
    </div>
    <div class="table-box">
      <h3>Stock Status <a href="products.php">View All →</a></h3>
      <table>
        <tr><th>Product</th><th>Stock</th><th>Status</th></tr>
        <?php
        $result = mysqli_query($conn, "SELECT name, stock_quantity, reorder_level FROM products ORDER BY stock_quantity ASC LIMIT 6");
        while ($row = mysqli_fetch_assoc($result)) {
            if ($row['stock_quantity'] == 0)
                $s = "<span class='badge out'>Out of Stock</span>";
            elseif ($row['stock_quantity'] <= $row['reorder_level'])
                $s = "<span class='badge low'>Low Stock</span>";
            else
                $s = "<span class='badge ok'>OK</span>";
            echo "<tr><td>{$row['name']}</td><td>{$row['stock_quantity']}</td><td>{$s}</td></tr>";
        }
        ?>
      </table>
    </div>
  </div>

  <div class="tables-row">
    <div class="table-box">
      <h3>Recent Orders <a href="orders.php">View All →</a></h3>
      <table>
        <tr><th>Order ID</th><th>Customer</th><th>Amount</th><th>Status</th></tr>
        <?php
        $result = mysqli_query($conn, "SELECT o.*, c.full_name FROM orders o LEFT JOIN customers c ON o.customer_id=c.id ORDER BY o.id DESC LIMIT 5");
        if (mysqli_num_rows($result) == 0) {
            echo "<tr><td colspan='4' style='text-align:center;padding:20px;color:rgba(255,255,255,0.3)'>No orders yet</td></tr>";
        }
        while ($row = mysqli_fetch_assoc($result)) {
            $s = $row['payment_status'];
            echo "<tr><td>ORD-{$row['id']}</td><td>{$row['full_name']}</td><td>Rs. ".number_format($row['total_amount'],0)."</td><td><span class='badge {$s}'>".ucfirst($s)."</span></td></tr>";
        }
        ?>
      </table>
    </div>
    <div class="table-box">
      <h3>Purchase Orders <a href="purchases.php">View All →</a></h3>
      <table>
        <tr><th>PO ID</th><th>Supplier</th><th>Product</th><th>Status</th></tr>
        <?php
        $result = mysqli_query($conn, "SELECT pu.*, s.company_name, p.name as pname FROM purchases pu LEFT JOIN suppliers s ON pu.supplier_id=s.id LEFT JOIN products p ON pu.product_id=p.id ORDER BY pu.id DESC LIMIT 5");
        if (mysqli_num_rows($result) == 0) {
            echo "<tr><td colspan='4' style='text-align:center;padding:20px;color:rgba(255,255,255,0.3)'>No purchases yet</td></tr>";
        }
        while ($row = mysqli_fetch_assoc($result)) {
            $s = $row['status'] ?? 'pending';
            echo "<tr><td>PO-{$row['id']}</td><td>{$row['company_name']}</td><td>{$row['pname']}</td><td><span class='badge {$s}'>".ucfirst($s)."</span></td></tr>";
        }
        ?>
      </table>
    </div>
  </div>

  <div class="footer">&copy; <?php echo date('Y'); ?> RSKF Group of Companies Ltd. — All Rights Reserved</div>
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
    setTimeout(() => { document.querySelectorAll('.counter').forEach(animateCounter); }, 400);
});
</script>
</body>
</html>
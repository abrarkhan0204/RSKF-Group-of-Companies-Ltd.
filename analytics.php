<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

// ── Sales Data (Last 6 Months) ──
$sales_labels = [];
$sales_data   = [];
for ($i = 5; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $label = date('M Y', strtotime("-$i months"));
    $result = mysqli_fetch_row(mysqli_query($conn,
        "SELECT COALESCE(SUM(total_amount),0) FROM orders
         WHERE DATE_FORMAT(order_date,'%Y-%m')='$month'
         AND payment_status='paid'"))[0];
    $sales_labels[] = $label;
    $sales_data[]   = (float)$result;
}

// ── Orders Per Month ──
$orders_data = [];
for ($i = 5; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $result = mysqli_fetch_row(mysqli_query($conn,
        "SELECT COUNT(*) FROM orders
         WHERE DATE_FORMAT(order_date,'%Y-%m')='$month'"))[0];
    $orders_data[] = (int)$result;
}

// ── Payment Status Breakdown ──
$paid      = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM orders WHERE payment_status='paid'"))[0];
$pending   = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM orders WHERE payment_status='pending'"))[0];
$partial   = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM orders WHERE payment_status='partial'"))[0];
$delivered = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM orders WHERE payment_status='delivered'"))[0];
$cancelled = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM orders WHERE payment_status='cancelled'"))[0];

// ── Top Products ──
$top_products = mysqli_query($conn,
    "SELECT p.name, SUM(oi.quantity) as total_qty, SUM(oi.subtotal) as total_sales
     FROM order_items oi
     LEFT JOIN products p ON oi.product_id=p.id
     GROUP BY oi.product_id ORDER BY total_sales DESC LIMIT 5");

// ── Top Customers ──
$top_customers = mysqli_query($conn,
    "SELECT c.full_name, COUNT(o.id) as total_orders, SUM(o.total_amount) as total_spent
     FROM orders o LEFT JOIN customers c ON o.customer_id=c.id
     GROUP BY o.customer_id ORDER BY total_spent DESC LIMIT 5");

// ── Stock Levels ──
$stock_data = mysqli_query($conn,
    "SELECT p.name, p.stock_quantity, p.reorder_level
     FROM products p ORDER BY p.stock_quantity ASC");

// ── Supplier Performance ──
$supplier_data = mysqli_query($conn,
    "SELECT s.company_name,
            COUNT(pu.id) as total_orders,
            SUM(pu.quantity) as total_qty,
            SUM(pu.total_cost) as total_value,
            SUM(CASE WHEN pu.status='received' THEN 1 ELSE 0 END) as completed
     FROM purchases pu LEFT JOIN suppliers s ON pu.supplier_id=s.id
     GROUP BY pu.supplier_id ORDER BY total_value DESC");

// ── Financial Overview ──
$total_revenue  = mysqli_fetch_row(mysqli_query($conn, "SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE payment_status='paid'"))[0];
$total_purchase = mysqli_fetch_row(mysqli_query($conn, "SELECT COALESCE(SUM(total_cost),0) FROM purchases WHERE status='received'"))[0];
$total_pending  = mysqli_fetch_row(mysqli_query($conn, "SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE payment_status='pending'"))[0];
$total_orders   = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM orders"))[0];
$total_customers= mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM customers"))[0];
$profit         = $total_revenue - $total_purchase;

// ── New Customers Per Month ──
$new_customers = [];
for ($i = 5; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $result = mysqli_fetch_row(mysqli_query($conn,
        "SELECT COUNT(*) FROM customers
         WHERE DATE_FORMAT(created_at,'%Y-%m')='$month'"))[0];
    $new_customers[] = (int)$result;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>RSKF — Analytics</title>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
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
    .date-badge { background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.08); padding:8px 16px; border-radius:8px; font-size:13px; color:rgba(255,255,255,0.6); }

    /* KPI Cards */
    .kpi-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:18px; margin-bottom:25px; }
    .kpi-card { background:#1a1a1a; border:1px solid rgba(255,255,255,0.06); border-radius:12px; padding:22px; opacity:0; animation:fadeUp 0.6s ease forwards; transition:transform 0.2s; }
    .kpi-card:hover { transform:translateY(-3px); }
    .kpi-card:nth-child(1){animation-delay:0.1s}
    .kpi-card:nth-child(2){animation-delay:0.2s}
    .kpi-card:nth-child(3){animation-delay:0.3s}
    .kpi-card:nth-child(4){animation-delay:0.4s}
    .kpi-card:nth-child(5){animation-delay:0.5s}
    .kpi-card:nth-child(6){animation-delay:0.6s}
    .kpi-icon { width:44px; height:44px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:20px; margin-bottom:14px; }
    .kpi-num   { font-size:26px; font-weight:800; margin-bottom:4px; }
    .kpi-label { font-size:12px; color:rgba(255,255,255,0.4); }
    .kpi-sub   { font-size:11px; margin-top:6px; }
    .green { color:#2ecc71; } .red { color:#e74c3c; } .yellow { color:#f39c12; } .blue { color:#3498db; }
    .icon-green  { background:rgba(39,174,96,0.15); }
    .icon-red    { background:rgba(231,76,60,0.15); }
    .icon-yellow { background:rgba(243,156,18,0.15); }
    .icon-blue   { background:rgba(52,152,219,0.15); }
    .icon-purple { background:rgba(155,89,182,0.15); }
    .icon-teal   { background:rgba(26,188,156,0.15); }
    .profit-card { border-color:rgba(39,174,96,0.2); background:rgba(39,174,96,0.05); }
    .loss-card   { border-color:rgba(231,76,60,0.2); background:rgba(231,76,60,0.05); }

    /* Charts */
    .charts-row { display:grid; grid-template-columns:2fr 1fr; gap:18px; margin-bottom:18px; }
    .charts-row-3 { display:grid; grid-template-columns:repeat(3,1fr); gap:18px; margin-bottom:18px; }
    .chart-box { background:#1a1a1a; border:1px solid rgba(255,255,255,0.06); border-radius:12px; padding:22px; opacity:0; animation:fadeUp 0.6s ease 0.3s forwards; }
    .chart-box h3 { font-size:15px; font-weight:600; margin-bottom:5px; }
    .chart-box .sub { font-size:12px; color:rgba(255,255,255,0.3); margin-bottom:18px; }
    .chart-wrap { position:relative; }

    /* Tables */
    .tables-row { display:grid; grid-template-columns:1fr 1fr; gap:18px; margin-bottom:18px; }
    .table-box { background:#1a1a1a; border:1px solid rgba(255,255,255,0.06); border-radius:12px; padding:22px; opacity:0; animation:fadeUp 0.6s ease 0.4s forwards; }
    .table-box h3 { font-size:15px; font-weight:600; margin-bottom:5px; }
    .table-box .sub { font-size:12px; color:rgba(255,255,255,0.3); margin-bottom:18px; }
    table { width:100%; border-collapse:collapse; font-size:13px; }
    table th { text-align:left; padding:8px 10px; color:rgba(255,255,255,0.3); font-weight:500; font-size:11px; text-transform:uppercase; letter-spacing:0.5px; border-bottom:1px solid rgba(255,255,255,0.06); }
    table td { padding:10px 10px; border-bottom:1px solid rgba(255,255,255,0.04); color:rgba(255,255,255,0.75); }
    table tr:last-child td { border-bottom:none; }
    table tr:hover td { background:rgba(255,255,255,0.03); }

    /* Progress Bar */
    .progress-bar { background:rgba(255,255,255,0.06); border-radius:4px; height:6px; margin-top:5px; overflow:hidden; }
    .progress-fill { height:100%; border-radius:4px; transition:width 1s ease; }

    /* Stock Items */
    .stock-item { display:flex; align-items:center; gap:12px; padding:10px 0; border-bottom:1px solid rgba(255,255,255,0.04); }
    .stock-item:last-child { border-bottom:none; }
    .stock-name { flex:1; font-size:13px; color:rgba(255,255,255,0.8); }
    .stock-num  { font-size:13px; font-weight:700; min-width:50px; text-align:right; }
    .stock-bar  { flex:2; }

    /* Supplier Score */
    .supplier-item { display:flex; justify-content:space-between; align-items:center; padding:12px 0; border-bottom:1px solid rgba(255,255,255,0.04); }
    .supplier-item:last-child { border-bottom:none; }
    .supplier-name { font-size:13px; color:rgba(255,255,255,0.8); }
    .supplier-stats { text-align:right; }
    .supplier-stats .val { font-size:14px; font-weight:700; color:#f39c12; }
    .supplier-stats .cnt { font-size:11px; color:rgba(255,255,255,0.3); }
    .score-badge { padding:3px 10px; border-radius:20px; font-size:11px; font-weight:600; }
    .score-high { background:rgba(39,174,96,0.15); color:#2ecc71; }
    .score-med  { background:rgba(243,156,18,0.15); color:#f39c12; }
    .score-low  { background:rgba(231,76,60,0.15); color:#e74c3c; }

    .empty { text-align:center; padding:30px; color:rgba(255,255,255,0.2); font-size:14px; }
    .footer { text-align:center; font-size:12px; color:rgba(255,255,255,0.2); margin-top:25px; }

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
    <a href="analytics.php" class="active">📈 Analytics</a>
    <div class="section-title">Manage</div>
    <a href="customers.php">👥 Customers</a>
    <a href="orders.php">🧾 Orders</a>
    <a href="products.php">📦 Products</a>
    <a href="purchases.php">📤 Purchase Orders</a>
    <a href="suppliers.php">🏭 Suppliers</a>
    <a href="employees.php">👷 Employees</a>
    <div class="section-title">Account</div>
    <a href="logout.php">🚪 Logout</a>
  </nav>
</div>

<div class="main">
  <div class="topbar">
    <div>
      <h1>📈 Analytics</h1>
      <p>Company performance overview — last 6 months</p>
    </div>
    <div class="date-badge">📅 <?php echo date('l, d F Y'); ?></div>
  </div>

  <!-- KPI Cards -->
  <div class="kpi-grid">
    <div class="kpi-card <?php echo $profit >= 0 ? 'profit-card' : 'loss-card'; ?>">
      <div class="kpi-icon <?php echo $profit >= 0 ? 'icon-green' : 'icon-red'; ?>">💰</div>
      <div class="kpi-num <?php echo $profit >= 0 ? 'green' : 'red'; ?>">
        Rs. <?php echo number_format(abs($profit), 0); ?>
      </div>
      <div class="kpi-label"><?php echo $profit >= 0 ? 'Net Profit' : 'Net Loss'; ?></div>
      <div class="kpi-sub <?php echo $profit >= 0 ? 'green' : 'red'; ?>">
        <?php echo $profit >= 0 ? '↑ Revenue exceeds costs' : '↓ Costs exceed revenue'; ?>
      </div>
    </div>
    <div class="kpi-card">
      <div class="kpi-icon icon-blue">📈</div>
      <div class="kpi-num blue">Rs. <?php echo number_format($total_revenue, 0); ?></div>
      <div class="kpi-label">Total Revenue</div>
      <div class="kpi-sub" style="color:rgba(255,255,255,0.3)">From paid orders</div>
    </div>
    <div class="kpi-card">
      <div class="kpi-icon icon-red">📉</div>
      <div class="kpi-num red">Rs. <?php echo number_format($total_purchase, 0); ?></div>
      <div class="kpi-label">Total Purchases</div>
      <div class="kpi-sub" style="color:rgba(255,255,255,0.3)">Stock purchase cost</div>
    </div>
    <div class="kpi-card">
      <div class="kpi-icon icon-yellow">⏳</div>
      <div class="kpi-num yellow">Rs. <?php echo number_format($total_pending, 0); ?></div>
      <div class="kpi-label">Pending Payments</div>
      <div class="kpi-sub" style="color:rgba(255,255,255,0.3)">Yet to be collected</div>
    </div>
    <div class="kpi-card">
      <div class="kpi-icon icon-teal">🧾</div>
      <div class="kpi-num" style="color:#1abc9c"><?php echo $total_orders; ?></div>
      <div class="kpi-label">Total Orders</div>
      <div class="kpi-sub" style="color:rgba(255,255,255,0.3)">All time</div>
    </div>
    <div class="kpi-card">
      <div class="kpi-icon icon-purple">👥</div>
      <div class="kpi-num" style="color:#9b59b6"><?php echo $total_customers; ?></div>
      <div class="kpi-label">Total Customers</div>
      <div class="kpi-sub" style="color:rgba(255,255,255,0.3)">Registered accounts</div>
    </div>
  </div>

  <!-- Revenue + Payment Breakdown -->
  <div class="charts-row">
    <div class="chart-box">
      <h3>Revenue & Orders Trend</h3>
      <div class="sub">Last 6 months performance</div>
      <div class="chart-wrap"><canvas id="revenueChart" height="100"></canvas></div>
    </div>
    <div class="chart-box">
      <h3>Payment Status</h3>
      <div class="sub">Order breakdown</div>
      <div class="chart-wrap"><canvas id="paymentChart" height="200"></canvas></div>
    </div>
  </div>

  <!-- Stock + Customers -->
  <div class="charts-row">
    <div class="chart-box">
      <h3>New Customers Per Month</h3>
      <div class="sub">Customer acquisition trend</div>
      <div class="chart-wrap"><canvas id="customersChart" height="100"></canvas></div>
    </div>
    <div class="chart-box">
      <h3>Stock Levels</h3>
      <div class="sub">Current inventory status</div>
      <?php
      mysqli_data_seek($stock_data, 0);
      $max_stock = 1;
      $stocks = [];
      while ($row = mysqli_fetch_assoc($stock_data)) {
          $stocks[] = $row;
          if ($row['stock_quantity'] > $max_stock) $max_stock = $row['stock_quantity'];
      }
      foreach ($stocks as $row):
          $pct = $max_stock > 0 ? ($row['stock_quantity'] / $max_stock * 100) : 0;
          $color = $row['stock_quantity'] == 0 ? '#e74c3c' :
                   ($row['stock_quantity'] <= $row['reorder_level'] ? '#f39c12' : '#2ecc71');
      ?>
      <div class="stock-item">
        <div class="stock-name"><?php echo strlen($row['name']) > 25 ? substr($row['name'],0,25).'...' : $row['name']; ?></div>
        <div class="stock-bar">
          <div class="progress-bar">
            <div class="progress-fill" style="width:<?php echo $pct; ?>%;background:<?php echo $color; ?>"></div>
          </div>
        </div>
        <div class="stock-num" style="color:<?php echo $color; ?>"><?php echo $row['stock_quantity']; ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Top Products + Top Customers -->
  <div class="tables-row">
    <div class="table-box">
      <h3>🏆 Top Products</h3>
      <div class="sub">By sales revenue</div>
      <table>
        <tr><th>#</th><th>Product</th><th>Qty Sold</th><th>Revenue</th></tr>
        <?php
        $i = 1;
        if (mysqli_num_rows($top_products) == 0) {
            echo "<tr><td colspan='4' class='empty'>No sales data yet</td></tr>";
        }
        while ($row = mysqli_fetch_assoc($top_products)):
        ?>
        <tr>
          <td style="color:<?php echo $i==1?'#f39c12':($i==2?'rgba(255,255,255,0.5)':'rgba(255,255,255,0.3)'); ?>; font-weight:700"><?php echo $i==1?'🥇':($i==2?'🥈':($i==3?'🥉':$i)); ?></td>
          <td><?php echo strlen($row['name'])>30?substr($row['name'],0,30).'...':$row['name']; ?></td>
          <td><?php echo number_format($row['total_qty'],0); ?></td>
          <td style="color:#2ecc71;font-weight:700">Rs. <?php echo number_format($row['total_sales'],0); ?></td>
        </tr>
        <?php $i++; endwhile; ?>
      </table>
    </div>

    <div class="table-box">
      <h3>👥 Top Customers</h3>
      <div class="sub">By total order value</div>
      <table>
        <tr><th>#</th><th>Customer</th><th>Orders</th><th>Total Spent</th></tr>
        <?php
        $i = 1;
        if (mysqli_num_rows($top_customers) == 0) {
            echo "<tr><td colspan='4' class='empty'>No customer data yet</td></tr>";
        }
        while ($row = mysqli_fetch_assoc($top_customers)):
        ?>
        <tr>
          <td style="color:<?php echo $i==1?'#f39c12':'rgba(255,255,255,0.3)'; ?>; font-weight:700"><?php echo $i==1?'🥇':($i==2?'🥈':($i==3?'🥉':$i)); ?></td>
          <td><?php echo $row['full_name']; ?></td>
          <td><?php echo $row['total_orders']; ?></td>
          <td style="color:#3498db;font-weight:700">Rs. <?php echo number_format($row['total_spent'],0); ?></td>
        </tr>
        <?php $i++; endwhile; ?>
      </table>
    </div>
  </div>

  <!-- Supplier Performance -->
  <div class="chart-box" style="margin-bottom:18px">
    <h3>🏭 Supplier Performance</h3>
    <div class="sub">Purchase volume and reliability</div>
    <?php
    $sup_count = 0;
    if (mysqli_num_rows($supplier_data) == 0): ?>
      <div class="empty">No supplier data yet</div>
    <?php else:
      while ($row = mysqli_fetch_assoc($supplier_data)):
          $completion_rate = $row['total_orders'] > 0 ? ($row['completed'] / $row['total_orders'] * 100) : 0;
          $score_class = $completion_rate >= 80 ? 'score-high' : ($completion_rate >= 50 ? 'score-med' : 'score-low');
          $sup_count++;
    ?>
      <div class="supplier-item">
        <div>
          <div class="supplier-name"><?php echo $row['company_name']; ?></div>
          <div style="font-size:11px;color:rgba(255,255,255,0.3);margin-top:3px">
            <?php echo $row['total_orders']; ?> orders · <?php echo number_format($row['total_qty'],0); ?> units supplied
          </div>
        </div>
        <div style="display:flex;align-items:center;gap:15px">
          <div class="supplier-stats">
            <div class="val">Rs. <?php echo number_format($row['total_value'],0); ?></div>
            <div class="cnt">Total Value</div>
          </div>
          <span class="score-badge <?php echo $score_class; ?>">
            <?php echo round($completion_rate); ?>% Complete
          </span>
        </div>
      </div>
    <?php endwhile; endif; ?>
  </div>

  <div class="footer">&copy; <?php echo date('Y'); ?> RSKF Group of Companies Ltd.</div>
</div>

<script>
const labels = <?php echo json_encode($sales_labels); ?>;
const salesData  = <?php echo json_encode($sales_data); ?>;
const ordersData = <?php echo json_encode($orders_data); ?>;
const custData   = <?php echo json_encode($new_customers); ?>;

Chart.defaults.color = 'rgba(255,255,255,0.4)';
Chart.defaults.borderColor = 'rgba(255,255,255,0.06)';

// Revenue Chart
new Chart(document.getElementById('revenueChart'), {
    data: {
        labels: labels,
        datasets: [
            {
                type: 'bar',
                label: 'Revenue (Rs.)',
                data: salesData,
                backgroundColor: 'rgba(192,57,43,0.5)',
                borderColor: '#c0392b',
                borderWidth: 1,
                borderRadius: 6,
                yAxisID: 'y'
            },
            {
                type: 'line',
                label: 'Orders',
                data: ordersData,
                borderColor: '#f39c12',
                backgroundColor: 'rgba(243,156,18,0.1)',
                borderWidth: 2,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#f39c12',
                pointRadius: 4,
                yAxisID: 'y1'
            }
        ]
    },
    options: {
        responsive: true,
        interaction: { mode:'index', intersect:false },
        plugins: { legend:{ labels:{ color:'rgba(255,255,255,0.6)', boxWidth:12 } } },
        scales: {
            y:  { position:'left',  grid:{ color:'rgba(255,255,255,0.05)' }, ticks:{ callback: v => 'Rs.'+v.toLocaleString() } },
            y1: { position:'right', grid:{ drawOnChartArea:false }, ticks:{ color:'#f39c12' } },
            x:  { grid:{ color:'rgba(255,255,255,0.05)' } }
        }
    }
});

// Payment Doughnut
new Chart(document.getElementById('paymentChart'), {
    type: 'doughnut',
    data: {
        labels: ['Paid','Pending','Partial','Delivered','Cancelled'],
        datasets: [{
            data: [<?php echo "$paid,$pending,$partial,$delivered,$cancelled"; ?>],
            backgroundColor: ['rgba(39,174,96,0.8)','rgba(243,156,18,0.8)','rgba(52,152,219,0.8)','rgba(26,188,156,0.8)','rgba(255,255,255,0.1)'],
            borderColor: ['#27ae60','#f39c12','#2980b9','#1abc9c','rgba(255,255,255,0.2)'],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        cutout: '65%',
        plugins: {
            legend: { position:'bottom', labels:{ color:'rgba(255,255,255,0.6)', boxWidth:12, padding:12 } }
        }
    }
});

// Customers Chart
new Chart(document.getElementById('customersChart'), {
    type: 'line',
    data: {
        labels: labels,
        datasets: [{
            label: 'New Customers',
            data: custData,
            borderColor: '#9b59b6',
            backgroundColor: 'rgba(155,89,182,0.15)',
            borderWidth: 2,
            tension: 0.4,
            fill: true,
            pointBackgroundColor: '#9b59b6',
            pointRadius: 5
        }]
    },
    options: {
        responsive: true,
        plugins: { legend:{ labels:{ color:'rgba(255,255,255,0.6)', boxWidth:12 } } },
        scales: {
            y: { grid:{ color:'rgba(255,255,255,0.05)' }, ticks:{ stepSize:1 } },
            x: { grid:{ color:'rgba(255,255,255,0.05)' } }
        }
    }
});
</script>
</body>
</html>
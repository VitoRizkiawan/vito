<?php
session_start();
require_once "config/database.php";

if(!isset($_SESSION['admin_login'])){
    header("Location: login.php");
    exit;
}

/* ================= DATA ================= */

/* STOCK */
$stock = mysqli_query($conn,"SELECT * FROM products ORDER BY id DESC");

/* SALES */
$sales = mysqli_query($conn,"
SELECT 
    orders.id,
    users.username AS customer_name,
    orders.payment_method AS category,
    orders.total_price AS price,
    orders.order_date AS created_at
FROM orders
JOIN users ON orders.user_id = users.id
ORDER BY orders.id DESC
");

/* TRANSACTION */
$transaction = mysqli_query($conn,"
SELECT 
    orders.id,
    users.username AS customer_name,
    orders.payment_method AS category,
    orders.total_price AS price,
    orders.order_date AS created_at,
    orders.status
FROM orders
JOIN users ON orders.user_id = users.id
ORDER BY orders.id DESC
");

/* ================= TOTAL (FIXED) ================= */
$summary = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT 
    COALESCE(SUM(CASE WHEN LOWER(status)='success' THEN total_price END),0) as success_total,
    COALESCE(SUM(CASE WHEN LOWER(status)='pending' THEN total_price END),0) as pending_total,
    COALESCE(SUM(total_price),0) as all_total
FROM orders
"));
?>

<!DOCTYPE html> 
<html>
<head>
<title>Generate Report</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

.main-content{
    margin-left:240px;
    padding:30px;
}

.nav-tabs .nav-link{
    color:#007F77;
    font-weight:500;
}
.nav-tabs .nav-link.active{
    background:#007F77;
    color:white;
}

.table-header{
    background:#007F77 !important;
    color:white !important;
}

.card-box{
    background:white;
    padding:20px;
    border-radius:10px;
    box-shadow:0 4px 10px rgba(0,0,0,0.1);
}

.summary-box{
    background:#007F77;
    color:white;
    padding:15px;
    border-radius:10px;
    margin-bottom:15px;
    font-weight:bold;
    line-height:1.6;
}

</style>

</head>

<body>

<?php include 'includes/sidebar.php'; ?>

<div class="main-content">

<h3 class="mb-4">Generate Report</h3>

<div class="card-box">

<!-- TAB -->
<ul class="nav nav-tabs mb-3">
<li class="nav-item">
<button class="nav-link active" data-bs-toggle="tab" data-bs-target="#stock">Stock</button>
</li>

<li class="nav-item">
<button class="nav-link" data-bs-toggle="tab" data-bs-target="#sales">Sales</button>
</li>

<li class="nav-item">
<button class="nav-link" data-bs-toggle="tab" data-bs-target="#transaction">Transaction</button>
</li>
</ul>

<div class="tab-content">

<!-- ================= STOCK ================= -->
<div class="tab-pane fade show active" id="stock">

<table class="table table-bordered">
<thead class="table-header">
<tr>
<th>ID</th>
<th>Name</th>
<th>Category</th>
<th>Stock</th>
<th>Price</th>
</tr>
</thead>

<tbody>
<?php while($row = mysqli_fetch_assoc($stock)): ?>
<tr>
<td><?= $row['id'] ?></td>
<td><?= $row['name'] ?></td>
<td><?= $row['category'] ?></td>
<td><?= $row['stock'] ?></td>
<td>Rp <?= number_format($row['price'],0,',','.') ?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>

</div>

<!-- ================= SALES ================= -->
<div class="tab-pane fade" id="sales">

<table class="table table-bordered">
<thead class="table-header">
<tr>
<th>ID</th>
<th>Customer</th>
<th>Payment</th>
<th>Price</th>
<th>Date</th>
</tr>
</thead>

<tbody>
<?php while($row = mysqli_fetch_assoc($sales)): ?>
<tr>
<td><?= $row['id'] ?></td>
<td><?= $row['customer_name'] ?></td>
<td><?= $row['category'] ?></td>
<td>Rp <?= number_format($row['price'],0,',','.') ?></td>
<td><?= date("d-m-Y", strtotime($row['created_at'])) ?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>

</div>

<!-- ================= TRANSACTION ================= -->
<div class="tab-pane fade" id="transaction">

<!-- 🔥 SUMMARY -->
<div class="summary-box">
Total Success: Rp <?= number_format($summary['success_total'],0,',','.') ?><br>
Total Pending: Rp <?= number_format($summary['pending_total'],0,',','.') ?><br>
Total Semua: Rp <?= number_format($summary['all_total'],0,',','.') ?>
</div>

<table class="table table-bordered">
<thead class="table-header">
<tr>
<th>ID</th>
<th>Customer</th>
<th>Payment</th>
<th>Total</th>
<th>Status</th>
<th>Date</th>
</tr>
</thead>

<tbody>
<?php while($row = mysqli_fetch_assoc($transaction)): ?>
<tr>
<td><?= $row['id'] ?></td>
<td><?= $row['customer_name'] ?></td>
<td><?= $row['category'] ?></td>
<td>Rp <?= number_format($row['price'],0,',','.') ?></td>

<td>
<span class="badge bg-<?=
strtolower($row['status'])=='pending' ? 'warning' :
(strtolower($row['status'])=='success' ? 'success' : 'danger') ?>">
<?= $row['status'] ?>
</span>
</td>

<td><?= date("d-m-Y", strtotime($row['created_at'])) ?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>

</div>

</div>
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
<?php
session_start();
require_once "../admin/config/database.php";

if(!isset($_SESSION['petugas_login'])){
    header("Location: login.php");
    exit;
}

/* ================= HANDLE AJAX ================= */
if(isset($_GET['getData'])){

    $data = [];

    // TOTAL USERS
    $user = mysqli_query($conn, "SELECT COUNT(*) as total FROM users");
    $data['users'] = mysqli_fetch_assoc($user)['total'] ?? 0;

    // TOTAL PRODUCTS
    $product = mysqli_query($conn, "SELECT COUNT(*) as total FROM products");
    $data['products'] = mysqli_fetch_assoc($product)['total'] ?? 0;

    // TOTAL TRANSACTIONS
    $trx = mysqli_query($conn, "SELECT COUNT(*) as total FROM orders");
    $data['transactions'] = mysqli_fetch_assoc($trx)['total'] ?? 0;

    // ✅ TOTAL SUCCESS (DARI TABEL SUCCESS)
    $success = mysqli_query($conn, "SELECT COUNT(*) as total FROM success");
    $data['success'] = mysqli_fetch_assoc($success)['total'] ?? 0;

    // TOTAL PENDING
    $pending = mysqli_query($conn, "
        SELECT COUNT(*) as total 
        FROM orders 
        WHERE LOWER(status)='pending'
    ");
    $data['pending'] = mysqli_fetch_assoc($pending)['total'] ?? 0;

    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Dashboard Petugas</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<style>
body{ margin:0; background:#cfcfcf; }

.main-content{
    margin-left:240px;
    padding:40px;
}

.card-box{
    width:220px;
    border-radius:15px;
    text-align:center;
}

.card-title{
    font-size:14px;
    color:#555;
}

.card-value{
    font-size:28px;
    font-weight:bold;
}

.success{ color:green; }
.pending{ color:orange; }
</style>

</head>

<body>

<?php include 'includes/sidebar.php'; ?>

<div class="main-content">

<h4>Dashboard</h4>
<br>

<div class="d-flex gap-4 flex-wrap">

<div class="card p-4 shadow card-box bg-white">
    <p class="card-title">Total Users</p>
    <div class="card-value" id="totalUsers">0</div>
</div>

<div class="card p-4 shadow card-box bg-white">
    <p class="card-title">Total Products</p>
    <div class="card-value" id="totalProducts">0</div>
</div>

<div class="card p-4 shadow card-box bg-white">
    <p class="card-title">Total Transactions</p>
    <div class="card-value" id="totalTransactions">0</div>
</div>

<div class="card p-4 shadow card-box bg-white">
    <p class="card-title">Success</p>
    <div class="card-value success" id="totalSuccess">0</div>
</div>

<div class="card p-4 shadow card-box bg-white">
    <p class="card-title">Pending</p>
    <div class="card-value pending" id="totalPending">0</div>
</div>

</div>

</div>

<script>
function loadDashboardData(){
    $.ajax({
        url: "dashboard.php?getData=true",
        method: "GET",
        dataType: "json",
        success: function(data){
            $("#totalUsers").text(data.users);
            $("#totalProducts").text(data.products);
            $("#totalTransactions").text(data.transactions);
            $("#totalSuccess").text(data.success);
            $("#totalPending").text(data.pending);
        }
    });
}

loadDashboardData();
setInterval(loadDashboardData, 5000);
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
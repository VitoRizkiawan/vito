<?php
/* ================= DETEKSI HALAMAN ================= */
$currentPath = $_SERVER['PHP_SELF'];

/* ================= BASE URL ================= */
$base_url = "/vito/admin/"; // SESUAIKAN DENGAN FOLDER PROJECT KAMU
?>

<style>

/* SIDEBAR */
.sidebar{
    width:260px;
    height:100vh;
    position:fixed;
    left:0;
    top:0;
    background:#0f7f73;
    color:white;
    padding:30px 0;
}

/* TITLE */
.sidebar h3{
    font-weight:700;
    margin-bottom:40px;
    padding-left:30px;
}

/* MENU */
.sidebar a{
    display:block;
    color:white;
    text-decoration:none;
    padding:14px 30px;
    font-size:15px;
    transition:0.2s;
    margin:0; /* BIAR JARAK SAMA */
}

.sidebar a:hover{
    background:#02BDB1;
}

/* ACTIVE MENU */
.sidebar a.active{
    background:#e5e5e5;
    color:black;
    font-weight:600;
}

/* LOGOUT */
.logout-wrapper{
    position:absolute;
    bottom:30px;
    width:100%;
    padding:0 30px;
}

.logout-btn{
    width:100%;
    background:#ff3b3b;
    border:none;
    padding:12px;
    border-radius:10px;
    color:white;
    font-weight:600;
    transition:0.2s;
}

.logout-btn:hover{
    background:#e60000;
}

/* CONTENT SHIFT */
.main-content{
    margin-left:260px;
    padding:30px;
    background:#f0f0f0;
    min-height:100vh;
}

</style>

<div class="sidebar">

<h3>Dashboard<br>Admin</h3>

<a href="<?= $base_url ?>dashboard.php" 
class="<?= strpos($currentPath, 'dashboard.php') !== false ? 'active' : '' ?>">
Home
</a>

<a href="<?= $base_url ?>users.php" 
class="<?= strpos($currentPath, 'users.php') !== false ? 'active' : '' ?>">
User Management
</a>

<a href="<?= $base_url ?>products.php" 
class="<?= strpos($currentPath, 'products.php') !== false ? 'active' : '' ?>">
Product Management
</a>

<a href="<?= $base_url ?>report.php" 
class="<?= strpos($currentPath, 'report.php') !== false ? 'active' : '' ?>">
Generate Report
</a>

<a href="<?= $base_url ?>transactions/transactions.php" 
class="<?= strpos($currentPath, 'transactions') !== false ? 'active' : '' ?>">
Transaction Management
</a>

<a href="<?= $base_url ?>backup.php" 
class="<?= strpos($currentPath, 'backup.php') !== false ? 'active' : '' ?>">
Data Backup / Restore
</a>

<div class="logout-wrapper">
<a href="<?= $base_url ?>logout.php" class="logout-btn d-block text-center text-decoration-none">
    Logout
</a>
</div>

</div>
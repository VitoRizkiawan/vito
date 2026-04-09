<?php
 $currentPage = basename($_SERVER['PHP_SELF']);
?>

<style>

/* SIDEBAR */
.sidebar{
    width:260px;
    height:100vh;
    position:fixed;
    left:0;
    top:0;
    background:#00504B;
    color:white;
    padding:30px 0; /* kiri kanan dihilangkan supaya rata */
}

/* TITLE */
.sidebar h3{
    font-weight:700;
    margin-bottom:40px;
    padding-left:30px; /* samakan dengan menu */
}

/* MENU */
.sidebar a{
    display:block;
    color:white;
    text-decoration:none;
    padding:14px 30px; /* ini yang bikin rata semua */
    font-size:15px;
    transition:0.2s;
}

.sidebar a:hover{
    background:#02BDB1;
}

/* ACTIVE MENU */
.sidebar a.active{
    background:white;
    color:black;
    font-weight:600;
}

/* LOGOUT BUTTON */
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

<h3>Dashboard<br>Petugas</h3>

<a href="dashboard.php" class="<?= $currentPage == 'dashboard.php' ? 'active' : '' ?>">
Home
</a>

<!-- Menu Petugas -->
<a href="products.php" class="<?= $currentPage == 'products.php' ? 'active' : '' ?>">
Product Management
</a>

<a href="transactions.php" class="<?= $currentPage == 'transactions.php' ? 'active' : '' ?>">
Transaction Management
</a>

<a href="report.php" class="<?= $currentPage == 'report.php' ? 'active' : '' ?>">
Generate Report
</a>
<!-- End Menu Petugas -->

<div class="logout-wrapper">
<a href="logout.php" class="logout-btn d-block text-center text-decoration-none">
    Logout
</a>
</div>

</div>
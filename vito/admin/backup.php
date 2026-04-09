<?php
session_start();
require_once "config/database.php";

if(!isset($_SESSION['admin_login'])){
    header("Location: login.php");
    exit;
}

$backupDir = "../backups/";

// buat folder backup jika belum ada
if(!file_exists($backupDir)){
    mkdir($backupDir,0777,true);
}

/* ================= BACKUP DATABASE ================= */
if(isset($_POST['backupNow'])){

    $tables = array();
    $result = mysqli_query($conn,"SHOW TABLES");

    while($row = mysqli_fetch_row($result)){
        $tables[] = $row[0];
    }

    $sqlScript = "";

    foreach($tables as $table){

        // structure table
        $result = mysqli_query($conn,"SHOW CREATE TABLE $table");
        $row = mysqli_fetch_row($result);
        $sqlScript .= "\n\n".$row[1].";\n\n";

        // data table
        $result = mysqli_query($conn,"SELECT * FROM $table");

        while($row = mysqli_fetch_assoc($result)){

            $columns = array_keys($row);
            $values  = array_values($row);

            $values = array_map(function($v){
                return "'".addslashes($v)."'";
            },$values);

            $sqlScript .= "INSERT INTO $table (".implode(",",$columns).") VALUES (".implode(",",$values).");\n";
        }
    }

    $fileName = "backup_".date("Ymd_His").".sql";
    file_put_contents($backupDir.$fileName,$sqlScript);

    header("Location: backup.php");
}

/* ================= DELETE BACKUP ================= */
if(isset($_GET['delete'])){
    unlink($backupDir.$_GET['delete']);
    header("Location: backup.php");
}

/* ================= RESTORE DATABASE ================= */
if(isset($_POST['restoreNow'])){

    if($_FILES['file']['name']){

        $file = $_FILES['file']['tmp_name'];
        $sql = file_get_contents($file);

        mysqli_multi_query($conn,$sql);

        header("Location: backup.php");
    }
}

/* ================= AMBIL FILE BACKUP ================= */
$files = glob($backupDir."*.sql");
?>

<!DOCTYPE html>
<html>
<head>
<title>Data Backup / Restore</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
margin:0;
background:#cfcfcf;
}

/* SIDEBAR */
.sidebar{
width:240px;
height:100vh;
background:#0e7c73;
position:fixed;
color:white;
padding:30px 20px;
}

.sidebar h3{
margin-bottom:40px;
}

.sidebar a{
display:block;
color:white;
text-decoration:none;
}

.logout-btn{
margin-top:40px;
background:#ff3b3b;
border:none;
padding:12px 30px;
border-radius:10px;
color:white;
}

/* CONTENT */
.main-content{
margin-left:240px;
padding:40px;
}

/* TABLE */
.table-header{
background:#007F77;
color:white;
}

.table-header th{
background:#007F77 !important;
color:white;
}

.btn-main{
background:#007F77;
color:white;
border:none;
}

.btn-main:hover{
background:#00665f;
}

</style>
</head>

<body>

<?php include 'includes/sidebar.php'; ?>

<!-- CONTENT -->
<div class="main-content">

<h4>Backup</h4>

<form method="POST">
<button name="backupNow" class="btn btn-main mb-3">
Backup Now
</button>
</form>

<table class="table table-bordered bg-white">
<thead class="table-header">
<tr>
<th>Backup File</th>
<th>Date</th>
<th>Size</th>
<th>Actions</th>
</tr>
</thead>

<tbody>

<?php foreach($files as $file): ?>
<tr>

<td><?= basename($file) ?></td>

<td>
<?= date("d-m-Y H:i", filemtime($file)) ?>
</td>

<td>
<?= round(filesize($file)/1024,2) ?> KB
</td>

<td>
<a href="../backups/<?= basename($file) ?>" class="btn btn-success btn-sm" download>
Download
</a>

<a href="?delete=<?= basename($file) ?>" class="btn btn-danger btn-sm">
Delete
</a>
</td>

</tr>
<?php endforeach; ?>

</tbody>
</table>

<hr class="my-5">

<h4>Restore</h4>

<form method="POST" enctype="multipart/form-data">

<div class="row">
<div class="col-md-4">
<input type="file" name="file" class="form-control" required>
</div>

<div class="col-md-3">
<button name="restoreNow" class="btn btn-main">
Restore Now
</button>
</div>
</div>

</form>

</div>

</body>
</html>
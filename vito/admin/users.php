<?php
session_start();
require_once "config/database.php";

if(!isset($_SESSION['admin_login'])){
    header("Location: login.php");
    exit;
}

/* ================= TAMBAH USER ================= */
if(isset($_POST['addUser'])){
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // 🔥 PERUBAHAN: Hash password
    $role = $_POST['role'];

    // 🔥 PERUBAHAN: Prepared statement untuk keamanan
    $stmt = mysqli_prepare($conn, "INSERT INTO users (username,email,password,role) VALUES (?,?,?,?)");
    mysqli_stmt_bind_param($stmt, "ssss", $username, $email, $password, $role);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

/* ================= UPDATE USER ================= */
if(isset($_POST['updateUser'])){
    $id = $_POST['id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    
    // 🔥 PERUBAHAN: Handle password (hanya update jika diisi)
    if(!empty($_POST['password'])){
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = mysqli_prepare($conn, "UPDATE users SET username=?, email=?, password=?, role=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "ssssi", $username, $email, $password, $role, $id);
    } else {
        $stmt = mysqli_prepare($conn, "UPDATE users SET username=?, email=?, role=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "sssi", $username, $email, $role, $id);
    }
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

/* ================= DELETE USER ================= */
if(isset($_POST['deleteUser'])){
    $id = $_POST['id'];
    // 🔥 PERUBAHAN: Prepared statement untuk DELETE
    $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

/* ================= AMBIL DATA ================= */
// 🔥 PERUBAHAN: Urutkan ASC agar nomor urut lebih natural (opsional)
$users = mysqli_query($conn, "SELECT * FROM users ORDER BY id ASC");
?>

<!DOCTYPE html>
<html>
<head>
<title>User Management</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
.table-header{
    background: #007F77 !important;
    color: white !important;
    justify-content: center;
    align-items: center;
}

.table-header th{
    background: #007F77 !important;
    color: white !important;
    justify-content: center;
    align-items: center;
}

/* Button warna custom */
.btn-custom{
    background-color: #007F77 !important;
    color: white !important;
    border: none !important;
}

.btn-custom:hover{
    background-color: #00665f !important;
}

/* container action agar center */
.action-box{
    display:flex;
    justify-content:center;
    align-items:center;
    gap:12px;
}

/* EDIT BUTTON */
.btn-edit{
    background-color:#199276 !important;
    color:white !important;
    border:none !important;
    width:45px;
    height:45px;
    border-radius:10px;
    display:flex;
    justify-content:center;
    align-items:center;
}

.btn-edit:hover{
    background-color:#157a62 !important;
}

/* DELETE BUTTON */
.btn-delete{
    background-color:#E54B4B !important;
    color:white !important;
    border:none !important;
    width:45px;
    height:45px;
    border-radius:10px;
    display:flex;
    justify-content:center;
    align-items:center;
}

.btn-delete:hover{
    background-color:#c93d3d !important;
}

/* ukuran icon lebih besar */
.action-box i{
    font-size:18px;
}
</style>

</head>
<body>

<?php include 'includes/sidebar.php'; ?>

<div class="main-content">

<h3>Users Management</h3>

<div class="mb-3">
<button class="btn btn-custom" data-bs-toggle="modal" data-bs-target="#addModal">
Add Officer
</button>
</div>

<table class="table table-bordered" border="1">
<thead class="table-header">
<tr>
<!-- 🔥 PERUBAHAN: Ganti "ID" jadi "No" untuk nomor urut visual -->
<th>ID</th>
<th>Username</th>
<th>Email</th>
<th>Password</th>
<th>Role</th>
<th>Date</th>
<th>Actions</th>
</tr>
</thead>

<tbody>
<?php 
// 🔥 PERUBAHAN: Inisialisasi counter nomor urut
$no = 1; 
while($row = mysqli_fetch_assoc($users)): 
?>
<tr>
<!-- 🔥 PERUBAHAN: Tampilkan nomor urut, bukan ID database -->
<td><?= $no++ ?></td>
<td><?= htmlspecialchars($row['username']) ?></td>
<td><?= htmlspecialchars($row['email']) ?></td>
<td>
    <!-- 🔥 PERUBAHAN: Sembunyikan password dengan asterisk (lebih aman) -->
    <?php 
    $pwd = $row['password'];
    echo (strlen($pwd) >= 60 && strpos($pwd, '$') === 0) 
        ? '••••••••' // Jika sudah di-hash, tampilkan dot
        : htmlspecialchars($pwd); // Jika masih plain, tampilkan (untuk migrasi)
    ?>
</td>
<td><?= ucfirst($row['role']) ?></td>
<td><?= date('d-m-Y', strtotime($row['created_at'])) ?></td>
<td>
    <div class="action-box">
        <!-- EDIT: 🔥 PENTING: Modal target tetap pakai $row['id'] asli -->
        <button class="btn btn-edit"
            data-bs-toggle="modal"
            data-bs-target="#editModal<?= $row['id'] ?>">
            <i class="bi bi-pencil-square"></i>
        </button>

        <!-- DELETE: 🔥 PENTING: Modal target tetap pakai $row['id'] asli -->
        <button class="btn btn-delete"
            data-bs-toggle="modal"
            data-bs-target="#deleteModal<?= $row['id'] ?>">
            <i class="bi bi-trash"></i>
        </button>
    </div>
</td>
</tr>

<!-- ================= EDIT MODAL ================= -->
<!-- 🔥 PENTING: ID modal tetap pakai $row['id'] asli untuk unik -->
<div class="modal fade" id="editModal<?= $row['id'] ?>">
<div class="modal-dialog">
<div class="modal-content">
<form method="POST">
<div class="modal-header">
<h5>Edit User</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
<!-- 🔥 PENTING: Hidden input tetap kirim ID asli ke backend -->
<input type="hidden" name="id" value="<?= $row['id'] ?>">

<label>Username</label>
<input type="text" name="username" class="form-control mb-3"
value="<?= htmlspecialchars($row['username']) ?>" required>

<label>Email</label>
<input type="email" name="email" class="form-control mb-3"
value="<?= htmlspecialchars($row['email']) ?>" required>

<label>Password</label>
<small class="text-muted d-block mb-1">Kosongkan jika tidak ingin mengubah</small>
<input type="password" name="password" class="form-control mb-3"
placeholder="••••••••" autocomplete="new-password">

<label>Role</label>
<select name="role" class="form-control mb-3" required>
<option value="admin" <?= $row['role']=='admin'?'selected':'' ?>>Admin</option>
<option value="petugas" <?= $row['role']=='petugas'?'selected':'' ?>>Petugas</option>
</select>
</div>
<div class="modal-footer">
<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
<button name="updateUser" class="btn btn-primary">Update</button>
</div>
</form>
</div>
</div>
</div>

<!-- ================= DELETE MODAL ================= -->
<!-- 🔥 PENTING: ID modal tetap pakai $row['id'] asli untuk unik -->
<div class="modal fade" id="deleteModal<?= $row['id'] ?>">
<div class="modal-dialog">
<div class="modal-content">
<form method="POST">
<div class="modal-header">
<h5>Hapus User?</h5>
</div>
<div class="modal-body">
<!-- 🔥 PENTING: Hidden input tetap kirim ID asli ke backend -->
<input type="hidden" name="id" value="<?= $row['id'] ?>">
Apakah yakin ingin menghapus user <strong><?= htmlspecialchars($row['username']) ?></strong>?
</div>
<div class="modal-footer">
<button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
<button name="deleteUser" class="btn btn-danger">Hapus</button>
</div>
</form>
</div>
</div>
</div>

<?php endwhile; ?>
</tbody>
</table>

</div>

<!-- ================= ADD MODAL ================= -->
<div class="modal fade" id="addModal">
<div class="modal-dialog">
<div class="modal-content">
<form method="POST">
<div class="modal-header">
<h5>Add Officer</h5>
</div>
<div class="modal-body">
<input type="text" name="username" class="form-control mb-3"
placeholder="Username" required>

<input type="email" name="email" class="form-control mb-3"
placeholder="Email" required>

<input type="password" name="password" class="form-control"
placeholder="Password" required>

<select name="role" class="form-control mt-3" required>
<option value="">-- Pilih Role --</option>
<option value="admin">Admin</option>
<option value="petugas">Petugas</option>
</select>
</div>
<div class="modal-footer">
<button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
<button name="addUser" class="btn btn-success">Save</button>
</div>
</form>
</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
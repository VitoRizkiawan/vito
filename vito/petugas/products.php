<?php
session_start();
require_once "../admin/config/database.php";

if(!isset($_SESSION['petugas_login'])){
    header("Location: login.php");
    exit;
}

include 'includes/sidebar.php';

/* ================= TAMBAH PRODUCT ================= */
if(isset($_POST['addProduct'])){
    $name = $_POST['name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $description = $_POST['description'];

    $image = $_FILES['image']['name'];
    $tmp = $_FILES['image']['tmp_name'];
    $ext = pathinfo($image, PATHINFO_EXTENSION);
    $newImageName = uniqid() . '.' . $ext;

    $uploadDir = "../admin/uploads/";

    if(!empty($image) && in_array(strtolower($ext), ['jpg','jpeg','png','gif'])){
        move_uploaded_file($tmp, $uploadDir.$newImageName);
    } else {
        $newImageName = 'default.png';
    }

    $stmt = mysqli_prepare($conn, 
    "INSERT INTO products(name,category,price,stock,description,image) 
     VALUES(?,?,?,?,?,?)");

    mysqli_stmt_bind_param($stmt, "ssisss", 
        $name, $category, $price, $stock, $description, $newImageName
    );

    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

/* ================= UPDATE PRODUCT ================= */
if(isset($_POST['updateProduct'])){
    $id = $_POST['id'];
    $name = $_POST['name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $description = $_POST['description'];

    $uploadDir = "../admin/uploads/";

    if($_FILES['image']['name']){
        $image = $_FILES['image']['name'];
        $tmp = $_FILES['image']['tmp_name'];
        $ext = pathinfo($image, PATHINFO_EXTENSION);
        $newImageName = uniqid() . '.' . $ext;

        if(in_array(strtolower($ext), ['jpg','jpeg','png','gif'])){
            move_uploaded_file($tmp, $uploadDir.$newImageName);

            $old = mysqli_fetch_assoc(mysqli_query($conn, "SELECT image FROM products WHERE id=$id"));
            if($old['image'] != 'default.png' && file_exists($uploadDir.$old['image'])){
                unlink($uploadDir.$old['image']);
            }

            $stmt = mysqli_prepare($conn, 
            "UPDATE products SET name=?, category=?, price=?, stock=?, description=?, image=? WHERE id=?");

            mysqli_stmt_bind_param($stmt, "ssisssi", 
                $name, $category, $price, $stock, $description, $newImageName, $id
            );
        } else {
            $stmt = mysqli_prepare($conn, 
            "UPDATE products SET name=?, category=?, price=?, stock=?, description=? WHERE id=?");

            mysqli_stmt_bind_param($stmt, "ssissi", 
                $name, $category, $price, $stock, $description, $id
            );
        }
    } else {
        $stmt = mysqli_prepare($conn, 
        "UPDATE products SET name=?, category=?, price=?, stock=?, description=? WHERE id=?");

        mysqli_stmt_bind_param($stmt, "ssissi", 
            $name, $category, $price, $stock, $description, $id
        );
    }

    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

/* ================= DELETE PRODUCT ================= */
if(isset($_POST['deleteProduct'])){
    $id = $_POST['id'];

    $uploadDir = "../admin/uploads/";

    $result = mysqli_query($conn, "SELECT image FROM products WHERE id=$id");
    $row = mysqli_fetch_assoc($result);
    if($row && $row['image'] != 'default.png' && file_exists($uploadDir.$row['image'])){
        unlink($uploadDir.$row['image']);
    }

    $stmt = mysqli_prepare($conn, "DELETE FROM products WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    echo "<script>
    document.addEventListener('DOMContentLoaded', function(){
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Produk berhasil dihapus',
            timer: 2000,
            showConfirmButton: false
        });
    });
    </script>";
}

$products = mysqli_query($conn, "SELECT * FROM products ORDER BY id ASC");
?>

<!DOCTYPE html>
<html>
<head>
<title>Product Management</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
.table-header{
    background:#00504B;
    color:white !important;
}
.table-header th{
    background:#00504B;
    color:white !important;
}
.btn-custom{
    background:#00504B !important;
    color:white !important;
    border:none !important;
}
.btn-custom:hover{
    background:#00665f !important;
}
.action-box{
    display:flex;
    justify-content:center;
    align-items:center;
    gap:12px;
}
.btn-edit{
    background:#199276 !important;
    color:white !important;
    width:45px;
    height:45px;
    border-radius:10px;
    display:flex;
    justify-content:center;
    align-items:center;
}
.btn-delete{
    background:#E54B4B !important;
    color:white !important;
    width:45px;
    height:45px;
    border-radius:10px;
    display:flex;
    justify-content:center;
    align-items:center;
}
</style>
</head>

<body>

<div class="main-content">

<h3>Product Management</h3>

<button class="btn btn-custom mb-3" data-bs-toggle="modal" data-bs-target="#addModal">
Add Product
</button>

<table class="table table-bordered">
<thead class="table-header">
<tr>
<th>No</th>
<th>Name</th>
<th>Category</th>
<th>Price</th>
<th>Stock</th>
<th>Description</th>
<th>Picture</th>
<th>Actions</th>
</tr>
</thead>

<tbody>
<?php $no=1; while($row=mysqli_fetch_assoc($products)): ?>
<tr>
<td><?= $no++ ?></td>
<td><?= htmlspecialchars($row['name']) ?></td>
<td><?= htmlspecialchars($row['category']) ?></td>
<td>Rp <?= number_format($row['price']) ?></td>
<td><?= $row['stock'] ?></td>
<td><?= htmlspecialchars(substr($row['description'],0,50)) ?>...</td>
<td>
<img src="../admin/uploads/<?= htmlspecialchars($row['image']) ?>" width="60" onerror="this.src='../admin/uploads/default.png'">
</td>
<td>
<div class="action-box">
<button class="btn btn-edit" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id'] ?>">
<i class="bi bi-pencil-square"></i>
</button>

<button class="btn btn-delete btn-delete-swal" 
    data-id="<?= $row['id'] ?>" 
    data-name="<?= htmlspecialchars($row['name']) ?>">
    <i class="bi bi-trash"></i>
</button>

</div>
</td>
</tr>

<!-- EDIT MODAL -->
<div class="modal fade" id="editModal<?= $row['id'] ?>">
<div class="modal-dialog">
<div class="modal-content">
<form method="POST" enctype="multipart/form-data">
<div class="modal-header">
<h5>Edit Product</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
<input type="hidden" name="id" value="<?= $row['id'] ?>">
<label>Name</label>
<input type="text" name="name" class="form-control mb-3" value="<?= htmlspecialchars($row['name']) ?>" required>
<label>Category</label>
<input type="text" name="category" class="form-control mb-3" value="<?= htmlspecialchars($row['category']) ?>" required>
<label>Price</label>
<input type="number" name="price" class="form-control mb-3" value="<?= $row['price'] ?>" required>
<label>Stock</label>
<input type="number" name="stock" class="form-control mb-3" value="<?= $row['stock'] ?>" required>
<label>Description</label>
<textarea name="description" class="form-control mb-3" rows="3" required><?= htmlspecialchars($row['description']) ?></textarea>
<label>Ganti Gambar</label>
<input type="file" name="image" class="form-control">
</div>
<div class="modal-footer">
<button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
<button name="updateProduct" class="btn btn-custom">Update</button>
</div>
</form>
</div>
</div>
</div>

<?php endwhile; ?>
</tbody>
</table>

</div>

<!-- ADD MODAL -->
<div class="modal fade" id="addModal">
<div class="modal-dialog">
<div class="modal-content">
<form method="POST" enctype="multipart/form-data">
<div class="modal-header">
<h5>Add Product</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
<input type="text" name="name" class="form-control mb-3" placeholder="Product Name" required>
<input type="text" name="category" class="form-control mb-3" placeholder="Category" required>
<input type="number" name="price" class="form-control mb-3" placeholder="Price" required>
<input type="number" name="stock" class="form-control mb-3" placeholder="Stock" required>
<label>Description</label>
<textarea name="description" class="form-control mb-3" rows="3" required></textarea>
<label>Upload Picture</label>
<input type="file" name="image" class="form-control" required>
</div>
<div class="modal-footer">
<button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
<button name="addProduct" class="btn btn-custom">Save</button>
</div>
</form>
</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.querySelectorAll('.btn-delete-swal').forEach(button => {
    button.addEventListener('click', function () {
        const id = this.getAttribute('data-id');
        const name = this.getAttribute('data-name');

        Swal.fire({
            title: 'Yakin hapus?',
            text: "Produk: " + name,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#E54B4B',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {

                const form = document.createElement('form');
                form.method = 'POST';

                const inputId = document.createElement('input');
                inputId.type = 'hidden';
                inputId.name = 'id';
                inputId.value = id;

                const inputDelete = document.createElement('input');
                inputDelete.type = 'hidden';
                inputDelete.name = 'deleteProduct';
                inputDelete.value = '1';

                form.appendChild(inputId);
                form.appendChild(inputDelete);

                document.body.appendChild(form);
                form.submit();
            }
        });
    });
});
</script>

</body>
</html>
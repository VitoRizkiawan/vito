<?php
session_start();
include "../admin/config/database.php";

$is_logged_in = isset($_SESSION['user_login']) && $_SESSION['role'] === 'user';

// ================= PROSES BUY LANGSUNG =================
if(isset($_GET['buy'])){
    if (!$is_logged_in) {
        header("Location: login.php");
        exit;
    }
    $buy_id = intval($_GET['buy']);

    // cek produk ada
    $check = mysqli_query($conn, "SELECT id, stock FROM products WHERE id=$buy_id");
    if(mysqli_num_rows($check) > 0){
        $prod = mysqli_fetch_assoc($check);

        // Cek stok sebelum buy
        if($prod['stock'] <= 0){
            echo "<script>alert('Stok produk habis!'); window.history.back();</script>";
            exit;
        }

        // buat session cart jika belum ada
        if(!isset($_SESSION['cart'])){
            $_SESSION['cart'] = [];
        }
        if(!isset($_SESSION['cart_qty'])){
            $_SESSION['cart_qty'] = [];
        }

        // 🔥 mode BUY NOW (langsung checkout 1 produk saja)
        $_SESSION['cart'] = [$buy_id];
        $_SESSION['cart_qty'][$buy_id] = 1;

        header("Location: checkout.php");
        exit;
    }
}

// ================= AMBIL DETAIL PRODUK =================
if(!isset($_GET['id'])){
    header("Location: index.php");
    exit;
}

$id = intval($_GET['id']);

$query = "SELECT * FROM products WHERE id = $id";
$result = mysqli_query($conn, $query);

if(mysqli_num_rows($result) == 0){
    echo "Produk tidak ditemukan";
    exit;
}

$product = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Detail Product</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
    background:#ffffff;
}
.navbar{
    background:#1fb5aa;
}
.navbar-brand{
    color:white !important;
    font-weight:600;
}
.navbar a{
    color:white !important;
    text-decoration:none;
}
.detail-card{
    background:#EAEFEF;
    padding:40px;
    border-radius:15px;
}
.product-img{
    width:100%;
    height:400px;
    object-fit:cover;
    border-radius:15px;
    background:#ccc;
}
.btn-custom{
    width:150px;
    height:45px;
    border-radius:8px;
    font-weight:600;
}
.btn-cart{
    background:#18a999;
    color:white;
    border:none;
}
.btn-cart:hover{
    background:#148f82;
    color:white;
}
.btn-buy{
    background:#18a999;
    color:white;
    border:none;
}
.btn-buy:hover{
    background:#148f82;
    color:white;
}
</style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar px-4 d-flex justify-content-between">
    <span class="navbar-brand">BuyZone</span>
    <a href="index.php">Back</a>
</nav>

<div class="container mt-5">

    <div class="detail-card">

        <div class="row align-items-center">

            <!-- GAMBAR -->
            <div class="col-md-5 mb-4 mb-md-0">
                <img src="../admin/uploads/<?= htmlspecialchars($product['image']); ?>" 
                     class="product-img"
                     onerror="this.src='https://via.placeholder.com/400x400?text=No+Image'">
            </div>

            <!-- DETAIL -->
            <div class="col-md-7">

                <h5 class="mb-3">Description</h5>
                <p>
                    <?= !empty($product['description']) 
                        ? htmlspecialchars($product['description']) 
                        : "Tidak ada deskripsi produk."; ?>
                </p>

                <div class="mt-4">
                    <p><strong>Title :</strong> <?= htmlspecialchars($product['name']); ?></p>
                    <p><strong>Category :</strong> <?= htmlspecialchars($product['category']); ?></p>
                    <p><strong>Price :</strong> 
                        Rp <?= number_format($product['price'], 0, ',', '.'); ?>
                    </p>
                    <p><strong>Stok :</strong> 
                        <?php if($product['stock'] > 0): ?>
                            <span class="text-success fw-bold"><?= $product['stock']; ?></span>
                        <?php else: ?>
                            <span class="text-danger fw-bold">Habis</span>
                        <?php endif; ?>
                    </p>
                </div>

                <?php if($product['stock'] > 0): ?>
                <div class="d-flex gap-3 mt-4">
                    
                    <!-- CART: Tambah ke cart dan redirect ke cart.php -->
                    <a href="<?= $is_logged_in ? 'cart.php?add='.$product['id'] : 'login.php' ?>" 
                       class="btn btn-custom btn-cart d-flex justify-content-center align-items-center">
                       Cart
                    </a>

                    <!-- BUY LANGSUNG: Langsung ke checkout -->
                    <a href="<?= $is_logged_in ? 'detail.php?id='.$product['id'].'&buy='.$product['id'] : 'login.php' ?>" 
                       class="btn btn-custom btn-buy d-flex justify-content-center align-items-center">
                       Buy
                    </a>

                </div>
                <?php else: ?>
                <div class="mt-4">
                    <div class="alert alert-danger d-flex align-items-center gap-2" style="border-radius:10px;">
                        <i class="bi bi-exclamation-triangle-fill" style="font-size:20px;"></i>
                        <span class="fw-bold">Stok habis — produk ini tidak dapat dibeli saat ini.</span>
                    </div>
                </div>
                <?php endif; ?>

            </div>

        </div>

    </div>

</div>

</body>
</html>
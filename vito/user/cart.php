<?php
session_start();
include "../admin/config/database.php";

if(!isset($_SESSION['user_login'])){
    header("Location: login.php");
    exit;
}

/* ================= INISIALISASI SESSION CART ================= */
if(!isset($_SESSION['cart'])){
    $_SESSION['cart'] = [];
}
if(!isset($_SESSION['cart_qty'])){
    $_SESSION['cart_qty'] = [];
}

/* ================= TAMBAH KE CART ================= */
if(isset($_GET['add'])){
    $add_id = intval($_GET['add']);
    
    $check = mysqli_query($conn, "SELECT * FROM products WHERE id = '$add_id'");
    if(mysqli_num_rows($check) > 0){
        $prod = mysqli_fetch_assoc($check);

        // Cek stok sebelum tambah ke cart
        if($prod['stock'] <= 0){
            header("Location: cart.php?msg=outofstock");
            exit;
        }

        if(!in_array($add_id, $_SESSION['cart'])){
            $_SESSION['cart'][] = $add_id;
            $_SESSION['cart_qty'][$add_id] = 1;
        }
    }
    header("Location: cart.php");
    exit;
}

/* ================= HAPUS DARI CART ================= */
if(isset($_GET['remove'])){
    $remove_id = intval($_GET['remove']);
    
    $key = array_search($remove_id, $_SESSION['cart']);
    if($key !== false){
        unset($_SESSION['cart'][$key]);
        unset($_SESSION['cart_qty'][$remove_id]);
        $_SESSION['cart'] = array_values($_SESSION['cart']);
    }
    
    header("Location: cart.php?msg=deleted");
    exit;
}

/* ================= UPDATE QUANTITY ================= */
if(isset($_POST['update_cart'])){
    foreach($_POST['qty'] as $id => $qty){
        $id = intval($id);
        $qty = intval($qty);
        
        if($qty > 0 && in_array($id, $_SESSION['cart'])){
            $_SESSION['cart_qty'][$id] = $qty;
        }
    }
    header("Location: cart.php");
    exit;
}

/* ================= AMBIL DATA CART ================= */
$products = [];
$total = 0;

if(!empty($_SESSION['cart'])){
    $ids = implode(",", array_map('intval', $_SESSION['cart']));
    $query = mysqli_query($conn, "SELECT * FROM products WHERE id IN ($ids)");
    while($row = mysqli_fetch_assoc($query)){
        $row['quantity'] = isset($_SESSION['cart_qty'][$row['id']]) ? $_SESSION['cart_qty'][$row['id']] : 1;
        $products[] = $row;
        $total += $row['price'] * $row['quantity'];
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cart - BuyZone</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{background:#ffffff;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;padding-bottom:30px;}
.navbar{background:#1fb5aa;padding:15px 20px;}
.navbar-brand,.navbar a{color:white !important;font-weight:600;text-decoration:none;font-size:18px;}
.tab-menu{display:flex;justify-content:center;gap:50px;margin:25px 0;}
.tab-menu a{text-decoration:none;color:#333;font-weight:500;font-size:16px;padding-bottom:5px;}
.tab-menu .active{color:#1fb5aa;border-bottom:3px solid #1fb5aa;font-weight:600;}
.cart-item{display:flex;align-items:center;gap:20px;padding:20px;margin:15px 20px;background:#EAEFEF;border-radius:12px;}
.cart-item img{width:80px;height:80px;object-fit:cover;border-radius:8px;background:#ddd;}
.cart-item-info{flex-grow:1;}
.cart-item-title{font-weight:600;font-size:16px;color:#333;margin-bottom:5px;}
.cart-item-price{color:#1fb5aa;font-weight:700;font-size:14px;}
.quantity-control{display:flex;align-items:center;gap:10px;margin-right:20px;}
.qty-btn{width:30px;height:30px;border:2px solid #1fb5aa;background:white;color:#1fb5aa;border-radius:6px;cursor:pointer;font-weight:600;display:flex;align-items:center;justify-content:center;transition:all 0.3s;}
.qty-btn:hover{background:#1fb5aa;color:white;}
.qty-input{width:50px;height:30px;border:2px solid #ddd;border-radius:6px;text-align:center;font-weight:600;}
.btn-hapus{background:#ff6b6b;color:white;border:none;padding:8px 25px;border-radius:8px;font-weight:600;cursor:pointer;transition:all 0.3s;text-decoration:none;display:inline-block;}
.btn-hapus:hover{background:#ff5252;color:white;}
.empty-cart{text-align:center;padding:60px 20px;background:white;border-radius:15px;margin:40px 20px;}
.empty-cart i{font-size:80px;color:#ddd;margin-bottom:20px;}

/* 🔥 TAMBAHAN SCROLL (TIDAK MENGUBAH STYLE LAMA) */
.cart-scroll{
    max-height: calc(100vh - 180px);
    overflow-y: auto;
}

/* 🔥 supaya tidak kepotong bawah */
.container{
    padding-bottom: 120px;
}

@media (max-width:768px){
.cart-item{flex-direction:column;text-align:center;}
.quantity-control{margin:15px 0;}
.btn-hapus{width:100%;}
}
</style>
</head>

<body>

<nav class="navbar d-flex justify-content-between">
<span class="navbar-brand">BuyZone</span>
<a href="index.php">Back</a>
</nav>

<div class="tab-menu">
<a href="cart.php" class="active">Cart</a>
<a href="checkout.php">Checkout</a>
<a href="history.php">History</a>
</div>

<div class="container">

<div class="cart-scroll"> <!-- 🔥 WRAPPER SCROLL -->

<?php if(empty($products)): ?>
<div class="empty-cart">
<i class="bi bi-cart-x"></i>
<h4 style="color:#666; margin-bottom:20px;">Empty shopping cart</h4>
<a href="index.php" class="btn btn-lg" style="background:#1fb5aa; color:white; border-radius:10px; padding:12px 40px;">
Start Shopping
</a>
</div>
<?php else: ?>

<form method="POST" action="cart.php">
<?php foreach($products as $p): ?>
<div class="cart-item">
<img src="../admin/uploads/<?= htmlspecialchars($p['image']); ?>">

<div class="cart-item-info">
<div class="cart-item-title"><?= htmlspecialchars($p['name']); ?></div>
<div class="cart-item-price">Rp <?= number_format($p['price'], 0, ',', '.'); ?></div>
</div>

<div class="quantity-control">
<button type="button" class="qty-btn" onclick="updateQty(<?= $p['id']; ?>, -1)">−</button>
<input type="number" name="qty[<?= $p['id']; ?>]" 
class="qty-input" 
id="qty-<?= $p['id']; ?>" 
value="<?= $p['quantity']; ?>" 
min="1" readonly>
<button type="button" class="qty-btn" onclick="updateQty(<?= $p['id']; ?>, 1)">+</button>
</div>

<a href="#" class="btn-hapus" onclick="confirmDelete(<?= $p['id']; ?>)">Hapus</a>
</div>
<?php endforeach; ?>
</form>

<?php endif; ?>

</div> <!-- 🔥 END SCROLL -->

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
function updateQty(id, change){
const input = document.getElementById('qty-' + id);
let newValue = parseInt(input.value) + change;

if(newValue < 1) newValue = 1;
if(newValue > 99) newValue = 99;

input.value = newValue;

const form = input.closest('form');
const formData = new FormData(form);
formData.append('update_cart', '1');

fetch('cart.php', {
method: 'POST',
body: formData
}).then(() => {
location.reload();
});
}

function confirmDelete(id){
Swal.fire({
title:'Yakin?',
text:'Item akan dihapus dari keranjang!',
icon:'warning',
showCancelButton:true,
confirmButtonColor:'#1fb5aa',
cancelButtonColor:'#d33',
confirmButtonText:'Ya, hapus!',
cancelButtonText:'Batal'
}).then((result)=>{
if(result.isConfirmed){
window.location.href="cart.php?remove="+id;
}
});
}

<?php if(isset($_GET['msg']) && $_GET['msg'] == 'outofstock'): ?>
Swal.fire({
icon:'error',
title:'Stok Habis!',
text:'Produk ini sudah habis dan tidak bisa ditambahkan ke keranjang.',
timer:2500,
showConfirmButton:false
});
<?php elseif(isset($_GET['msg'])): ?>
Swal.fire({
icon:'success',
title:'Berhasil!',
text:'Item berhasil dihapus',
timer:1500,
showConfirmButton:false
});
<?php endif; ?>
</script>

</body>
</html>
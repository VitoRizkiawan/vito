<?php
session_start();
include "../admin/config/database.php";

if(!isset($_SESSION['user_login'])){
    header("Location: login.php");
    exit;
}

/* ================= AMBIL DATA CART ================= */
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$cart_qty = isset($_SESSION['cart_qty']) ? $_SESSION['cart_qty'] : [];

$total = 0;
$products = [];

if(!empty($cart)){
    $ids = implode(",", array_map('intval', $cart));
    $query = mysqli_query($conn, "SELECT * FROM products WHERE id IN ($ids)");
    while($row = mysqli_fetch_assoc($query)){
        $qty = isset($cart_qty[$row['id']]) ? $cart_qty[$row['id']] : 1;
        $row['qty'] = $qty;

        $products[] = $row;
        $total += $row['price'] * $qty;
    }
}

/* ================= AMBIL ALAMAT DEFAULT DARI PROFILE ================= */
$default_name = '';
$default_phone = '';
$default_address = '';
if(isset($_SESSION['user_id'])){
    $uid = $_SESSION['user_id'];
    $user_q = mysqli_query($conn, "
        SELECT u.name, p.full_name, p.phone as p_phone, p.address as p_address 
        FROM users u 
        LEFT JOIN user_profiles p ON u.id = p.user_id 
        WHERE u.id='$uid'
    ");
    if($user_q && $u = mysqli_fetch_assoc($user_q)){
        $default_name = !empty($u['full_name']) ? $u['full_name'] : ($u['name'] ?? '');
        $default_phone = $u['p_phone'] ?? '';
        $default_address = $u['p_address'] ?? '';
    }
}

/* ================= PROSES ORDER ================= */
if(isset($_POST['orderNow'])){

if(!isset($_SESSION['user_id'])){
        echo "<script>alert('Session login tidak valid, silakan login ulang'); window.location='login.php';</script>";
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $payment = $_POST['payment'];

    // Data penerima & Shipping
    $name = mysqli_real_escape_string($conn, $_POST['name'] ?? '');
    $phone = mysqli_real_escape_string($conn, $_POST['phone'] ?? '');

    $shipping_expedition = mysqli_real_escape_string($conn, $_POST['shipping_expedition'] ?? '');
    $shipping_type = 'Reguler'; // Fallback
    $shipping_cost = intval($_POST['shipping_cost'] ?? 0);

    $total_price = isset($_POST['total_dynamic']) ? $_POST['total_dynamic'] : $total;

    $status = "Pending";
    $tracking_status = "Menunggu Pembayaran";
    $order_date = date('Y-m-d H:i:s');

    $selected_ids = isset($_POST['selected_products']) && $_POST['selected_products'] !== '' 
        ? explode(',', $_POST['selected_products']) : [];

    // Validasi: tidak bisa checkout tanpa produk yang dipilih
    if(empty($selected_ids)){
        echo "<script>alert('Tidak ada produk yang dipilih!'); window.history.back();</script>";
        exit;
    }

    // Validasi stok sebelum checkout
    foreach($products as $p){
        if(!in_array($p['id'], $selected_ids)) continue;
        $stok_check = mysqli_fetch_assoc(mysqli_query($conn, "SELECT stock FROM products WHERE id='{$p['id']}'"));
        if(!$stok_check || $stok_check['stock'] <= 0){
            echo "<script>alert('Produk \"" . addslashes($p['name']) . "\" stok habis!'); window.history.back();</script>";
            exit;
        }
        if($stok_check['stock'] < $p['qty']){
            echo "<script>alert('Stok produk \"" . addslashes($p['name']) . "\" tidak mencukupi! Sisa stok: " . $stok_check['stock'] . "'); window.history.back();</script>";
            exit;
        }
    }
    
    $proofName = "";
    if($payment == "Transfer" && isset($_FILES['proof']['name']) && $_FILES['proof']['name'] != ""){
        $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
        $ext = strtolower(pathinfo($_FILES['proof']['name'], PATHINFO_EXTENSION));
        
        if(in_array($ext, $allowed)){
            $proofName = uniqid() . "_" . time() . "." . $ext;
            $upload_path = "../admin/uploads/proofs/" . $proofName;
            
            if(!file_exists("../admin/uploads/proofs/")){
                mkdir("../admin/uploads/proofs/", 0777, true);
            }
            
            if(move_uploaded_file($_FILES['proof']['tmp_name'], $upload_path)){
                $proofName = "proofs/" . $proofName;
            }
        } else {
            echo "<script>alert('Format file tidak valid! Gunakan JPG, PNG, atau PDF'); window.history.back();</script>";
            exit;
        }
    }
    
    $query_order = mysqli_query($conn, "INSERT INTO orders 
        (user_id, name, phone, address, payment_method, payment_proof, shipping_expedition, shipping_type, shipping_cost, total_price, status, tracking_status, order_date) 
        VALUES 
        ('$user_id', '$name', '$phone', '$address', '$payment', '$proofName', '$shipping_expedition', '$shipping_type', '$shipping_cost', '$total_price', '$status', '$tracking_status', '$order_date')");
    
    if($query_order){
        $order_id = mysqli_insert_id($conn);
        
        foreach($products as $p){
            if(!in_array($p['id'], $selected_ids)) continue;

            $product_id = $p['id'];
            $price = $p['price'];
            $qty = $p['qty'];
            
            mysqli_query($conn, "INSERT INTO order_details 
                (order_id, product_id, price, quantity) 
                VALUES 
                ('$order_id', '$product_id', '$price', '$qty')");

            // Kurangi stok produk
            mysqli_query($conn, "UPDATE products SET stock = stock - $qty WHERE id = '$product_id'");
        }
        
        unset($_SESSION['cart']);
        unset($_SESSION['cart_qty']);
        
        $_SESSION['success'] = "🎉 Order berhasil! Nomor pesanan: #" . $order_id;
        
        header("Location: index.php");
        exit;
    } else {
        echo "<script>alert('Terjadi kesalahan: " . mysqli_error($conn) . "'); window.history.back();</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Checkout - BuyZone</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{background:#f5f5f5;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;}
.navbar{background:#1fb5aa;padding:14px 20px;}
.navbar-brand,.navbar a{color:white !important;font-weight:600;text-decoration:none;font-size:17px;}
.tab-menu{display:flex;justify-content:center;gap:40px;background:#fff;padding:14px 0;border-bottom:1px solid #e5e7eb;}
.tab-menu a{text-decoration:none;color:#aaa;font-weight:600;font-size:14px;padding-bottom:6px;}
.tab-menu .active{color:#1fb5aa;border-bottom:3px solid #1fb5aa;}

.product-card{background:#fff;border-radius:10px;padding:14px 16px;margin-bottom:10px;display:flex;align-items:center;gap:14px;box-shadow:0 1px 3px rgba(0,0,0,0.05);}
.product-card img{width:65px;height:65px;object-fit:cover;border-radius:8px;background:#eee;}
.product-info h5{font-size:14px;font-weight:700;color:#1e293b;margin-bottom:2px;}
.product-info .price{font-size:13px;font-weight:700;color:#1fb5aa;}
.product-info .desc{font-size:11px;color:#94a3b8;}
.product-scroll{max-height:calc(100vh - 280px);overflow-y:auto;}
.product-check{width:20px;height:20px;accent-color:#1fb5aa;cursor:pointer;}

/* ===== PAYMENT BAR ===== */
.payment-bar{
    position:fixed;bottom:0;left:0;right:0;z-index:100;
    background:#1fb5aa;
    box-shadow:0 -4px 20px rgba(0,0,0,0.15);
    padding:18px 28px;
    color:#fff;
}

.pb-row{display:flex;align-items:center;gap:14px;flex-wrap:wrap;}
.pb-row + .pb-row{margin-top:12px;padding-top:12px;border-top:1px solid rgba(255,255,255,0.18);}

/* Payment tabs */
.payment-tabs{display:flex;gap:8px;}
.payment-tab{background:rgba(255,255,255,0.15);border:none;padding:8px 22px;border-radius:8px;color:#fff;font-weight:600;cursor:pointer;font-size:14px;transition:0.2s;}
.payment-tab.active{background:#fff;color:#1fb5aa;}
.payment-content{display:none;}
.payment-content.active{display:inline;}
.transfer-info{font-size:13px;color:rgba(255,255,255,0.7);}

/* Inputs */
.pb-select,.pb-input{
    border:1px solid rgba(255,255,255,0.25);border-radius:8px;
    padding:10px 14px;background:rgba(255,255,255,0.1);
    font-size:14px;color:#fff;transition:0.2s;
}
.pb-select{appearance:none;cursor:pointer;padding-right:32px;
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='white' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
    background-repeat:no-repeat;background-position:right 10px center;}
.pb-select option{background:#1aa99e;color:#fff;}
.pb-input::placeholder{color:rgba(255,255,255,0.4);}
.pb-select:focus,.pb-input:focus{outline:none;border-color:rgba(255,255,255,0.5);}

.pb-file{border:1px solid rgba(255,255,255,0.25);border-radius:8px;padding:8px 12px;background:rgba(255,255,255,0.1);font-size:13px;color:rgba(255,255,255,0.65);cursor:pointer;}
.pb-file::file-selector-button{background:rgba(255,255,255,0.2);color:#fff;border:none;padding:4px 12px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;margin-right:8px;}
.placeholder-box{background:rgba(255,255,255,0.08);border:1px dashed rgba(255,255,255,0.2);border-radius:8px;padding:10px 14px;font-size:13px;color:rgba(255,255,255,0.45);text-align:center;}

/* Total */
.total-info{text-align:right;white-space:nowrap;}
.total-sub{font-size:13px;color:rgba(255,255,255,0.65);}
.total-grand{font-size:18px;font-weight:700;margin-top:2px;}

/* Order button */
.order-btn{background:#fff;color:#1fb5aa;border:none;padding:12px 36px;border-radius:10px;font-weight:700;font-size:15px;cursor:pointer;transition:0.2s;white-space:nowrap;}
.order-btn:hover:not(:disabled){background:#f0fdfa;}
.order-btn:disabled{background:rgba(255,255,255,0.12);color:rgba(255,255,255,0.3);cursor:not-allowed;}

.main-container{padding:16px 20px 220px;}
.empty-cart{text-align:center;padding:60px 20px;background:#fff;border-radius:12px;margin:20px 0;}
.empty-cart i{font-size:70px;color:#ddd;margin-bottom:16px;}
</style>
</head>

<body>

<nav class="navbar d-flex justify-content-between">
<span class="navbar-brand">BuyZone</span>
<a href="cart.php"><i class="bi bi-arrow-left"></i> Back</a>
</nav>

<div class="tab-menu">
<a href="cart.php">Cart</a>
<a href="checkout.php" class="active">Checkout</a>
<a href="history.php">History</a>
</div>

<div class="main-container">
<div class="product-scroll">

<?php foreach($products as $p): ?>
<div class="product-card">
<img src="../admin/uploads/<?= htmlspecialchars($p['image']); ?>">
<div class="product-info flex-grow-1">
<h5><?= htmlspecialchars($p['name']); ?></h5>
<div class="price">Rp <?= number_format($p['price'], 0, ',', '.'); ?></div>
<div class="desc"><?= htmlspecialchars(substr($p['description'], 0, 60)); ?>... · Qty: <?= $p['qty']; ?></div>
</div>
<input type="checkbox" class="product-check" data-id="<?= $p['id']; ?>" data-price="<?= $p['price']; ?>" data-qty="<?= $p['qty']; ?>" checked>
</div>
<?php endforeach; ?>

</div>
</div>

<!-- ===== PAYMENT BAR ===== -->
<form method="POST" enctype="multipart/form-data" id="checkoutForm">
<div class="payment-bar">

<!-- Baris 1: Payment + Ekspedisi -->
<div class="pb-row">
    <div class="payment-tabs">
        <button type="button" class="payment-tab active" onclick="switchPayment(event,'transfer')">Transfer</button>
        <button type="button" class="payment-tab" onclick="switchPayment(event,'cod')">COD</button>
    </div>
    <div id="transferContent" class="payment-content active">
        <span class="transfer-info">087864200621 (Dana)</span>
    </div>
    <div id="codContent" class="payment-content">
        <span class="transfer-info">Bayar saat diterima</span>
    </div>

    <div style="margin-left:auto;display:flex;gap:6px;align-items:center;">
        <select class="pb-select" id="shippingExpedition" name="shipping_expedition" required>
            <option value="" disabled selected>Ekspedisi (Pilih Tarif)</option>
            <option value="JNE">JNE (Rp 9.000)</option>
            <option value="J&T">J&T Express (Rp 8.000)</option>
            <option value="SiCepat">SiCepat (Rp 8.500)</option>
            <option value="AnterAja">AnterAja (Rp 8.000)</option>
        </select>
    </div>
</div>

<!-- Baris Form Penerima -->
<div class="pb-row">
    <input type="text" name="name" class="pb-input" placeholder="Nama Penerima" value="<?= htmlspecialchars($default_name); ?>" required style="flex:1;min-width:120px;">
    <input type="text" name="phone" class="pb-input" placeholder="Nomor HP" value="<?= htmlspecialchars($default_phone); ?>" required style="flex:1;min-width:120px;">
    <input type="text" name="address" class="pb-input" placeholder="Alamat lengkap..." value="<?= htmlspecialchars($default_address); ?>" required style="flex:2;min-width:150px;">
</div>

<!-- Baris Bukti + Total + Order -->
<div class="pb-row">
    <div id="fileUploadContainer">
        <input type="file" name="proof" class="pb-file" id="proofFile">
    </div>
    <div id="codPlaceholder" style="display:none;">
        <div class="placeholder-box">COD — Tanpa bukti</div>
    </div>

    <div style="margin-left:auto;display:flex;align-items:center;gap:14px;">
        <div class="total-info">
            <div class="total-sub"><span id="subtotalPrice">Rp <?= number_format($total, 0, ',', '.'); ?></span> + Ongkir <span id="ongkirPrice">Rp 0</span></div>
            <div class="total-grand">Total: <span id="totalPrice">Rp <?= number_format($total, 0, ',', '.'); ?></span></div>
        </div>

        <button type="submit" name="orderNow" class="order-btn" id="orderBtn" <?= empty($products) ? 'disabled' : '' ?>>Order</button>
    </div>
</div>

<input type="hidden" name="selected_products" id="selectedProducts">
<input type="hidden" name="total_dynamic" id="totalDynamic">
<input type="hidden" name="payment" id="paymentMethod" value="Transfer">
<input type="hidden" name="shipping_cost" id="shippingCostInput" value="0">

</div>
</form>

<script>
const shippingRates = {
    'JNE': 9000,
    'J&T': 8000,
    'SiCepat': 8500,
    'AnterAja': 8000
};

let currentShippingCost = 0;
let currentSubtotal = 0;

function switchPayment(e, method){
    document.querySelectorAll('.payment-tab').forEach(t => t.classList.remove('active'));
    e.target.classList.add('active');
    document.querySelectorAll('.payment-content').forEach(c => c.classList.remove('active'));

    if(method === 'transfer'){
        document.getElementById('transferContent').classList.add('active');
        document.getElementById('fileUploadContainer').style.display = 'block';
        document.getElementById('codPlaceholder').style.display = 'none';
    } else {
        document.getElementById('codContent').classList.add('active');
        document.getElementById('fileUploadContainer').style.display = 'none';
        document.getElementById('codPlaceholder').style.display = 'block';
    }
    document.getElementById('paymentMethod').value = method.charAt(0).toUpperCase() + method.slice(1);
}

document.getElementById('shippingExpedition').addEventListener('change', function(){
    const expedition = this.value;
    currentShippingCost = shippingRates[expedition] || 0;
    document.getElementById('shippingCostInput').value = currentShippingCost;
    updateGrandTotal();
});

function updateTotal(){
    let total = 0, selected = [];
    document.querySelectorAll('.product-check').forEach(cb => {
        if(cb.checked){
            total += parseInt(cb.dataset.price) * parseInt(cb.dataset.qty);
            selected.push(cb.dataset.id);
        }
    });
    currentSubtotal = total;
    document.getElementById('subtotalPrice').innerText = 'Rp ' + total.toLocaleString('id-ID');
    document.getElementById('selectedProducts').value = selected.join(',');

    const btn = document.getElementById('orderBtn');
    btn.disabled = selected.length === 0;

    updateGrandTotal();
}

function updateGrandTotal(){
    const grand = currentSubtotal + currentShippingCost;
    document.getElementById('ongkirPrice').innerText = 'Rp ' + currentShippingCost.toLocaleString('id-ID');
    document.getElementById('totalPrice').innerText = 'Rp ' + grand.toLocaleString('id-ID');
    document.getElementById('totalDynamic').value = grand;
}

document.querySelectorAll('.product-check').forEach(cb => cb.addEventListener('change', updateTotal));
updateTotal();

document.getElementById('checkoutForm').addEventListener('submit', function(e){
    const sel = document.getElementById('selectedProducts').value;
    if(!sel || sel.trim() === ''){
        e.preventDefault();
        alert('Tidak ada produk yang dipilih!');
        return;
    }
    if(!document.getElementById('shippingExpedition').value){
        e.preventDefault();
        alert('Pilih ekspedisi pengiriman!');
        return;
    }
});
</script>

</body>
</html>
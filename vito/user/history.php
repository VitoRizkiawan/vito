<?php
session_start();
include "../admin/config/database.php";

if(!isset($_SESSION['user_login'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = mysqli_prepare($conn, "
    SELECT o.*, od.product_id, p.name, p.price, p.image 
    FROM orders o 
    JOIN order_details od ON o.id = od.order_id 
    JOIN products p ON od.product_id = p.id 
    WHERE o.user_id = ? 
    ORDER BY o.order_date DESC
");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$orders = [];
while($row = mysqli_fetch_assoc($result)){
    $orders[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Transaksi - BuyZone</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        *{ margin: 0; padding: 0; box-sizing: border-box; }
        body{ background: #f5f7fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .navbar{ background: #1fb5aa; padding: 15px 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .navbar-brand, .navbar a{ color: white !important; font-weight: 600; text-decoration: none; font-size: 18px; }

        .tab-menu{
            display: flex; justify-content: center; gap: 50px; margin-top: 0; 
            background: #fff; padding: 15px 0; border-bottom: 1px solid #eee;
        }
        .tab-menu a{ text-decoration: none; color: #777; font-weight: 600; font-size: 15px; padding-bottom: 5px; }
        .tab-menu .active{ color: #1fb5aa; border-bottom: 3px solid #1fb5aa; }

        .history-card{
            background: #fff; border-radius: 12px; padding: 20px; margin: 20px 0;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03); border: 1px solid #ebebeb;
            display: flex; gap: 20px; align-items: center;
        }
        
        .history-card img{
            width: 100px; height: 100px; object-fit: cover; border-radius: 10px; background: #f0f0f0;
        }

        .history-info{ flex-grow: 1; }
        .history-info h5{ margin-bottom: 5px; font-weight: 700; color: #2c3e50; }
        .history-info .price{ font-size: 16px; font-weight: 700; color: #1fb5aa; margin-bottom: 5px; }
        .history-info .date{ font-size: 13px; color: #888; margin-bottom: 10px; }

        .status-badge{
            display: inline-block; padding: 5px 12px; border-radius: 6px; 
            font-size: 12px; font-weight: 700;
        }
        .bg-pending { background: #fff3cd; color: #856404; border: 1px solid #ffc107; }
        .bg-success { background: #d1e7dd !important; color: #0f5132 !important; border: 1px solid #198754; }
        .bg-cancelled { background: #f8d7da; color: #842029; border: 1px solid #dc3545; }

        .empty-history{
            text-align: center; padding: 80px 20px; background: white; border-radius: 15px; margin: 40px 0;
        }
        .empty-history i{ font-size: 80px; color: #ddd; margin-bottom: 20px; display: block;}

        @media (max-width: 768px){
            .history-card{ flex-direction: column; text-align: center; }
            .history-card img{ width: 100%; height: 200px; }
        }
    </style>
</head>

<body>

<nav class="navbar d-flex justify-content-between">
    <span class="navbar-brand">BuyZone</span>
    <a href="index.php">Back</a>
</nav>

<div class="tab-menu">
    <a href="cart.php">Cart</a>
    <a href="checkout.php">Checkout</a>
    <a href="history.php" class="active">History</a>
</div>

<div class="container pb-5">
    <?php if(empty($orders)): ?>
        <div class="empty-history">
            <i class="bi bi-clock-history"></i>
            <h4 style="color:#666; margin-bottom:20px;">Belum Ada Riwayat Pembayaran</h4>
            <a href="index.php" class="btn btn-lg" style="background:#1fb5aa; color:white; border-radius:10px; padding:12px 40px;">
                Belanja Sekarang
            </a>
        </div>
    <?php else: ?>

        <?php foreach($orders as $order): ?>
        <div class="history-card">
            <img src="../admin/uploads/<?= htmlspecialchars($order['image']); ?>" 
                 alt="<?= htmlspecialchars($order['name']); ?>"
                 onerror="this.src='https://via.placeholder.com/100?text=No+Image'">
            
            <div class="history-info">
                <h5><?= htmlspecialchars($order['name']); ?></h5>
                <div class="price">Rp <?= number_format($order['price'], 0, ',', '.'); ?></div>
                <div class="date"><i class="bi bi-calendar3"></i> <?= date('d M Y, H:i', strtotime($order['order_date'])); ?></div>
                
                <?php 
                    $stat = strtolower($order['status']);
                    $bg = "bg-pending";
                    if($stat == 'success') $bg = "bg-success";
                    elseif($stat == 'cancelled') $bg = "bg-cancelled";
                ?>
                <div class="status-badge <?= $bg ?>">
                    <?= ucfirst(strtolower($order['status'])) ?>
                </div>
            </div>
            
            <div class="text-end">
                <a href="tracking.php" class="btn btn-sm btn-outline-secondary">Lacak Paket <i class="bi bi-truck"></i></a>
            </div>
        </div>
        <?php endforeach; ?>

    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
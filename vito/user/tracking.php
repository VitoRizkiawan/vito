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
    <title>Pesanan Saya & Tracking - BuyZone</title>
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
            background: #fff; border-radius: 12px; padding: 25px; margin: 20px 0;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03); border: 1px solid #ebebeb;
            display: flex; gap: 20px; align-items: flex-start;
        }
        
        .history-card img{
            width: 100px; height: 100px; object-fit: cover; border-radius: 10px; background: #f0f0f0;
        }

        .history-info{ flex-grow: 1; }
        .history-info h5{ margin-bottom: 5px; font-weight: 700; color: #2c3e50; }
        .history-info .price{ font-size: 16px; font-weight: 700; color: #1fb5aa; margin-bottom: 4px; }
        .history-info .date{ font-size: 13px; color: #888; margin-bottom: 15px; }

        .status-badge{
            display: inline-block; padding: 6px 14px; border-radius: 6px; 
            font-size: 12px; font-weight: 700; margin-bottom: 15px;
        }
        .bg-cancelled { background: #ffebee; color: #c62828; }

        /* TRACKING BAR */
        .tracking-box{
            margin-top: 10px; padding-top: 15px; border-top: 1px dashed #eee;
        }
        .track-wrap{
            display: flex; justify-content: space-between; position: relative; max-width: 500px;
        }
        .track-wrap::before{
            content: ''; position: absolute; top: 14px; left: 15px; right: 15px;
            height: 3px; background: #e0e0e0; z-index: 1;
        }
        
        .track-step{
            position: relative; z-index: 2; text-align: center; flex: 1;
        }
        .track-icon{
            width: 30px; height: 30px; border-radius: 50%; background: #e0e0e0;
            color: white; display: flex; align-items: center; justify-content: center;
            margin: 0 auto 5px; font-size: 14px; transition: 0.3s;
        }
        .track-label{
            font-size: 11px; font-weight: 600; color: #aaa;
        }

        /* Active states */
        .track-step.done .track-icon{ background: #1fb5aa; box-shadow: 0 0 0 3px rgba(31, 181, 170, 0.2); }
        .track-step.done .track-label{ color: #1fb5aa; }

        .line-fill{
            position: absolute; top: 14px; left: 15px; height: 3px; background: #1fb5aa; z-index: 1; transition: 0.5s;
        }

        .empty-history{
            text-align: center; padding: 80px 20px; background: white; border-radius: 15px; margin: 40px 0;
        }
        .empty-history i{ font-size: 80px; color: #ddd; margin-bottom: 20px; display: block;}

        @media (max-width: 768px){
            .history-card{ flex-direction: column; text-align: left; }
            .history-card img{ width: 100%; height: 200px; }
            .track-wrap{ max-width: 100%; }
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
    <a href="history.php">History</a>
    <a href="tracking.php" class="active">Tracking</a>
</div>

<div class="container pb-5">
    <?php if(empty($orders)): ?>
        <div class="empty-history">
            <i class="bi bi-box-seam"></i>
            <h4 style="color:#666; margin-bottom:20px;">Belum Ada Pesanan</h4>
            <a href="index.php" class="btn btn-lg" style="background:#1fb5aa; color:white; border-radius:10px; padding:12px 40px;">
                Belanja Sekarang
            </a>
        </div>
    <?php else: ?>

        <?php foreach($orders as $order): 
            $status = $order['tracking_status'];
            
            // Map status ke urutan (1 to 4)
            $step = 0;
            $is_cancelled = false;
            
            if($status == 'Menunggu Pembayaran') $step = 1;
            elseif($status == 'Dikemas') $step = 2;
            elseif($status == 'Dikirim') $step = 3;
            elseif($status == 'Selesai') $step = 4;
            elseif($status == 'Dibatalkan') $is_cancelled = true;
            
            // Hitung persentase lebar garis progress bar
            $line_width = 0;
            if($step == 1) $line_width = 0;
            if($step == 2) $line_width = 33;
            if($step == 3) $line_width = 66;
            if($step == 4) $line_width = 100;
        ?>
        <div class="history-card">
            <img src="../admin/uploads/<?= htmlspecialchars($order['image']); ?>" 
                 alt="<?= htmlspecialchars($order['name']); ?>"
                 onerror="this.src='https://via.placeholder.com/100?text=No+Image'">
            
            <div class="history-info">
                <h5><?= htmlspecialchars($order['name']); ?></h5>
                <div class="price">Rp <?= number_format($order['price'], 0, ',', '.'); ?></div>
                <div class="date"><i class="bi bi-calendar3"></i> <?= date('d M Y, H:i', strtotime($order['order_date'])); ?></div>
                
                <?php if($is_cancelled): ?>
                    <div class="status-badge bg-cancelled">
                        <i class="bi bi-x-circle"></i> Pesanan Dibatalkan
                    </div>
                <?php else: ?>
                    <!-- TRACKING UI -->
                    <div class="tracking-box">
                        <div class="track-wrap">
                            <div class="line-fill" style="width: <?= $line_width ?>%;"></div>
                            
                            <div class="track-step <?= $step >= 1 ? 'done' : '' ?>">
                                <div class="track-icon"><i class="bi bi-wallet2"></i></div>
                                <div class="track-label">Bayar</div>
                            </div>
                            <div class="track-step <?= $step >= 2 ? 'done' : '' ?>">
                                <div class="track-icon"><i class="bi bi-box-seam"></i></div>
                                <div class="track-label">Dikemas</div>
                            </div>
                            <div class="track-step <?= $step >= 3 ? 'done' : '' ?>">
                                <div class="track-icon"><i class="bi bi-truck"></i></div>
                                <div class="track-label">Dikirim</div>
                            </div>
                            <div class="track-step <?= $step >= 4 ? 'done' : '' ?>">
                                <div class="track-icon"><i class="bi bi-check2-circle"></i></div>
                                <div class="track-label">Selesai</div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>

    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
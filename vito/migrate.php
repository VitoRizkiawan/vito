<?php
require_once "c:/xampp/htdocs/vito/admin/config/database.php";

mysqli_query($conn, "UPDATE orders SET status='Menunggu Pembayaran' WHERE status='Pending'");
mysqli_query($conn, "UPDATE orders SET status='Selesai' WHERE status='Success'");
mysqli_query($conn, "UPDATE orders SET status='Dibatalkan' WHERE status='Cancelled'");

echo "DB Migration done.\n";
?>

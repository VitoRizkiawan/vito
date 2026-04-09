<?php
require_once "c:/xampp/htdocs/vito/admin/config/database.php";

// Tambah kolom tracking_status
$check = mysqli_query($conn, "SHOW COLUMNS FROM orders LIKE 'tracking_status'");
if(mysqli_num_rows($check) == 0){
    mysqli_query($conn, "ALTER TABLE orders ADD COLUMN tracking_status VARCHAR(50) DEFAULT 'Menunggu Pembayaran'");
}

// Set initial tracking_status sama dengan status saat ini (agar migrate tidak kosong)
// status saat ini adalah Menunggu Pembayaran, Dikemas, Dikirim, Selesai, Dibatalkan.
mysqli_query($conn, "UPDATE orders SET tracking_status=status WHERE tracking_status='Menunggu Pembayaran' OR tracking_status IS NULL");

// Kembalikan kolom status (pembayaran) menjadi gaya lama
mysqli_query($conn, "UPDATE orders SET status='Pending' WHERE status='Menunggu Pembayaran'");
mysqli_query($conn, "UPDATE orders SET status='Success' WHERE status IN ('Dikemas','Dikirim','Selesai')");
mysqli_query($conn, "UPDATE orders SET status='Cancelled' WHERE status='Dibatalkan'");

echo "DB Migration 2 done.\n";
?>

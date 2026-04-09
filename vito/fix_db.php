<?php
require_once "c:/xampp/htdocs/vito/admin/config/database.php";

$alter = mysqli_query($conn, "ALTER TABLE orders MODIFY status VARCHAR(50) DEFAULT 'Pending'");
if($alter){
    echo "Altered successfully!\n";
    mysqli_query($conn, "UPDATE orders SET status='Pending' WHERE status='' OR status='pending'");
    mysqli_query($conn, "UPDATE orders SET status='Success' WHERE status='success'");
    mysqli_query($conn, "UPDATE orders SET status='Cancelled' WHERE status='cancelled'");
    echo "Fixed statuses.\n";
} else {
    echo "Error: " . mysqli_error($conn) . "\n";
}
?>

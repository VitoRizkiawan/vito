<?php
require_once "../admin/config/database.php";

$id = intval($_GET['id']);
$status = strtolower($_GET['status']);

echo "ID: $id <br>";
echo "STATUS: $status <br><br>";

if($status == 'success' || $status == 'cancelled'){

    $update = mysqli_query($conn, "
        UPDATE orders SET status='$status' WHERE id='$id'
    ");

    if(!$update){
        die("ERROR UPDATE: " . mysqli_error($conn));
    }

    echo "UPDATE BERHASIL <br>";

    if($status == 'success'){

        $cek = mysqli_query($conn, "SELECT * FROM success WHERE order_id=$id");

        if(mysqli_num_rows($cek) == 0){

            $data = mysqli_fetch_assoc(mysqli_query($conn, "
                SELECT orders.*, users.username 
                FROM orders 
                JOIN users ON orders.user_id = users.id 
                WHERE orders.id = $id
            "));

            if(!$data){
                die("DATA TIDAK DITEMUKAN!");
            }

            $insert = mysqli_query($conn, "
                INSERT INTO success(order_id, customer_name, payment_method, total_price)
                VALUES(
                    '{$data['id']}',
                    '{$data['username']}',
                    '{$data['payment_method']}',
                    '{$data['total_price']}'
                )
            ");

            if(!$insert){
                die("ERROR INSERT: " . mysqli_error($conn));
            }

            echo "INSERT SUCCESS BERHASIL <br>";
        } else {
            echo "DATA SUDAH ADA (TIDAK DOUBLE) <br>";
        }
    }
} else {
    echo "STATUS TIDAK VALID!";
}

exit;
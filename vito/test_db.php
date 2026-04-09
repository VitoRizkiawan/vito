<?php
require_once "c:/xampp/htdocs/vito/admin/config/database.php";

$res = mysqli_query($conn, "DESCRIBE orders");
while($row = mysqli_fetch_assoc($res)){
    echo str_pad($row['Field'], 20) . " | " . str_pad($row['Type'], 30) . " | " . $row['Default'] . "\n";
}
?>

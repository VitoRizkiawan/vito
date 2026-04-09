<?php
session_start();

if(!isset($_SESSION['user_login'])){
    header("Location: login.php");
    exit;
}

if(isset($_GET['id'])){
    $id = intval($_GET['id']);
    
    if(!isset($_SESSION['cart'])){
        $_SESSION['cart'] = [];
    }
    if(!isset($_SESSION['cart_qty'])){
        $_SESSION['cart_qty'] = [];
    }
    
    if(!in_array($id, $_SESSION['cart'])){
        $_SESSION['cart'][] = $id;
        $_SESSION['cart_qty'][$id] = 1;
    }
}

header("Location: dashboard.php");
exit;
?>
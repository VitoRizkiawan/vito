<?php
session_start();
include "../admin/config/database.php";

if(isset($_POST['login'])){
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
    $user = mysqli_fetch_assoc($query);

    if($user && password_verify($password, $user['password'])){

        $_SESSION['user_login'] = true;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // 🔥 CEK ROLE
        if($user['role'] == 'admin'){
            header("Location: ../admin/dashboard.php");
        } elseif($user['role'] == 'petugas'){
            header("Location: ../petugas/dashboard.php");
        } else {
            header("Location: index.php"); // user biasa
        }

        exit;

    } else {
        $error = "Username atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - BuyZone</title>

<style>
*{
    box-sizing:border-box;
}

body{
    margin:0;
    padding:0;
    background:#2dbdb6;
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* CARD LOGIN */
.login-card{
    width:700px;
    height:500px;
    background:#B0FFFA;
    border-radius:15px;
    box-shadow:0 10px 25px rgba(0,0,0,0.2);
    display:flex;
    flex-direction:column;
    justify-content:center;
    align-items:center;
    text-align:center;
    padding:40px;
}

/* TEXT */
.header-text{
    font-size:22px;
    margin-bottom:5px;
}

.title-text{
    font-size:36px;
    font-weight:700;
    margin-bottom:40px;
}

/* INPUT */
.form-control{
    width:524px;
    height:66px;
    border-radius:14px;
    border:none;
    padding:0 25px;
    font-size:16px;
    margin-bottom:25px;
    background:#fff;
    outline:none;
}

.form-control:focus{
    box-shadow:0 0 0 2px #02BDB1;
}

/* BUTTON */
.btn-login{
    width:360px;
    height:66px;
    border-radius:14px;
    border:none;
    background:#02BDB1;
    color:white;
    font-size:18px;
    font-weight:700;
    cursor:pointer;
    transition:0.3s;
}

.btn-login:hover{
    background:#029f95;
}

/* FOOTER */
.footer-text{
    margin-top:35px;
    font-size:15px;
}

.footer-text a{
    font-weight:700;
    color:#008a80;
    text-decoration:none;
}

.footer-text a:hover{
    text-decoration:underline;
}

/* ERROR */
.alert-danger{
    width:524px;
    background:#ffd6d6;
    color:#b30000;
    padding:15px;
    border-radius:10px;
    margin-bottom:20px;
    font-size:14px;
}
</style>
</head>

<body>

<div class="login-card">

    <p class="header-text">Hi! Welcome To BuyZone</p>
    <h2 class="title-text"></h2>

    <?php if(isset($error)): ?>
        <div class="alert-danger">
            <?= $error ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="username" class="form-control" placeholder="Username" required>
        <input type="password" name="password" class="form-control" placeholder="Password" required>
        <button type="submit" name="login" class="btn-login">LOGIN</button>
    </form>

    <div class="footer-text">
        Don't Have Account? <a href="register.php">Sign Up</a>
    </div>

</div>

</body>
</html>
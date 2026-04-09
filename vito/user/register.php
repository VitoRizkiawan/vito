<?php
session_start();
include "../admin/config/database.php";

if(isset($_POST['register'])){

    // ambil data dari form
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // validasi input kosong
    if(empty($username) || empty($email) || empty($password)){
        $error = "Semua field wajib diisi!";
    } else {

        // hash password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // set role otomatis
        $role = "user";

        // cek username atau email sudah ada
        $cek = mysqli_query($conn, "SELECT id FROM users WHERE username='$username' OR email='$email'");

        if(mysqli_num_rows($cek) > 0){
            $error = "Username atau Email sudah digunakan!";
        } else {

            // query insert (role dipastikan masuk)
            $query = "INSERT INTO users (username, email, password, role) 
                      VALUES ('$username', '$email', '$password_hash', '$role')";

            $insert = mysqli_query($conn, $query);

            if($insert){
                $success = "Akun berhasil dibuat!";
            } else {
                $error = "Gagal membuat akun! " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sign Up - BuyZone</title>

<style>
*{ box-sizing:border-box; }

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

/* CARD */
.login-card{
    width:700px;
    height:650px;
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

/* TITLE */
.title-text{
    font-size:34px;
    font-weight:700;
    margin-bottom:35px;
}

/* INPUT */
.form-control{
    width:524px;
    height:66px;
    border-radius:14px;
    border:none;
    padding:0 25px;
    font-size:16px;
    margin-bottom:22px;
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
    margin-top:30px;
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

/* ALERT */
.alert{
    width:524px;
    padding:15px;
    border-radius:10px;
    margin-bottom:20px;
    font-size:14px;
}

.alert-danger{
    background:#ffd6d6;
    color:#b30000;
}

.alert-success{
    background:#d4edda;
    color:#155724;
}
</style>
</head>

<body>

<div class="login-card">

    <h2 class="title-text">Sign Up</h2>

    <?php if(isset($error)): ?>
        <div class="alert alert-danger">
            <?= $error ?>
        </div>
    <?php endif; ?>

    <?php if(isset($success)): ?>
        <div class="alert alert-success">
            <?= $success ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="username" class="form-control" placeholder="Username" required>
        <input type="email" name="email" class="form-control" placeholder="Email" required>
        <input type="password" name="password" class="form-control" placeholder="Password" required>
        <button type="submit" name="register" class="btn-login">CREATE ACCOUNT</button>
    </form>

    <div class="footer-text">
        Have An Account? <a href="login.php">Login</a>
    </div>

</div>

</body>
</html>
    <?php
    session_start();
    require_once "config/database.php";

    if(isset($_POST['login'])){

        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $password = $_POST['password'];

        $query = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
        $user = mysqli_fetch_assoc($query);

        if($user && password_verify($password, $user['password'])){

            // SESSION (INI YANG PENTING)
            $_SESSION['admin_login'] = true;
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            if($user['role'] == 'admin'){
                header("Location: dashboard.php");
            } else if($user['role'] == 'petugas'){
                header("Location: petugas/dashboard.php");
            }

            exit;

        } else {
            $error = "Username atau password salah!";
        }
    }
    ?>

    <!DOCTYPE html>
    <html>
    <head>
    <title>Login Admin</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
    body{
        height:100vh;
        display:flex;
        justify-content:center;
        align-items:center;
        background:#007F77;
    }

    .login-box{
        height: 500px;
        width: 700px;
        padding:40px;
        border-radius:15px;
        background:#149a8d;
        color:white;
        box-shadow:0 10px 25px rgba(0,0,0,0.2);
        display:flex;
        flex-direction:column;
        justify-content:center; /* center vertical */
        align-items:center; /* center horizontal */
    }

    /* INPUT */
    .form-control{
        width:524px;
        height:66px;
        border-radius:20px;
        border:none;
        padding:0 20px;
    }

    /* BUTTON LOGIN */
    .btn-login{
        width:380px;
        height:66px;
        border-radius:20px;
        background:#02BDB1;
        color:white;
        border:none;
        font-weight:600;
    }

    .btn-login:hover{
        background:#01a79d;
    }

    </style>

    </head>
    <body>

    <div class="login-box">

    <h3 class="mb-5 text-center">Hi! Welcome Admin</h3>

    <!-- <?php if(isset($error)): ?>
    <div class="alert alert-danger w-100 text-center">
    <?= $error ?>
    </div>e
    <?php endif; ?> -->

    <form method="POST" class="d-flex flex-column align-items-center">

    <div class="mb-3">
    <input type="text" name="username" class="form-control" placeholder="Username" required>
    </div>

    <div class="mb-4">
    <input type="password" name="password" class="form-control" placeholder="Password" required>
    </div>

    <button name="login" class="btn-login">
    LOGIN
    </button>

    </form>

    </div>

    </body>
    </html>

<?php
session_start();

/* HAPUS SEMUA SESSION */
session_unset();
session_destroy();

/* OPTIONAL: HAPUS COOKIE SESSION */
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

/* REDIRECT KE HALAMAN LOGIN */
header("Location: login.php");
exit;
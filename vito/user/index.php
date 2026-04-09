<?php
session_start();

include "../admin/config/database.php";

$is_logged_in = isset($_SESSION['user_login']) && $_SESSION['role'] === 'user';

/* 🔥 AMBIL DATA USER */
$user = null;
if ($is_logged_in) {
    $user_id = $_SESSION['user_id'];
    $query_user = mysqli_query($conn, "
        SELECT u.id, u.username, u.email, u.name as u_name, 
               p.full_name, p.phone as p_phone, p.address as p_address, p.photo 
        FROM users u 
        LEFT JOIN user_profiles p ON u.id = p.user_id 
        WHERE u.id='$user_id'
    ");
    $user = mysqli_fetch_assoc($query_user);
    if($user){
        $user['name'] = !empty($user['full_name']) ? $user['full_name'] : $user['u_name'];
        $user['phone'] = $user['p_phone'] ?? '';
        $user['address'] = $user['p_address'] ?? '';
        $user['profile_picture'] = $user['photo'] ?? '';
    } else {
        // Jika user tidak ada di database tapi session masih ada (misal akun dihapus manual)
        session_destroy();
        header("Location: login.php");
        exit;
    }
}

/* 🔥 UPDATE PROFILE */
if(isset($_POST['saveProfile'])){
    if (!$is_logged_in) {
        header("Location: login.php");
        exit;
    }
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);

    // Check existing profile
    $check_prof = mysqli_query($conn, "SELECT photo FROM user_profiles WHERE user_id='$user_id'");
    $prof_data = mysqli_fetch_assoc($check_prof);
    $photo = $prof_data ? $prof_data['photo'] : '';
    
    if(isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0){
        $allowed = ['jpg', 'jpeg', 'png'];
        $ext = strtolower(pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION));
        
        if(in_array($ext, $allowed)){
            $filename = uniqid() . "_prof." . $ext;
            $upload_path = "../admin/uploads/profiles/";
            if(!file_exists($upload_path)){ mkdir($upload_path, 0777, true); }
            
            if(move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_path . $filename)){
                if(!empty($photo) && file_exists($upload_path . $photo)){
                    unlink($upload_path . $photo);
                }
                $photo = $filename;
            }
        }
    }

    // Update users table
    mysqli_query($conn, "UPDATE users SET name='$name', username='$username', email='$email' WHERE id='$user_id'");

    // Update or Insert into user_profiles
    if($prof_data){
        mysqli_query($conn, "UPDATE user_profiles SET full_name='$name', phone='$phone', address='$address', photo='$photo' WHERE user_id='$user_id'");
    } else {
        mysqli_query($conn, "INSERT INTO user_profiles (user_id, full_name, phone, address, photo) VALUES ('$user_id', '$name', '$phone', '$address', '$photo')");
    }

    $_SESSION['username'] = $username;

    header("Location: index.php?showProfile=1&success=1");
    exit;
}

/* 🔥 SEARCH */
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : "";

if($search != ""){
    $query = "SELECT * FROM products 
              WHERE name LIKE '%$search%' 
              OR category LIKE '%$search%'";
}else{
    $query = "SELECT * FROM products";
}

$result = mysqli_query($conn, $query);

/* 🔥 BANNER SLIDER PHP */
$banner_images = glob("../admin/uploads/banner*.jpg");
if(count($banner_images) == 0){
    $banner_images[] = "https://via.placeholder.com/1200x220";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>BuyZone Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
body{ background:#ffff; }

.navbar{ background:#1fb5aa; }
.navbar-brand{ color:white !important; font-weight:600; }

/* 🔹 BANNER SESUAI LEBAR SEARCH BAR */
.banner-container{
    margin-top:20px;
}
.banner{
    background:#dcdcdc;
    height:220px;
    display:flex;
    justify-content:center;
    align-items:center;
    overflow:hidden;
    position: relative;
    border-radius:15px;
}
.banner img{
    width:100%;
    height:100%;
    object-fit:cover;
    position:absolute;
    top:0;
    left:0;
    opacity:0;
    transition: opacity 1s ease-in-out;
}
.banner img.active{
    opacity:1;
}

.product-card{
    position: relative;
    border-radius: 15px;
    overflow: hidden;
}

.product-img{
    width: 100%;
    height: 400px;
    object-fit: cover;
    background: #ccc;
}

.product-info{
    position: absolute;
    bottom: 0;
    width: 100%;
    background: #18a999;
    padding: 15px;
    border-top-left-radius: 15px;
    border-top-right-radius: 15px;
}

.product-title{
    color: white;
    font-weight: 600;
    font-size: 15px;
}

.product-img img{
    width:100%;
    height:100%;
    object-fit:cover;
}

.btn-detail,.btn-cart{
    width:145px;
    height:46px;
    display:flex;
    align-items:center;
    justify-content:center;
    border-radius:8px;
    font-weight:600;
}

.btn-detail{ background:#0d6efd; color:white; border:none; }
.btn-cart{ background:#e5e5e5; border:none; }

.footer{
    background:#1fb5aa;
    color:white;
    padding:20px;
    margin-top:40px;
}

.nav-icons a{
    color:white;
    font-size:20px;
    margin-left:18px;
    text-decoration:none;
}
.nav-icons a:hover{
    color:#e0f7f5;
}
</style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar px-4 d-flex justify-content-between">

<span class="navbar-brand">BuyZone</span>

<div class="d-flex align-items-center text-white">

<div class="nav-icons text-white d-flex align-items-center">

<?php if($is_logged_in): ?>
    <a href="cart.php" title="Cart"><i class="bi bi-cart3"></i></a>
    <a href="history.php" title="History"><i class="bi bi-clock-history"></i></a>
    <a href="tracking.php" title="Tracking Paket"><i class="bi bi-box-seam"></i></a>
    <!-- 🔥 PROFILE BUTTON -->
    <a href="?showProfile=1">
        <?php if(!empty($user['profile_picture'])): ?>
            <img src="../admin/uploads/profiles/<?= htmlspecialchars($user['profile_picture']); ?>" class="rounded-circle" style="width:24px; height:24px; object-fit:cover; border:1px solid white;">
        <?php else: ?>
            <i class="bi bi-person-circle"></i>
        <?php endif; ?>
    </a>
    <a href="logout.php"><i class="bi bi-box-arrow-right"></i></a>
<?php else: ?>
    <a href="login.php" class="btn btn-light btn-sm fw-bold px-3 ms-3" style="color:#1fb5aa; margin-left:18px;">Login</a>
<?php endif; ?>

</div>

</div>
</nav>

<!-- 🔥 PROFILE PAGE -->
<?php if(isset($_GET['showProfile'])): ?>
    <?php 
    if(!$is_logged_in) {
        header("Location: login.php");
        exit;
    }
    ?>

<div class="container mt-5">
    <div class="card p-4">

        <h4 class="mb-4">Profile</h4>

        <?php if(isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Data profile berhasil disimpan!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">

            <div class="text-center mb-4">
                <div class="mx-auto position-relative" style="width:120px; height:120px; border-radius:50%; border: 3px solid #1fb5aa; background:#ccc; overflow:hidden;">
                    <?php if(!empty($user['profile_picture'])): ?>
                        <img src="../admin/uploads/profiles/<?= htmlspecialchars($user['profile_picture']); ?>" id="preview_prof" style="width:100%; height:100%; object-fit:cover;">
                        <i class="bi bi-person d-none" id="prof_icon" style="font-size:50px; color:white; line-height:114px;"></i>
                    <?php else: ?>
                        <img src="" id="preview_prof" class="d-none" style="width:100%; height:100%; object-fit:cover;">
                        <i class="bi bi-person d-flex align-items-center justify-content-center h-100" id="prof_icon" style="font-size:50px; color:white;"></i>
                    <?php endif; ?>
                </div>
                <div class="mt-3">
                    <label for="profile_picture" class="btn btn-sm btn-outline-primary" style="cursor:pointer;">
                        <i class="bi bi-camera"></i> Ubah Foto
                    </label>
                    <input type="file" id="profile_picture" name="profile_picture" style="display:none;" accept="image/png, image/jpeg, image/jpg" onchange="previewImage(event)">
                </div>
            </div>

            <div class="mb-3">
                <label>Nama</label>
                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name'] ?? ''); ?>">
            </div>

            <div class="mb-3">
                <label>Username</label>
                <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['username'] ?? ''); ?>" required>
            </div>

            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email'] ?? ''); ?>" required>
            </div>

            <div class="mb-3">
                <label>No Telp</label>
                <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone'] ?? ''); ?>">
            </div>

            <div class="mb-3">
                <label>Alamat</label>
                <textarea name="address" class="form-control"><?= htmlspecialchars($user['address'] ?? ''); ?></textarea>
            </div>

            <button type="submit" name="saveProfile" class="btn btn-success">Simpan</button>
            <a href="index.php" class="btn btn-secondary">Kembali</a>

        </form>

        <script>
        function previewImage(event) {
            const input = event.target;
            if(input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e){
                    let img = document.getElementById('preview_prof');
                    let icon = document.getElementById('prof_icon');
                    if(img) {
                        img.src = e.target.result;
                        img.classList.remove('d-none');
                    }
                    if(icon) {
                        icon.classList.remove('d-flex');
                        icon.classList.add('d-none');
                    }
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        </script>

    </div>
</div>

<?php else: ?>

<!-- NORMAL DASHBOARD -->

<div class="container banner-container">
    <div class="banner" id="bannerContainer">
        <?php foreach($banner_images as $i => $img): ?>
            <img src="<?= $img; ?>" class="<?= $i===0?'active':''; ?>" onerror="this.src='https://via.placeholder.com/1200x220'">
        <?php endforeach; ?>
    </div>
</div>

<div class="container mt-4">
<form method="GET">
<div class="input-group">
<input type="text" name="search" class="form-control" placeholder="Search Item..." value="<?= htmlspecialchars($search); ?>">
<button class="btn btn-success"><i class="bi bi-search"></i></button>
</div>
</form>
</div>

<div class="container py-5">
<div class="row">

<?php while($row = mysqli_fetch_assoc($result)) : ?>

<div class="col-md-3 mb-4">
<div class="product-card">

<img src="../admin/uploads/<?= $row['image']; ?>" class="product-img">

<div class="product-info">

<div>
<div class="product-title"><?= $row['name']; ?></div>
<small class="text-white"><?= $row['category']; ?></small><br>
<small class="text-white fw-semibold">Rp <?= number_format($row['price']); ?></small>
</div>

<div class="d-flex justify-content-between mt-2">
<a href="detail.php?id=<?= $row['id']; ?>" class="btn btn-primary btn-sm">Detail</a>
<?php if($row['stock'] > 0): ?>
<a href="<?= $is_logged_in ? 'cart.php?add='.$row['id'] : 'login.php' ?>" class="btn btn-light btn-sm">Cart</a>
<?php else: ?>
<span class="badge bg-danger d-flex align-items-center" style="font-size:12px;">Stok Habis</span>
<?php endif; ?>
</div>

</div>
</div>
</div>

<?php endwhile; ?>

</div>
</div>

<div class="footer d-flex justify-content-between px-4">
<div>BuyZone</div>
</div>

<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- 🔥 JS Banner Slider dengan Fade -->
<script>
const banners = document.querySelectorAll('#bannerContainer img');
let current = 0;

setInterval(() => {
    banners[current].classList.remove('active');
    current = (current + 1) % banners.length;
    banners[current].classList.add('active');
}, 5000); // ganti tiap 5 detik
</script>

</body>
</html>
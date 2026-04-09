<?php
session_start();
require_once "../admin/config/database.php";

if(!isset($_SESSION['petugas_login'])){
    header("Location: login.php");
    exit;
}

/* ================= UPDATE STATUS VIA DROPDOWN ================= */
if(isset($_POST['update_field'])){
    $order_id = intval($_POST['order_id']);
    $new_status = mysqli_real_escape_string($conn, $_POST['new_status']);
    $field = $_POST['update_field'];

    if($field == 'status'){
        if(in_array($new_status, ['Pending','Success','Cancelled'])){
            mysqli_query($conn, "UPDATE orders SET status='$new_status' WHERE id='$order_id'");

            // Jika Selesai/Success, insert ke tabel success (jika belum ada)
            if($new_status == 'Success'){
                $cek = mysqli_query($conn, "SELECT * FROM success WHERE order_id=$order_id");
                if(mysqli_num_rows($cek) == 0){
                    $data = mysqli_fetch_assoc(mysqli_query($conn, "
                        SELECT orders.*, users.username 
                        FROM orders 
                        JOIN users ON orders.user_id = users.id 
                        WHERE orders.id = $order_id
                    "));
                    if($data){
                        mysqli_query($conn, "
                            INSERT INTO success(order_id, customer_name, payment_method, total_price)
                            VALUES(
                                '{$data['id']}',
                                '{$data['username']}',
                                '{$data['payment_method']}',
                                '{$data['total_price']}'
                            )
                        ");
                    }
                }
            }
        }
    } elseif($field == 'tracking_status') {
        if(in_array($new_status, ['Menunggu Pembayaran', 'Dikemas', 'Dikirim', 'Selesai', 'Dibatalkan'])){
            mysqli_query($conn, "UPDATE orders SET tracking_status='$new_status' WHERE id='$order_id'");
        }
    }

    header("Location: transactions.php?msg=updated");
    exit;
}

/* ================= AMBIL DATA ================= */
$transactions = mysqli_query($conn, "
SELECT 
    orders.id,
    users.username AS customer_name,
    orders.payment_method AS category,
    orders.total_price AS price,
    orders.payment_proof,
    orders.status,
    orders.tracking_status,
    orders.order_date AS created_at
FROM orders
JOIN users ON orders.user_id = users.id
ORDER BY orders.id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Transaction Management</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<!-- SWEETALERT -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
body{
margin:0;
background:#cfcfcf;
}

.main-content{
margin-left:240px;
padding:40px;
}

/* ✅ WARNA DIUBAH DI SINI */
.table-header{
background:#00504B;
color:white;
}

.btn-view{
background:#007F77;
color:white;
border:none;
}

.btn-success{
background:green;
color:white;
border:none;
}

.btn-danger{
background:red;
color:white;
border:none;
}
</style>

</head>

<body>

<?php include 'includes/sidebar.php'; ?>

<div class="main-content">

<h4 class="mb-4">Transaction Management</h4>

<table class="table table-bordered bg-white">

<thead class="table-header">
<tr>
<th>Customer</th>
<th>Category</th>
<th>Price</th>
<th>Proof</th>
<th>Payment Status</th>
<th>Tracking Status</th>
<th>Action</th>
</tr>
</thead>

<tbody>

<?php while($row = mysqli_fetch_assoc($transactions)): ?>

<tr>
<td><?= $row['customer_name'] ?></td>
<td><?= $row['category'] ?></td>
<td>Rp <?= number_format($row['price'],0,',','.') ?></td>

<td>
<?php if($row['payment_proof']): ?>
<button class="btn btn-view" data-bs-toggle="modal" data-bs-target="#proof<?= $row['id'] ?>">
View
</button>
<?php else: ?>
-
<?php endif; ?>
</td>

<!-- PAYMENT STATUS DROPDOWN -->
<td>
<select class="form-select form-select-sm status-dropdown"
    data-field="status"
    data-id="<?= $row['id'] ?>"
    data-original="<?= $row['status'] ?>"
    style="width:130px; font-weight:600;
    <?php
        $st = strtolower($row['status']);
        if($st=='pending') echo 'background:#fff3cd; color:#856404; border-color:#ffc107;';
        elseif($st=='success') echo 'background:#d1e7dd; color:#0f5132; border-color:#198754;';
        else echo 'background:#f8d7da; color:#842029; border-color:#dc3545;';
    ?>">
    <option value="Pending" <?= $row['status']=='Pending' ? 'selected' : '' ?>>Pending</option>
    <option value="Success" <?= $row['status']=='Success' ? 'selected' : '' ?>>Success</option>
    <option value="Cancelled" <?= $row['status']=='Cancelled' ? 'selected' : '' ?>>Cancelled</option>
</select>
</td>

<!-- TRACKING STATUS DROPDOWN -->
<td>
<select class="form-select form-select-sm status-dropdown"
    data-field="tracking_status"
    data-id="<?= $row['id'] ?>"
    data-original="<?= $row['tracking_status'] ?>"
    style="width:180px; font-weight:600;
    <?php
        $tr = $row['tracking_status'];
        if($tr=='Menunggu Pembayaran') echo 'background:#fff3cd; color:#856404; border-color:#ffc107;';
        elseif($tr=='Dikemas') echo 'background:#e2e3e5; color:#383d41; border-color:#d6d8db;';
        elseif($tr=='Dikirim') echo 'background:#cce5ff; color:#004085; border-color:#b8daff;';
        elseif($tr=='Selesai') echo 'background:#d1e7dd; color:#0f5132; border-color:#198754;';
        else echo 'background:#f8d7da; color:#842029; border-color:#dc3545;';
    ?>">
    <option value="Menunggu Pembayaran" <?= $row['tracking_status']=='Menunggu Pembayaran' ? 'selected' : '' ?>>Menunggu Pembayaran</option>
    <option value="Dikemas" <?= $row['tracking_status']=='Dikemas' ? 'selected' : '' ?>>Dikemas</option>
    <option value="Dikirim" <?= $row['tracking_status']=='Dikirim' ? 'selected' : '' ?>>Dikirim</option>
    <option value="Selesai" <?= $row['tracking_status']=='Selesai' ? 'selected' : '' ?>>Selesai</option>
    <option value="Dibatalkan" <?= $row['tracking_status']=='Dibatalkan' ? 'selected' : '' ?>>Dibatalkan</option>
</select>
</td>

<td>
<button class="btn btn-view" data-bs-toggle="modal" data-bs-target="#receipt<?= $row['id'] ?>">
Receipt
</button>
</td>
</tr>

<!-- MODAL PROOF -->
<div class="modal fade" id="proof<?= $row['id'] ?>">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content">

<div class="modal-header">
<h5>Proof of Payment</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body text-center">
<img src="../admin/uploads/<?= $row['payment_proof'] ?>" width="100%">
</div>

</div>
</div>
</div>

<!-- MODAL RECEIPT -->
<div class="modal fade" id="receipt<?= $row['id'] ?>">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content">

<div class="modal-header">
<h5>Receipt</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">

<p>ID: <?= $row['id'] ?></p>
<p>Customer: <?= $row['customer_name'] ?></p>
<p>Payment: <?= $row['category'] ?></p>
<p>Price: Rp <?= number_format($row['price'],0,',','.') ?></p>
<p>Payment Status: <?= ucfirst($row['status']) ?></p>
<p>Tracking Status: <?= $row['tracking_status'] ?></p>
<p>Date: <?= date('d-m-Y H:i', strtotime($row['created_at'])) ?></p>

</div>

</div>
</div>
</div>

<?php endwhile; ?>

</tbody>

</table>

</div>

<!-- HIDDEN FORM UNTUK UPDATE STATUS -->
<form method="POST" id="statusForm" style="display:none;">
    <input type="hidden" name="update_field" id="formUpdateField">
    <input type="hidden" name="order_id" id="formOrderId">
    <input type="hidden" name="new_status" id="formNewStatus">
</form>

<!-- SWEETALERT SCRIPT -->
<script>
document.querySelectorAll('.status-dropdown').forEach(select => {
    select.addEventListener('change', function(){
        const orderId = this.dataset.id;
        const fieldName = this.dataset.field; // status atau tracking_status
        const originalStatus = this.dataset.original;
        const newStatus = this.value;
        const dropdown = this;

        let tipe = fieldName === 'status' ? 'Pembayaran' : 'Pelacakan Pengiriman';
        let text = 'Ubah status ' + tipe + ' menjadi "' + newStatus + '"?';

        Swal.fire({
            title: 'Konfirmasi',
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, ubah!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if(result.isConfirmed){
                document.getElementById('formUpdateField').value = fieldName;
                document.getElementById('formOrderId').value = orderId;
                document.getElementById('formNewStatus').value = newStatus;
                document.getElementById('statusForm').submit();
            } else {
                // Kembalikan ke value semula jika batal
                dropdown.value = originalStatus;
            }
        });
    });
});

// NOTIFIKASI SETELAH UPDATE
<?php if(isset($_GET['msg'])): ?>
Swal.fire({
    icon: 'success',
    title: 'Berhasil!',
    text: 'Status berhasil diperbarui!',
    timer: 2000,
    showConfirmButton: false
});
<?php endif; ?>
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
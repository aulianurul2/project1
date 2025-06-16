<?php
include '../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pesan = intval($_POST['id_pesan']);
    $status = $_POST['status'];

    if ($status == 'success') {
        mysqli_query($conn, "
            UPDATE pembayaran 
            SET status_pembayaran = 'success'
            WHERE id_pesan = $id_pesan
        ");
        echo "Status pembayaran diupdate.";
    } else {
        echo "Status tidak dikenali.";
    }
}

<?php
session_start();
include '../koneksi.php';

if (isset($_POST['update'])) {
    $id_pesan = mysqli_real_escape_string($conn, $_POST['id_pesan']);
    $status_baru = mysqli_real_escape_string($conn, $_POST['status_baru']);

    // Hanya izinkan status 'diproses'
    $allowed_status = ['diproses'];
    if (!in_array($status_baru, $allowed_status)) {
        $_SESSION['error'] = "Status tidak valid.";
        header('Location: pemesanan.php');
        exit;
    }

    // Ambil nomor meja yang terkait dengan pemesanan
    $query_no_meja = "SELECT DISTINCT no_meja FROM detail_pesanan WHERE id_pesan = '$id_pesan'";
    $result_no_meja = mysqli_query($conn, $query_no_meja);

    $no_meja_list = [];
    while ($row = mysqli_fetch_assoc($result_no_meja)) {
        $no_meja_list[] = $row['no_meja'];
    }

    if (count($no_meja_list) === 0) {
        $_SESSION['error'] = "Nomor meja tidak ditemukan.";
        header('Location: pemesanan.php');
        exit;
    }

    $no_meja_str = "'" . implode("','", $no_meja_list) . "'";

    // Hapus pesanan lama yang masih 'diproses' di meja yang sama
    $query_hapus_lama = "DELETE FROM pemesanan WHERE status = 'diproses' AND id_pesan != '$id_pesan' AND id_pesan IN (
        SELECT id_pesan FROM detail_pesanan WHERE no_meja IN ($no_meja_str)
    )";
    mysqli_query($conn, $query_hapus_lama);

    // Update status dan catat waktu proses
    $waktu_diproses = date('Y-m-d H:i:s');
    $query_update = "UPDATE pemesanan SET status = 'diproses', waktu_diproses = '$waktu_diproses' WHERE id_pesan = '$id_pesan'";

    if (mysqli_query($conn, $query_update)) {
        $_SESSION['success'] = "Pesanan diproses.";
        header("Location: status.php?id_pesan=$id_pesan");
        exit;
    } else {
        $_SESSION['error'] = "Gagal update status: " . mysqli_error($conn);
        header('Location: pemesanan.php');
        exit;
    }
} else {
    $_SESSION['error'] = "Akses tidak valid.";
    header('Location: pemesanan.php');
    exit;
}

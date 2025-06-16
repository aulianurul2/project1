<?php
session_start();
include '../koneksi.php';

if (isset($_POST['update'])) {
    $id_pesan = mysqli_real_escape_string($conn, $_POST['id_pesan']);
    $status_baru = mysqli_real_escape_string($conn, $_POST['status_baru']);

    // Izinkan status tertentu
    $allowed_status = ['diproses', 'selesai', 'selesai_keluar', 'dibatalkan'];
    if (!in_array($status_baru, $allowed_status)) {
        $_SESSION['error'] = "Status tidak valid.";
        header('Location: pemesanan.php');
        exit;
    }

    // Ambil nomor meja terkait (jika diperlukan untuk validasi/operasi lain)
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

    // Jika status baru adalah "diproses", hapus yang lain di meja sama yang sedang 'diproses'
    if ($status_baru === 'diproses') {
        $no_meja_str = "'" . implode("','", $no_meja_list) . "'";
        $query_hapus_lama = "DELETE FROM pemesanan 
            WHERE status = 'diproses' 
            AND id_pesan != '$id_pesan' 
            AND id_pesan IN (
                SELECT id_pesan FROM detail_pesanan WHERE no_meja IN ($no_meja_str)
            )";
        mysqli_query($conn, $query_hapus_lama);
    }

    // Update status dan catat waktu_diproses jika status = diproses
    $update_fields = "status = '$status_baru'";
    if ($status_baru === 'diproses') {
        $waktu_diproses = date('Y-m-d H:i:s');
        $update_fields .= ", waktu_diproses = '$waktu_diproses'";
    }

    $query_update = "UPDATE pemesanan SET $update_fields WHERE id_pesan = '$id_pesan'";

    if (mysqli_query($conn, $query_update)) {
        $_SESSION['success'] = "Status pemesanan berhasil diupdate menjadi '$status_baru'.";
        header("Location: pemesanan.php?id_pesan=$id_pesan");
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

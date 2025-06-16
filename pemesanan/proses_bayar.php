<?php
session_start();
include '../koneksi.php';

if (!isset($_GET['id_pesan'])) {
    $_SESSION['error'] = "ID tidak ditemukan.";
    header('Location: pemesanan.php');
    exit;
}

$id_pesan = mysqli_real_escape_string($conn, $_GET['id_pesan']);

mysqli_begin_transaction($conn);
try {
    // Update status pembayaran dan status selesai_keluar
    $q1 = "UPDATE pemesanan SET bayar = 'sudah', status = 'selesai_keluar' WHERE id_pesan = '$id_pesan'";
    if (!mysqli_query($conn, $q1)) throw new Exception("Gagal update pesanan.");

    // Ambil semua no_meja dari detail
    $q2 = mysqli_query($conn, "SELECT DISTINCT no_meja FROM detail_pesanan WHERE id_pesan = '$id_pesan'");
    $meja = [];
    while ($row = mysqli_fetch_assoc($q2)) $meja[] = $row['no_meja'];

    if ($meja) {
        $meja_str = "'" . implode("','", $meja) . "'";
        $q3 = "UPDATE meja SET status = 'kosong' WHERE nomor_meja IN ($meja_str)";
        if (!mysqli_query($conn, $q3)) throw new Exception("Gagal update meja.");
    }

    mysqli_commit($conn);
    header("Location: cetak_struk.php?id_pesan=$id_pesan"); // arahkan ke struk
    exit;
} catch (Exception $e) {
    mysqli_rollback($conn);
    $_SESSION['error'] = $e->getMessage();
    header('Location: pemesanan.php');
    exit;
}

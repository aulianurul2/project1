<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../login.php");
    exit();
}

include "../koneksi.php";

if (isset($_GET['id'])) {
    $id_meja = intval($_GET['id']);

    // Ambil nama file barcode untuk dihapus
    $result = mysqli_query($conn, "SELECT barcode_image FROM meja WHERE id_meja = $id_meja");
    $data = mysqli_fetch_assoc($result);
    $barcode_name = $data['barcode_image'];

    $target_dir = "../uploads/barcode/";

    // Hapus file barcode jika ada
    if ($barcode_name && file_exists($target_dir . $barcode_name)) {
        unlink($target_dir . $barcode_name);
    }

    // Hapus data meja dari database
    $query = "DELETE FROM meja WHERE id_meja = $id_meja";
    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Meja berhasil dihapus.";
    } else {
        $_SESSION['error'] = "Gagal menghapus meja: " . mysqli_error($conn);
    }
} else {
    $_SESSION['error'] = "ID meja tidak ditemukan.";
}

header("Location: meja.php");
exit();

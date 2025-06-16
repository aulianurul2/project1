<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include "../koneksi.php";

// Ambil ID dari URL
$id = $_GET['id'];

// Hapus gambar terkait
$get = mysqli_query($conn, "SELECT gambar FROM menu WHERE id_menu = $id");
$data = mysqli_fetch_assoc($get);
$gambar = $data['gambar'];
if (file_exists("/kasir/assets/img/uploads/" . $gambar)) {
    unlink("/kasir/assets/img/uploads/" . $gambar);
}

// Hapus dari database
mysqli_query($conn, "DELETE FROM menu WHERE id_menu = $id");

header("Location: index.php");
?>

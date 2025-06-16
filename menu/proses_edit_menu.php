<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../login.php");
    exit();
}

include "../koneksi.php";

$id = $_POST['id'];
$nama = $_POST['nama_produk'];
$harga = $_POST['harga'];
$stok = $_POST['stok'];
$kategori = $_POST['kategori'];

$gambar_baru = $_FILES['gambar']['name'];
$tmp = $_FILES['gambar']['tmp_name'];

if ($gambar_baru) {
    // Hapus gambar lama
    $lama = mysqli_fetch_assoc(mysqli_query($conn, "SELECT gambar FROM menu WHERE id_menu = $id"));
    if (file_exists("../uploads/menu/" . $lama['gambar'])) {
        unlink("../uploads/menu/" . $lama['gambar']);
    }

    // Upload gambar baru
    move_uploaded_file($tmp, "../uploads/menu/" . $gambar_baru);

    // Update dengan gambar baru
    $sql = "UPDATE menu SET nama_produk=?, harga=?, stok=?, kategori=?, gambar=? WHERE id_menu=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdissi", $nama, $harga, $stok, $kategori, $gambar_baru, $id);
} else {
    // Update tanpa gambar
    $sql = "UPDATE menu SET nama_produk=?, harga=?, stok=?, kategori=? WHERE id_menu=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdisi", $nama, $harga, $stok, $kategori, $id);
}


if ($stmt->execute()) {
    header("Location: index.php?page=menu");
    exit();
} else {
    echo "Gagal update: " . $stmt->error;
}
?>

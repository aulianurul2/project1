<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../../login.php");
    exit();
}

include "../koneksi.php";

// Ambil data dari form
$nama_produk = $_POST['nama_produk'] ?? '';
$harga       = $_POST['harga'] ?? 0;
$stok        = $_POST['stok'] ?? 0;
$kategori    = $_POST['kategori'] ?? '';

// Tentukan folder upload dengan path absolut
$folder_upload = realpath(__DIR__ . '/../uploads/menu/');

// Jika folder upload belum ada, buat
if ($folder_upload === false) {
    $create_path = __DIR__ . '/../uploads/menu/';
    if (!mkdir($create_path, 0777, true)) {
        die("Gagal membuat folder upload di: $create_path");
    }
    $folder_upload = realpath($create_path);
}

// Cek apakah folder bisa ditulis
if (!is_writable($folder_upload)) {
    die("Folder upload tidak dapat ditulis: $folder_upload");
}

// Proses upload gambar
if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
    $nama_file = $_FILES['gambar']['name'];
    $tmp_file  = $_FILES['gambar']['tmp_name'];
    $ukuran_file = $_FILES['gambar']['size'];

    // Validasi ukuran maksimum 8MB
    if ($ukuran_file > 8 * 1024 * 1024) {
        echo "<script>alert('Ukuran gambar terlalu besar. Maksimum 8MB!'); window.location.href='index.php';</script>";
        exit();
    }

    // Validasi ekstensi gambar
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
    $ext = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed_extensions)) {
        echo "<script>alert('Format gambar tidak didukung!'); window.location.href='index.php';</script>";
        exit();
    }

    // Rename file agar unik
    $nama_baru = uniqid('menu_') . '.' . $ext;
    $path_file = $folder_upload . DIRECTORY_SEPARATOR . $nama_baru;

    // Pindahkan file ke folder upload
    if (move_uploaded_file($tmp_file, $path_file)) {
        // Simpan data ke database
        $nama_produk = mysqli_real_escape_string($conn, $nama_produk);
        $kategori = mysqli_real_escape_string($conn, $kategori);

        $query = "INSERT INTO menu (nama_produk, harga, stok, kategori, gambar)
                  VALUES ('$nama_produk', $harga, $stok, '$kategori', '$nama_baru')";

        if (mysqli_query($conn, $query)) {
            header("Location: index.php?success=tambah");
            exit();
        } else {
            echo "<script>alert('Gagal menyimpan ke database.'); window.location.href='index.php';</script>";
            exit();
        }
    } else {
        echo "<script>alert('Gagal mengupload gambar.'); window.location.href='index.php';</script>";
        exit();
    }
} else {
    echo "<script>alert('Tidak ada file yang diupload atau terjadi kesalahan upload.'); window.location.href='index.php';</script>";
    exit();
}
?>

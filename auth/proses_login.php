<?php
session_start();
include "../koneksi.php";

// Validasi koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Ambil data dari form
$username = trim($_POST['username'] ?? '');
$no_meja = (int)($_POST['no_meja'] ?? 0);

// Validasi
if ($username === '' || $no_meja <= 0) {
    echo "<script>alert('Nama dan Nomor Meja wajib diisi!'); window.location.href = 'login_pelanggan.php';</script>";
    exit;
}

// Simpan ke database
$sql = "INSERT INTO user (username, no_meja, role_) VALUES (?, ?, 'pelanggan')";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("si", $username, $no_meja);
    if ($stmt->execute()) {
        $_SESSION['pelanggan'] = [
            'id_user' => $stmt->insert_id,
            'username' => $username,
            'no_meja' => $no_meja,
        ];

        header("Location: ../index.php");
        exit;
    } else {
        echo "Gagal menyimpan data: " . $stmt->error;
    }
    $stmt->close();
} else {
    echo "Query gagal dipersiapkan: " . $conn->error;
}

$conn->close();
?>

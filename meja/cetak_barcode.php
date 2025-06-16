<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../../login.php");
    exit();
}

include "../koneksi.php";

if (!isset($_GET['id'])) {
    die("ID meja tidak ditemukan.");
}

$id_meja = intval($_GET['id']);
$query = "SELECT * FROM meja WHERE id_meja = $id_meja LIMIT 1";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) === 0) {
    die("Data meja tidak ditemukan.");
}

$meja = mysqli_fetch_assoc($result);
$nomor_meja = $meja['nomor_meja'];
$barcode_image = $meja['barcode_image'];

// Cek apakah file barcode tersedia
$barcode_path = "../uploads/barcode/" . $barcode_image;
if (!file_exists($barcode_path)) {
    die("QR Code belum dibuat untuk meja ini.");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak QR Meja <?= htmlspecialchars($nomor_meja) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding-top: 100px;
        }
        .qr-box {
            display: inline-block;
            padding: 20px;
            border: 2px dashed #999;
            border-radius: 10px;
        }
        .qr-label {
            font-size: 20px;
            margin-top: 10px;
        }
        @media print {
            button {
                display: none;
            }
        }
        .kembali {
        display: inline-block;
        background: blue;
        color: #fff;
        padding: 12px 30px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 50;
        transition: background-color 0.3s ease;
        margin: 0 auto;
        display: block;
        width: fit-content;
    }

    </style>
</head>
<body>

<div class="qr-box">
    <h2>QR Code Meja <?= htmlspecialchars($nomor_meja) ?></h2>
    <img src="<?= $barcode_path ?>" alt="QR Code Meja <?= htmlspecialchars($nomor_meja) ?>" width="200"><br>
    <div class="qr-label">Nomor Meja: <?= htmlspecialchars($nomor_meja) ?></div>
</div>

<br><br>
<button onclick="window.print()">🖨️ Cetak QR</button>
<a class="kembali" href="meja.php">Kembali ke Menu</a>
</body>
</html>

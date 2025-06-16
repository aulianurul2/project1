<?php
session_start();

if (!isset($_SESSION['last_id_pesanan'])) {
    // Kalau tidak ada ID pesanan, kembali ke menu
    header("Location: pilih_menu.php");
    exit;
}

$id_pesanan = $_SESSION['last_id_pesanan'];
unset($_SESSION['last_id_pesanan']); // Opsional, supaya nggak dobel redirect

?>

<!DOCTYPE html>
<html>
<head>
    <title>Checkout Berhasil</title>
    <style>
        body {
            background-color: #f7f7f7;
            font-family: Arial, sans-serif;
            text-align: center;
            padding-top: 80px;
        }
        .box {
            background-color: white;
            padding: 40px;
            border-radius: 15px;
            display: inline-block;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
        }
        a.button {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 25px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }
        a.button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="box">
        <h1>✅ Pesanan Berhasil!</h1>
        <p>Terima kasih, pesanan Anda sedang diproses oleh dapur sakti kami 🍜</p>
        <a class="button" href="detail_pesanan.php?id_pesan=<?= $id_pesanan ?>">Lihat Detail Pesanan</a>
    </div>
</body>
</html>

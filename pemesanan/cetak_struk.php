<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Pemesanan</title>
    <style>
        body {
            font-family: monospace;
            width: 280px;
            margin: auto;
            padding: 10px;
        }
        h2, .text-center {
            text-align: center;
        }
        .logo {
            display: block;
            margin: 0 auto 5px;
            max-width: 100px; /* Logo tidak melebihi lebar ini */
            max-height: 80px; /* Tinggi maksimum */
        }
        .line {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }
        .total {
            font-weight: bold;
            font-size: 1.1em;
        }
        .footer {
            text-align: center;
            margin-top: 10px;
            font-size: 12px;
        }
    </style>
    <script>
        window.onload = function () {
            window.print();
            setTimeout(() => {
                window.location.href = 'pemesanan.php';
            }, 1000);
        };
    </script>
</head>
<body>

<?php
include '../koneksi.php';
session_start();

if (!isset($_GET['id_pesan'])) {
    echo "ID pesanan tidak ditemukan.";
    exit;
}

$id_pesan = mysqli_real_escape_string($conn, $_GET['id_pesan']);

$query = "
    SELECT 
        p.id_pesan,
        p.tanggal,
        p.status,
        GROUP_CONCAT(CONCAT(dp.produk, ' x', dp.jml_beli) SEPARATOR '\n') AS produk_all,
        SUM(dp.jml_beli * dp.harga_satuan) AS total_harga,
        GROUP_CONCAT(DISTINCT dp.no_meja ORDER BY dp.no_meja SEPARATOR ', ') AS no_meja_all
    FROM pemesanan p
    JOIN detail_pesanan dp ON p.id_pesan = dp.id_pesan
    WHERE p.id_pesan = '$id_pesan'
    GROUP BY p.id_pesan
";

$result = mysqli_query($conn, $query);
if (!$result || mysqli_num_rows($result) === 0) {
    echo "Data pesanan tidak ditemukan.";
    exit;
}

$data = mysqli_fetch_assoc($result);
$kasir = isset($_SESSION['nama_admin']) ? $_SESSION['nama_admin'] : 'Admin';
$metode_pembayaran = 'Tunai';
?>

<h2>Struk Pemesanan</h2>
<img src="/projek1v/assets/img/logof.jpg" alt="Logo Terrace A" class="logo">
<div class="text-center">Terrace A</div>
<div class="text-center">====================</div>
<p><strong>ID:</strong> <?= htmlspecialchars($data['id_pesan']) ?></p>
<p><strong>Tanggal:</strong> <?= htmlspecialchars($data['tanggal']) ?></p>
<p><strong>Meja:</strong> <?= htmlspecialchars($data['no_meja_all']) ?></p>
<div class="line"></div>
<p><strong>Pesanan:</strong></p>
<pre><?= htmlspecialchars($data['produk_all']) ?></pre>
<div class="line"></div>
<p class="total">Total: Rp <?= number_format($data['total_harga'], 0, ',', '.') ?></p>

<p><strong>Kasir:</strong> <?= htmlspecialchars($kasir) ?></p>
<div class="line"></div>
<p class="footer">Terima kasih telah berkunjung!</p>

</body>
</html>

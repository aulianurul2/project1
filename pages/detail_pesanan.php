<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['no_meja'])) {
    echo "<script>
        alert('Nomor meja belum diset. Silakan kembali ke halaman awal.');
        window.location.href = '../index.php';
    </script>";
    exit;
}

$no_meja = $_SESSION['no_meja'];

$query = "SELECT dp.id_pesan, dp.tgl_pesan, dp.jam_pesan, p.status 
          FROM detail_pesanan dp
          JOIN pemesanan p ON dp.id_pesan = p.id_pesan
          WHERE dp.no_meja = '$no_meja' AND p.no_meja = '$no_meja'
          AND p.status NOT IN ('selesai_keluar', 'batal')
          GROUP BY dp.id_pesan
          ORDER BY dp.tgl_pesan ASC, dp.jam_pesan ASC";

$result = mysqli_query($conn, $query);
if (!$result) {
    die("Query gagal: " . mysqli_error($conn));
}

if (mysqli_num_rows($result) == 0) {
    echo "<script>
        alert('Belum ada pesanan aktif. Silakan kembali pesan ke halaman awal.');
        window.location.href = '../index.php';
    </script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan Meja <?= htmlspecialchars($no_meja) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="/projek1/assets/css/style.css" />
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        body {
            font-family: 'Poppins', Arial, sans-serif;
            background-color: #fefefe;
            color: #333;
            margin: 100px auto 50px; 
            max-width: 900px;
            padding: 0 20px;
        }
        h2 {
            color: #5b3a29;
            text-align: center;
            margin-bottom: 5px;
        }
        .meta {
            text-align: center;
            color: #777;
            font-size: 0.9rem;
            margin-bottom: 20px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgb(0 0 0 / 0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            padding: 12px 15px;
            border: 1px solid #ddd;
            text-align: center;
            font-size: 1rem;
        }
        th {
            background-color: #5b3a29;
            color: #fff;
            font-weight: 600;
        }
        .info-pembayaran {
            border-radius: 6px;
            padding: 15px 20px;
            font-weight: 600;
            text-align: center;
            margin-top: 60px;
            margin-bottom: 30px;
        }
        .info-diproses {
            background-color: #e6f0ff;
            border: 1px solid #3399ff;
            color: #00529B;
            box-shadow: 0 2px 6px rgba(51, 153, 255, 0.3);
        }
        .info-selesai {
            background-color: #eaffea;
            border: 1px solid #2ecc71;
            color: #1d8f4d;
            box-shadow: 0 2px 6px rgba(46, 204, 113, 0.3);
        }
        .info-menunggu {
            background-color: #fff4e6;
            border: 1px solid #ffcc00;
            color: #a85b00;
            box-shadow: 0 2px 6px rgba(255, 204, 0, 0.3);
        }
        .kembali {
            display: inline-block;
            background: #000;
            color: #fff;
            padding: 12px 30px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            transition: background-color 0.3s ease;
            margin: 0 auto;
            display: block;
            width: fit-content;
        }
        .kembali:hover {
            background: #333;
        }
        .pesanan-container {
            margin-bottom: 50px;
            border-bottom: 2px solid #ddd;
            padding-bottom: 30px;
        }
    </style>
</head>
<body>

<?php include('layout/navbar.php'); ?>

<h2>Detail Semua Pesanan Aktif Meja <?= htmlspecialchars($no_meja) ?></h2>
<?php include('../pemesanan/status.php'); ?>
<?php
$alert_diproses_shown = false;
$alert_selesai_shown = false;
$alert_menunggu_shown = false;

while ($data = mysqli_fetch_assoc($result)) {
    $id_pesan = $data['id_pesan'];
    $status = $data['status'];

    if ($status == 'diproses' && !$alert_diproses_shown) {
        echo "<p class='info-pembayaran info-diproses'>🕒 <strong>Informasi:</strong> Pesanan Anda sedang diproses oleh dapur. Mohon tunggu sebentar.</p>";
        $alert_diproses_shown = true;
    } elseif ($status == 'menunggu pembayaran' && !$alert_menunggu_shown) {
        echo "<p class='info-pembayaran info-menunggu'>💳 <strong>Informasi:</strong> Silakan segera lakukan pembayaran di kasir untuk menyelesaikan pesanan Anda.</p>";
        $alert_menunggu_shown = true;
    }

    echo '<div class="pesanan-container">';
    echo '<h3>Pesanan ID: ' . htmlspecialchars($id_pesan) . '</h3>';
    echo '<p class="meta">Tanggal: ' . htmlspecialchars($data['tgl_pesan']) . ' | Jam: ' . htmlspecialchars($data['jam_pesan']) . ' | Status: ' . htmlspecialchars($status) . '</p>';

    $query_detail = "SELECT m.nama_produk, dp.jml_beli AS jumlah, dp.harga_satuan
                     FROM detail_pesanan dp
                     JOIN menu m ON dp.id_menu = m.id_menu
                     WHERE dp.id_pesan = $id_pesan AND dp.no_meja = '$no_meja'";

    $result_detail = mysqli_query($conn, $query_detail);

    if (!$result_detail) {
        echo "<p>Error ambil detail pesanan: " . mysqli_error($conn) . "</p>";
        continue;
    }

    echo '<table>
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Jumlah</th>
                    <th>Harga Satuan</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>';

    $total_akhir = 0;
    while ($row = mysqli_fetch_assoc($result_detail)) {
        $subtotal = $row['jumlah'] * $row['harga_satuan'];
        $total_akhir += $subtotal;
        echo '<tr>
                <td>' . htmlspecialchars($row['nama_produk']) . '</td>
                <td>' . htmlspecialchars($row['jumlah']) . '</td>
                <td>Rp ' . number_format($row['harga_satuan'], 0, ',', '.') . '</td>
                <td>Rp ' . number_format($subtotal, 0, ',', '.') . '</td>
              </tr>';
    }

    echo '</tbody>
        <tfoot>
            <tr>
                <td colspan="3">Total Bayar</td>
                <td>Rp ' . number_format($total_akhir, 0, ',', '.') . '</td>
            </tr>
        </tfoot>
        </table>
        </div>';
}
?>

<a class="kembali" href="pilih_menu.php">⬅️ Kembali ke Menu</a>

</body>
</html>

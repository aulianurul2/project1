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
if (!$result || mysqli_num_rows($result) == 0) {
    unset($_SESSION['no_meja']);
    echo "<script>
        alert('Belum ada pesanan aktif. Silakan kembali pesan ke halaman awal.');
        window.location.href = '../index.php';
    </script>";
    exit;
}

$adaPesananAktif = false;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Detail Pesanan Meja <?= htmlspecialchars($no_meja) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../assets/css/style.css" />
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
        }
        .info-pembayaran {
            border-radius: 6px;
            padding: 15px 20px;
            font-weight: 600;
            text-align: center;
            margin-top: 30px;
            margin-bottom: 20px;
        }
        .info-diproses {
            background-color: #e6f0ff;
            border: 1px solid #3399ff;
            color: #00529B;
        }
        .info-menunggu {
            background-color: #fff4e6;
            border: 1px solid #ffcc00;
            color: #a85b00;
        }
        .timer-countdown {
            font-weight: 700;
            font-size: 1.3rem;
            color: #00529B;
            margin: 15px 0;
            text-align: center;
        }
        .kembali {
            display: block;
            width: fit-content;
            margin: 0 auto;
            background: #000;
            color: #fff;
            padding: 12px 30px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            transition: background-color 0.3s ease;
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

<h2>Detail Pesanan Meja <?= htmlspecialchars($no_meja) ?></h2>

<?php
while ($data = mysqli_fetch_assoc($result)) {
    $id_pesan = $data['id_pesan'];
    $status = $data['status'];

    // Cek apakah masih ada pesanan aktif
    if ($status == 'diproses' || $status == 'menunggu pembayaran') {
        $adaPesananAktif = true;
    }

    if ($status == 'diproses') {
        $wq = mysqli_query($conn, "SELECT waktu_diproses FROM pemesanan WHERE id_pesan = $id_pesan");
        $wData = mysqli_fetch_assoc($wq);
        $waktu_diproses = isset($wData['waktu_diproses']) ? strtotime($wData['waktu_diproses']) : time();
        $elapsed = time() - $waktu_diproses;

        echo "<p class='info-pembayaran info-diproses'>🕒 <strong>Informasi:</strong> Pesanan Anda sedang diproses oleh dapur. Mohon tunggu sebentar.</p>";
        echo "<div class='timer-countdown'>Estimasi waktu tersisa: <span id='timer-$id_pesan'></span></div>";

        echo "<script>
            let mulai$id_pesan = Date.now() - (" . ($elapsed * 1000) . ");
            let durasi$id_pesan = 20 * 60;

            function updateTimer$id_pesan() {
                let now = Date.now();
                let selisih = Math.floor((now - mulai$id_pesan) / 1000);
                let sisa = durasi$id_pesan - selisih;
                let output = '';

                if (sisa >= 0) {
                    let m = Math.floor(sisa / 60);
                    let d = sisa % 60;
                    if (d < 10) d = '0' + d;
                    output = m + ':' + d;
                } else {
                    let l = -sisa;
                    let m = Math.floor(l / 60);
                    let d = l % 60;
                    if (d < 10) d = '0' + d;
                    output = 'Lewat ' + m + ':' + d;
                }

                document.getElementById('timer-$id_pesan').textContent = output;

                if (selisih % 60 === 0 && sisa < 0) {
                    fetch('../controller/cek_status_pesanan.php?id_pesan=$id_pesan')
                    .then(res => res.json())
                    .then(data => {
                        if (data.status !== 'diproses') {
                            location.reload();
                        }
                    });
                }
            }

            setInterval(updateTimer$id_pesan, 1000);
            updateTimer$id_pesan();
        </script>";
    } elseif ($status == 'menunggu pembayaran') {
        echo "<p class='info-pembayaran info-menunggu'>💳 <strong>Informasi:</strong> Silakan segera lakukan pembayaran di kasir.</p>";
    }

    echo '<div class="pesanan-container">';
    echo '<h3>Pesanan ID: ' . htmlspecialchars($id_pesan) . '</h3>';
    echo '<p class="meta">Tanggal: ' . htmlspecialchars($data['tgl_pesan']) . ' | Jam: ' . htmlspecialchars($data['jam_pesan']) . ' | Status: ' . htmlspecialchars($status) . '</p>';

    $qDetail = "SELECT m.nama_produk, dp.jml_beli AS jumlah, dp.harga_satuan
                FROM detail_pesanan dp
                JOIN menu m ON dp.id_menu = m.id_menu
                WHERE dp.id_pesan = $id_pesan AND dp.no_meja = '$no_meja'";
    $rDetail = mysqli_query($conn, $qDetail);

    echo '<table><thead><tr><th>Produk</th><th>Jumlah</th><th>Harga Satuan</th><th>Subtotal</th></tr></thead><tbody>';

    $total = 0;
    while ($row = mysqli_fetch_assoc($rDetail)) {
        $sub = $row['jumlah'] * $row['harga_satuan'];
        $total += $sub;
        echo "<tr>
                <td>" . htmlspecialchars($row['nama_produk']) . "</td>
                <td>" . $row['jumlah'] . "</td>
                <td>Rp " . number_format($row['harga_satuan'], 0, ',', '.') . "</td>
                <td>Rp " . number_format($sub, 0, ',', '.') . "</td>
            </tr>";
    }

    echo "</tbody><tfoot><tr><td colspan='3'>Total Bayar</td><td>Rp " . number_format($total, 0, ',', '.') . "</td></tr></tfoot></table>";
    echo '</div>';
}
?>

<?php
if (!$adaPesananAktif) {
    unset($_SESSION['no_meja']);
    echo "<script>
        alert('Pesanan Anda sudah selesai. Terima kasih!');
        window.location.href = '../index.php';
    </script>";
    exit;
}
?>

<a class="kembali" href="pilih_menu.php">⬅️ Kembali ke Menu</a>

</body>
</html>

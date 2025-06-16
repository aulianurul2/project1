<?php
session_start();

// koneksi database
$host = "localhost";
$user = "root";
$pass = "";
$db   = "ui_project2";

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

$bulan_tes = [
    '01' => "Januari", '02' => "Februari", '03' => "Maret",
    '04' => "April",   '05' => "Mei",      '06' => "Juni",
    '07' => "Juli",    '08' => "Agustus",  '09' => "September",
    '10' => "Oktober", '11' => "November", '12' => "Desember"
];

$bulan = date('m');
$tahun = date('Y');
$tanggal = '';

if (isset($_POST['bln']) && isset($_POST['thn'])) {
    $bulan = $_POST['bln'];
    $tahun = $_POST['thn'];
    $tanggal = $_POST['tgl'] ?? '';
}

$where = "MONTH(dp.tgl_pesan) = '$bulan' AND YEAR(dp.tgl_pesan) = '$tahun'";
if (!empty($tanggal) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
    $where .= " AND DATE(dp.tgl_pesan) = '$tanggal'";
}

$sql = "SELECT dp.id_detail, dp.produk, dp.jml_beli, dp.harga_satuan, dp.no_meja, dp.tgl_pesan, dp.jam_pesan, p.status 
        FROM detail_pesanan dp 
        LEFT JOIN pemesanan p ON dp.id_pesan = p.id_pesan 
        WHERE $where AND p.status = 'selesai_keluar' 
        ORDER BY dp.tgl_pesan DESC";

$query = mysqli_query($conn, $sql);

// Export ke Excel
if (isset($_POST['export_excel'])) {
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=laporan_pemesanan_{$bulan}_{$tahun}.xls");

    echo "Laporan Pemesanan Bulan " . $bulan_tes[$bulan] . " " . $tahun;
    if (!empty($tanggal)) echo " Tanggal $tanggal";
    echo "\n\n";

    echo "No\tProduk\tJumlah\tHarga Satuan\tNo Meja\tTanggal\tJam\tStatus\n";
    $no = 1;
    while ($row = mysqli_fetch_assoc($query)) {
        echo $no++ . "\t" .
             $row['produk'] . "\t" .
             $row['jml_beli'] . "\t" .
             $row['harga_satuan'] . "\t" .
             $row['no_meja'] . "\t" .
             "=" . $row['tgl_pesan'] . "\t" .
             $row['jam_pesan'] . "\t" .
             $row['status'] . "\n";
    }
    exit();
}

// Statistik
$sql_total = "SELECT COUNT(DISTINCT p.id_pesan) AS total_pemesanan, 
                     SUM(dp.jml_beli * dp.harga_satuan) AS total_pendapatan
              FROM detail_pesanan dp
              LEFT JOIN pemesanan p ON dp.id_pesan = p.id_pesan
              WHERE $where AND p.status = 'selesai_keluar'";

$result_total = mysqli_query($conn, $sql_total);
$row_total = mysqli_fetch_assoc($result_total);
$total_pemesanan = $row_total['total_pemesanan'] ?? 0;
$total_pendapatan = $row_total['total_pendapatan'] ?? 0;

// Chart data
$sql_chart = "SELECT dp.produk, 
                     SUM(dp.jml_beli) AS total_jual, 
                     SUM(dp.jml_beli * dp.harga_satuan) AS total_uang
              FROM detail_pesanan dp
              LEFT JOIN pemesanan p ON dp.id_pesan = p.id_pesan
              WHERE $where AND p.status = 'selesai_keluar'
              GROUP BY dp.produk
              ORDER BY total_jual DESC";

$result_chart = mysqli_query($conn, $sql_chart);

$produk_list = [];
$total_jual_list = [];
$total_uang_list = [];

while ($chart = mysqli_fetch_assoc($result_chart)) {
    $produk_list[] = $chart['produk'];
    $total_jual_list[] = $chart['total_jual'];
    $total_uang_list[] = $chart['total_uang'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Laporan Pemesanan</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,700,900" rel="stylesheet">
  <link href="../assets/css/sb-admin-2.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .card-stats {
      border-left: 4px solid #4e73df;
      padding: 10px 20px;
      margin-bottom: 20px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .card-stats h5 {
      font-weight: 700;
    }
    .card-stats .value {
      font-size: 24px;
      font-weight: 700;
      color: #1cc88a;
    }
  </style>
</head>
<body id="page-top">
<div id="wrapper">
<?php include '../layout/sidebar.php'; ?>
<div id="content-wrapper" class="d-flex flex-column">
<div id="content">
<?php include '../layout/navbar.php'; ?>
<div class="container-fluid">
  <h1 class="h3 mb-4 text-gray-800">Laporan Pemesanan Bulan <?= $bulan_tes[$bulan] ?> <?= $tahun ?></h1>

  <div class="row mb-4">
    <div class="col-md-6">
      <div class="card-stats bg-white">
        <h5>Total Pemesanan</h5>
        <div class="value"><?= number_format($total_pemesanan) ?></div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card-stats bg-white">
        <h5>Total Pendapatan</h5>
        <div class="value">Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></div>
      </div>
    </div>
  </div>

  <!-- Chart -->
  <div class="card mb-4">
    <div class="card-body">
      <canvas id="chartProduk" height="120"></canvas>
    </div>
  </div>

  <form method="post" class="mb-4">
    <div class="form-row">
      <div class="col-md-3">
        <select name="bln" class="form-control">
          <option value="">Pilih Bulan</option>
          <?php foreach ($bulan_tes as $key => $val): ?>
            <option value="<?= $key ?>" <?= $key == $bulan ? 'selected' : '' ?>><?= $val ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-2">
        <select name="thn" class="form-control">
          <option value="">Pilih Tahun</option>
          <?php for ($i = 2017; $i <= date('Y'); $i++): ?>
            <option value="<?= $i ?>" <?= $i == $tahun ? 'selected' : '' ?>><?= $i ?></option>
          <?php endfor; ?>
        </select>
      </div>
      <div class="col-md-3">
        <input type="date" name="tgl" class="form-control" value="<?= $tanggal ?>" />
      </div>
      <div class="col-md-2">
        <button type="submit" class="btn btn-primary btn-block">Tampilkan</button>
      </div>
      <div class="col-md-2">
        <button type="submit" name="export_excel" class="btn btn-success btn-block">Unduh Excel</button>
      </div>
    </div>
  </form>

  <!-- Tabel -->
  <div class="table-responsive">
    <table class="table table-bordered table-sm">
      <thead class="thead-light">
        <tr>
          <th>No</th>
          <th>Produk</th>
          <th>Jumlah</th>
          <th>Harga Satuan</th>
          <th>No Meja</th>
          <th>Tanggal</th>
          <th>Jam</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $no = 1;
        mysqli_data_seek($query, 0);
        if (mysqli_num_rows($query) > 0):
          while ($row = mysqli_fetch_assoc($query)):
        ?>
        <tr>
          <td><?= $no++ ?></td>
          <td><?= htmlspecialchars($row['produk']) ?></td>
          <td><?= $row['jml_beli'] ?></td>
          <td>Rp <?= number_format($row['harga_satuan'], 0, ',', '.') ?></td>
          <td><?= htmlspecialchars($row['no_meja']) ?></td>
          <td><?= $row['tgl_pesan'] ?></td>
          <td><?= $row['jam_pesan'] ?></td>
          <td><?= htmlspecialchars($row['status']) ?></td>
        </tr>
        <?php endwhile; else: ?>
        <tr><td colspan="8" class="text-center">Tidak ada data pemesanan.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const ctx = document.getElementById('chartProduk').getContext('2d');
  const chartProduk = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: <?= json_encode($produk_list) ?>,
      datasets: [
        {
          label: 'Jumlah Terjual',
          data: <?= json_encode($total_jual_list) ?>,
          backgroundColor: 'rgba(54, 162, 235, 0.6)',
          borderColor: 'rgba(54, 162, 235, 1)',
          borderWidth: 1
        },
        {
          label: 'Total Pendapatan',
          data: <?= json_encode($total_uang_list) ?>,
          backgroundColor: 'rgba(75, 192, 192, 0.6)',
          borderColor: 'rgba(75, 192, 192, 1)',
          borderWidth: 1,
          yAxisID: 'uang'
        }
      ]
    },
    options: {
      responsive: true,
      interaction: {
        mode: 'index',
        intersect: false
      },
      scales: {
        y: {
          beginAtZero: true,
          title: {
            display: true,
            text: 'Jumlah Terjual'
          }
        },
        uang: {
          beginAtZero: true,
          position: 'right',
          title: {
            display: true,
            text: 'Pendapatan (Rp)'
          },
          ticks: {
            callback: function(value) {
              return 'Rp ' + value.toLocaleString();
            }
          }
        }
      },
      plugins: {
        tooltip: {
          callbacks: {
            label: function(context) {
              let label = context.dataset.label || '';
              if (label) label += ': ';
              if (context.datasetIndex === 1) {
                label += 'Rp ' + context.parsed.y.toLocaleString();
              } else {
                label += context.parsed.y;
              }
              return label;
            }
          }
        }
      }
    }
  });
</script>
</body>
</html>

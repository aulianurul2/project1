<?php
// koneksi database
$host = "localhost";
$user = "root";
$pass = "";
$db   = "ui_project2";

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// ambil bulan dan tahun sekarang
$bulan = date('m');
$tahun = date('Y');

// ===============================
// Pemesanan Terbaru (10 data)
// ===============================
$sql_pemesanan_terbaru = "
    SELECT dp.id_detail, dp.produk, dp.jml_beli, dp.no_meja, dp.tgl_pesan, dp.jam_pesan, p.status 
    FROM detail_pesanan dp
    LEFT JOIN pemesanan p ON dp.id_pesan = p.id_pesan
    ORDER BY dp.tgl_pesan DESC, dp.jam_pesan DESC
    LIMIT 10
";
$res_pemesanan_terbaru = mysqli_query($conn, $sql_pemesanan_terbaru);
if (!$res_pemesanan_terbaru) {
    die("Query error: " . mysqli_error($conn));
}

// ===============================
// Produk dengan stok menipis (<=5)
// ===============================
$sql_produk_menipis = "SELECT nama_produk, stok FROM menu WHERE stok <= 5 ORDER BY stok ASC";
$res_produk_menipis = mysqli_query($conn, $sql_produk_menipis);

// ===============================
// Total Meja
// ===============================
$sql_total_meja = "SELECT COUNT(*) AS total_meja FROM meja";
$res_total_meja = mysqli_query($conn, $sql_total_meja);
$total_meja = 0;
if ($res_total_meja) {
    $data = mysqli_fetch_assoc($res_total_meja);
    $total_meja = $data['total_meja'] ?? 0;
}

// ===============================
// Meja Aktif bulan ini
// ===============================
$sql_meja_aktif = "
    SELECT COUNT(DISTINCT dp.no_meja) AS meja_aktif
    FROM detail_pesanan dp
    JOIN pemesanan p ON dp.id_pesan = p.id_pesan
    WHERE MONTH(dp.tgl_pesan) = '$bulan' 
      AND YEAR(dp.tgl_pesan) = '$tahun'
      AND p.status NOT IN ('selesai_keluar', 'batal')
";
$res_meja_aktif = mysqli_query($conn, $sql_meja_aktif);
$meja_aktif = 0;
if ($res_meja_aktif) {
    $data = mysqli_fetch_assoc($res_meja_aktif);
    $meja_aktif = $data['meja_aktif'] ?? 0;
}

// ===============================
// Pesanan Aktif bulan ini
// ===============================
$sql_pesanan_aktif = "
    SELECT COUNT(*) AS pesanan_aktif
    FROM pemesanan
    WHERE MONTH(tanggal) = '$bulan' 
      AND YEAR(tanggal) = '$tahun'
      AND status NOT IN ('selesai_keluar', 'batal')
";
$res_pesanan_aktif = mysqli_query($conn, $sql_pesanan_aktif);
$pesanan_aktif = 0;
if ($res_pesanan_aktif) {
    $data = mysqli_fetch_assoc($res_pesanan_aktif);
    $pesanan_aktif = $data['pesanan_aktif'] ?? 0;
} else {
    echo "Query error (pesanan aktif): " . mysqli_error($conn);
}

// ===============================
// Total Menu
// ===============================
$sql_total_menu = "SELECT COUNT(*) AS total_menu FROM menu";
$res_total_menu = mysqli_query($conn, $sql_total_menu);
$total_menu = 0;
if ($res_total_menu) {
    $data = mysqli_fetch_assoc($res_total_menu);
    $total_menu = $data['total_menu'] ?? 0;
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,700,900" rel="stylesheet" />
    <link href="../assets/css/sb-admin-2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
    <style>
      .card-stats {
          border-left: 4px solid #4e73df;
          padding: 10px 20px;
          margin-bottom: 20px;
          box-shadow: 0 0 10px rgba(0,0,0,0.1);
          background: #fff;
          border-radius: 4px;
      }
      .card-stats h5 {
          font-weight: 700;
          margin-bottom: 5px;
          color: #4e73df;
      }
      .card-stats .value {
          font-size: 28px;
          font-weight: 700;
          color: #1cc88a;
      }
      .card-stats.meja-aktif { border-color: #f6c23e; }
      .card-stats.pesanan-aktif { border-color: #36b9cc; }
      .card-stats.total-menu { border-color: #858796; }
    </style>
</head>
<body id="page-top">
    <div id="wrapper">
        <?php include '../layout/sidebar.php'; ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include '../layout/navbar.php'; ?>
                <div class="container-fluid">
                    <h1 class="mb-4">Dashboard</h1>

                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card-stats">
                                <h5>Total Meja</h5>
                                <div class="value"><?= number_format($total_meja) ?></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card-stats meja-aktif">
                                <h5>Meja Aktif</h5>
                                <div class="value"><?= number_format($meja_aktif) ?></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card-stats pesanan-aktif">
                                <h5>Pesanan Aktif</h5>
                                <div class="value"><?= number_format($pesanan_aktif) ?></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card-stats total-menu">
                                <h5>Total Menu</h5>
                                <div class="value"><?= number_format($total_menu) ?></div>
                            </div>
                        </div>
                    </div>

                  <!-- Container untuk tabel-tabel -->
<div class="container-fluid">
  
  <!-- Tabel Pesanan Terbaru -->
  <div class="card mb-4 shadow-sm">
    <div class="card-header bg-primary text-white">
      <h6 class="m-0 font-weight-bold">Pemesanan Terbaru (10 Data)</h6>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-bordered table-hover mb-0 text-center">
          <thead class="thead-dark">
            <tr>
              <th>No</th>
              <th>Produk</th>
              <th>Jumlah</th>
              <th>No Meja</th>
              <th>Tanggal</th>
              <th>Jam</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php
            if(isset($res_pemesanan_terbaru) && mysqli_num_rows($res_pemesanan_terbaru) > 0){
              $no = 1;
              while($row = mysqli_fetch_assoc($res_pemesanan_terbaru)){
                echo "<tr>";
                echo "<td>".$no++."</td>";
                echo "<td>".htmlspecialchars($row['produk'])."</td>";
                echo "<td>".$row['jml_beli']."</td>";
                echo "<td>".$row['no_meja']."</td>";
                echo "<td>".$row['tgl_pesan']."</td>";
                echo "<td>".$row['jam_pesan']."</td>";
                echo "<td>".$row['status']."</td>";
                echo "</tr>";
              }
            } else {
              echo "<tr><td colspan='7'>Tidak ada data pemesanan terbaru.</td></tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Tabel Produk Stok Menipis -->
  <div class="card mb-4 shadow-sm">
    <div class="card-header bg-danger text-white">
      <h6 class="m-0 font-weight-bold">Produk Stok Menipis (≤ 5)</h6>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-bordered table-sm mb-0 text-center">
          <thead class="thead-light">
            <tr>
              <th>No</th>
              <th>Nama Produk</th>
              <th>Stok</th>
            </tr>
          </thead>
          <tbody>
            <?php
            if ($res_produk_menipis && mysqli_num_rows($res_produk_menipis) > 0) {
                $no = 1;
                while ($row = mysqli_fetch_assoc($res_produk_menipis)) {
                    echo "<tr>";
                    echo "<td>{$no}</td>";
                    echo "<td>" . htmlspecialchars($row['nama_produk']) . "</td>";
                    echo "<td>{$row['stok']}</td>";
                    echo "</tr>";
                    $no++;
                }
            } else {
                echo "<tr><td colspan='3'>Tidak ada produk dengan stok menipis.</td></tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>


                </div>
            </div>
        </div>
    </div>
</body>
</html>

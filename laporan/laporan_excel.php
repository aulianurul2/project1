<?php
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Pemesanan.xls");

// Koneksi database
$host = "localhost";
$user = "root";
$pass = "";
$db   = "ui_project2";
$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Ambil bulan & tahun dari parameter GET
$bulan = $_GET['bln'] ?? date('m');
$tahun = $_GET['thn'] ?? date('Y');

// Query data
$sql = "SELECT dp.produk, dp.jml_beli, dp.harga_satuan, dp.no_meja, dp.tgl_pesan, dp.jam_pesan, p.status
        FROM detail_pesanan dp
        LEFT JOIN pemesanan p ON dp.id_pesan = p.id_pesan
        WHERE MONTH(dp.tgl_pesan) = '$bulan' AND YEAR(dp.tgl_pesan) = '$tahun'
          AND p.status = 'selesai_keluar'
        ORDER BY dp.tgl_pesan DESC";

$query = mysqli_query($conn, $sql);

// Header kolom
echo "No\tProduk\tJumlah\tHarga Satuan\tNo Meja\tTanggal\tJam\tStatus\n";

// Data
$no = 1;
while ($row = mysqli_fetch_assoc($query)) {
    echo $no++ . "\t" .
         $row['produk'] . "\t" .
         $row['jml_beli'] . "\t" .
         $row['harga_satuan'] . "\t" .
         $row['no_meja'] . "\t" .
         "=\"" . $row['tgl_pesan'] . "\"\t" .  // Format tanggal sebagai teks
         $row['jam_pesan'] . "\t" .
         $row['status'] . "\n";
}
?>

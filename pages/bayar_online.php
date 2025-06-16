<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['last_id_pesanan'])) {
    echo "ID Pesanan tidak ditemukan.";
    exit;
}

$id_pesanan = intval($_SESSION['last_id_pesanan']);

$q = mysqli_query($conn, "SELECT * FROM pemesanan WHERE id_pesan = $id_pesanan");
$data = mysqli_fetch_assoc($q);

if (!$data) {
    echo "Pesanan tidak ditemukan.";
    exit;
}

$total_harga = (int)$data['total_harga'];
$no_meja = $data['no_meja'];

// Update metode jadi 'online'
mysqli_query($conn, "UPDATE pemesanan SET metode = 'online' WHERE id_pesan = $id_pesanan");

// Midtrans
require_once dirname(__FILE__) . '/midtrans-php-master/Midtrans.php';

\Midtrans\Config::$serverKey = 'SB-Mid-server-g3QPGBIQ7w6rP_LhcYIsxgBO';
\Midtrans\Config::$isProduction = false;
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

$order_id = 'ORDER-' . $id_pesanan . '-' . time();

$params = [
  'transaction_details' => [
    'order_id' => $order_id,
    'gross_amount' => $total_harga,
  ],
  'customer_details' => [
    'first_name' => "Budi",
    'email' => "budi@example.com",
  ],
  'callbacks' => [
    'finish' => "http://localhost/projek1/pages/detail_pesanan.php?id_pesan=$id_pesanan"
  ]
];

try {
    $snapToken = \Midtrans\Snap::getSnapToken($params);
} catch (Exception $e) {
    echo "Gagal membuat Snap Token: " . $e->getMessage();
    exit;
}

// Tambahkan entry pembayaran awal dengan status "pending"
$cek = mysqli_query($conn, "SELECT * FROM pembayaran WHERE id_pesan = $id_pesanan");
if (mysqli_num_rows($cek) > 0) {
    mysqli_query($conn, "
        UPDATE pembayaran 
        SET 
            order_id_midtrans = '$order_id',
            status_pembayaran = 'pending',
            metode = 'online',
            total_bayar = $total_harga,
            waktu_bayar = NOW()
        WHERE id_pesan = $id_pesanan
    ");
} else {
    mysqli_query($conn, "
        INSERT INTO pembayaran (id_pesan, order_id_midtrans, status_pembayaran, metode, total_bayar, waktu_bayar)
        VALUES ($id_pesanan, '$order_id', 'pending', 'online', $total_harga, NOW())
    ");
}


?>
<!DOCTYPE html>
<html>
<head>
    <title>Pembayaran Online</title>
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="SB-Mid-client-lqzvytkKq4yazpYk"></script>
</head>
<body>
    <h2>Total Pembayaran: Rp<?= number_format($total_harga) ?></h2>
    <button id="pay-button">Bayar Sekarang</button>

    <script type="text/javascript">
        document.getElementById('pay-button').onclick = function () {
            snap.pay("<?= $snapToken ?>", {
                onSuccess: function(result){
                    console.log("Pembayaran sukses", result);
                    window.location.href = "checkout_success.php?id_pesan=<?= $id_pesanan ?>";
                },
                onPending: function(result){
                    console.log("Menunggu pembayaran", result);
                    window.location.href = "checkout_success.php?id_pesan=<?= $id_pesanan ?>";
                },
                onError: function(result){
                    alert("Pembayaran gagal");
                    console.log(result);
                }
            });
        };
    </script>
</body>
</html>

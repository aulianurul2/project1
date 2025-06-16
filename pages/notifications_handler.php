<?php
include '../koneksi.php';
require_once dirname(__FILE__) . '/midtrans-php-master/Midtrans.php';

\Midtrans\Config::$serverKey = 'SB-Mid-server-g3QPGBIQ7w6rP_LhcYIsxgBO';
\Midtrans\Config::$isProduction = false;
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

// Ambil notifikasi
$json_result = file_get_contents('php://input');
$notification = json_decode($json_result);

// Cek jenis status
$transaction = $notification->transaction_status;
$order_id = str_replace("ORDER-", "", $notification->order_id); // ORDER-134 jadi 134

if ($transaction == 'settlement') {
    mysqli_query($conn, "UPDATE pemesanan SET status_pembayaran = 'lunas' WHERE id_pesan = $order_id");
} elseif ($transaction == 'expire') {
    mysqli_query($conn, "UPDATE pemesanan SET status_pembayaran = 'expired' WHERE id_pesan = $order_id");
} elseif ($transaction == 'pending') {
    mysqli_query($conn, "UPDATE pemesanan SET status_pembayaran = 'pending' WHERE id_pesan = $order_id");
}
http_response_code(200); // beri respon ke Midtrans bahwa webhook berhasil
?>

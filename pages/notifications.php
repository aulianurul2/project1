<?php
require_once '../vendor/autoload.php'; // pastikan sudah include Midtrans library

\Midtrans\Config::$serverKey = 'SB-Mid-server-g3QPGBIQ7w6rP_LhcYIsxgBO';
\Midtrans\Config::$isProduction = false;
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

$notif = new \Midtrans\Notification();

$order_id = $notif->order_id;
$transaction = $notif->transaction_status;

if ($transaction == 'settlement') {
    // koneksi DB kamu di sini
    $conn = new mysqli("localhost", "root", "", "ui_roject2");

    // pastikan order_id mapping ke tabel kamu
    $update = "UPDATE pembayaran SET status_pembayaran='Sudah dibayar' WHERE id_pesan='$order_id'";
    $conn->query($update);
}
?>

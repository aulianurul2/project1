<?php
$order_id = $_GET['order_id'];
$metode = $_GET['metode'];
$total = $_GET['total'] ?? 0;

echo "<h2>Pembayaran Berhasil!</h2>";
echo "<p>Order ID: $order_id</p>";
echo "<p>Metode: $metode</p>";
echo "<p>Total: Rp " . number_format($total, 0, ',', '.') . "</p>";

// Di sini bisa lanjut simpan ke DB: pesanan dan status = selesai
?>

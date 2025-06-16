<?php
// Konfigurasi Midtrans
require_once '../vendor/autoload.php'; // Sesuaikan path autoload Anda
\Midtrans\Config::$serverKey = 'SB-Mid-server-g3QPGBIQ7w6rP_LhcYIsxgBO'; // Ganti dengan server key sandbox Anda
\Midtrans\Config::$isProduction = false;
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

// Ambil order_id dari parameter GET misalnya ?order_id=ORDER-135
$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : null;

if (!$order_id) {
    die("Order ID tidak ditemukan.");
}

try {
    // Ambil status transaksi dari Midtrans
    $status = \Midtrans\Transaction::status($order_id);

    // Debug: tampilkan semua isi status jika perlu
    // echo "<pre>"; print_r($status); echo "</pre>";

    $status = \Midtrans\Transaction::status($order_id);

    echo "Status transaksi untuk {$order_id} adalah: {$transaction_status} <br>";

    // Jika pembayaran sukses
    if ($transaction_status == 'settlement') {
        // Koneksi ke database
        $koneksi = new mysqli("localhost", "root", "", "db_warkop"); // Ganti sesuai database Anda

        if ($koneksi->connect_error) {
            die("Koneksi gagal: " . $koneksi->connect_error);
        }

        // Update status di tabel pembayaran
        $stmt = $koneksi->prepare("UPDATE pembayaran SET status_pembayaran = 'Sudah dibayar' WHERE id_pesan = ?");
        $id_pesan = str_replace("ORDER-", "", $order_id); // Pastikan sesuai struktur Anda
        $stmt->bind_param("i", $id_pesan);

        if ($stmt->execute()) {
            echo "Status pembayaran berhasil diupdate di database.";
        } else {
            echo "Gagal mengupdate database.";
        }

        $stmt->close();
        $koneksi->close();
    }

} catch (Exception $e) {
    echo "Terjadi kesalahan: " . $e->getMessage();
}
?>

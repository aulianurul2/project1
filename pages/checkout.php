<?php
session_start();
include '../koneksi.php';
require_once dirname(__FILE__) . '/midtrans-php-master/Midtrans.php';

// Konfigurasi Midtrans
\Midtrans\Config::$serverKey = 'SB-Mid-server-g3QPGBIQ7w6rP_LhcYIsxgBO';
\Midtrans\Config::$isProduction = false;
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

// Validasi input
$metode = $_POST['metode'] ?? 'cash';
if (!in_array($metode, ['cash', 'online'])) {
    echo "<script>alert('Metode pembayaran tidak valid.'); window.history.back();</script>";
    exit;
}

// Cek keranjang
if (empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit;
}

$cart = $_SESSION['cart'];
$no_meja = isset($_SESSION['no_meja']) ? intval($_SESSION['no_meja']) : 0;

if ($no_meja <= 0) {
    echo "<script>alert('Nomor meja tidak valid.'); window.history.back();</script>";
    exit;
}

mysqli_begin_transaction($conn);

try {
    $tanggal = date('Y-m-d');
    $jam = date('H:i:s');
    $waktu_pesan = $tanggal . ' ' . $jam;
    $total = 0;

    // Hitung total dari keranjang
    foreach ($cart as $id => $item) {
        $id = intval($id);
        $jumlah = $item['qty'];

        $result = mysqli_query($conn, "SELECT harga, stok FROM menu WHERE id_menu = $id");
        $row = mysqli_fetch_assoc($result);
        if (!$row) throw new Exception("Produk dengan ID $id tidak ditemukan.");
        if ($jumlah > $row['stok']) throw new Exception("Stok tidak cukup untuk produk ID $id.");

        $total += $row['harga'] * $jumlah;
    }

    // Cari pesanan aktif berdasarkan meja
    $cek = mysqli_query($conn, "SELECT id_pesan FROM pemesanan WHERE no_meja = $no_meja AND status IN ('menunggu pembayaran', 'diproses') LIMIT 1");
    $id_pesanan = 0;

    if (mysqli_num_rows($cek) > 0) {
        $row = mysqli_fetch_assoc($cek);
        $id_pesanan = $row['id_pesan'];
        mysqli_query($conn, "UPDATE pemesanan SET total_harga = total_harga + $total WHERE id_pesan = $id_pesanan");
    } else {
        $stmt = mysqli_prepare($conn, "INSERT INTO pemesanan (tanggal, total_harga, status, no_meja, metode) VALUES (?, ?, 'menunggu pembayaran', ?, ?)");
        mysqli_stmt_bind_param($stmt, "sdis", $waktu_pesan, $total, $no_meja, $metode);
        mysqli_stmt_execute($stmt);
        $id_pesanan = mysqli_insert_id($conn);
    }

    $_SESSION['last_id_pesanan'] = $id_pesanan;

    // Simpan detail pesanan & update stok
    $sql_detail = "INSERT INTO detail_pesanan (id_pesan, id_menu, jml_beli, harga_satuan, no_meja, tgl_pesan, jam_pesan, produk)
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_detail = mysqli_prepare($conn, $sql_detail);

    $sql_update_stok = "UPDATE menu SET stok = stok - ? WHERE id_menu = ?";
    $stmt_update_stok = mysqli_prepare($conn, $sql_update_stok);

    foreach ($cart as $id => $item) {
        $id = intval($id);
        $jumlah = $item['qty'];

        $query = mysqli_query($conn, "SELECT harga, nama_produk FROM menu WHERE id_menu = $id");
        $row = mysqli_fetch_assoc($query);
        $harga = $row['harga'];
        $nama_produk = $row['nama_produk'];

        mysqli_stmt_bind_param($stmt_detail, "iiidisss", $id_pesanan, $id, $jumlah, $harga, $no_meja, $tanggal, $jam, $nama_produk);
        mysqli_stmt_execute($stmt_detail);

        mysqli_stmt_bind_param($stmt_update_stok, "ii", $jumlah, $id);
        mysqli_stmt_execute($stmt_update_stok);
    }

    // Update status meja
    $update_meja = mysqli_query($conn, "UPDATE meja SET status = 'diproses' WHERE nomor_meja = $no_meja");
    if (!$update_meja) throw new Exception("Gagal update status meja.");

    mysqli_commit($conn);

    // Kosongkan keranjang
    $_SESSION['last_checkout'] = $cart;
    unset($_SESSION['cart']);

    if ($metode === 'cash') {
        header("Location: checkout_success.php");
        exit;
    } else {
        // Midtrans
        $order_id = "ORDER-" . $id_pesanan . "-" . time();

        $params = [
            'transaction_details' => [
                'order_id' => $order_id,
                'gross_amount' => (int)$total,
            ],
            'customer_details' => [
                'first_name' => 'Meja ' . $no_meja,
                'email' => 'meja' . $no_meja . '@warkop.com',
                'phone' => '081234567890',
            ],
        ];

        $snapToken = \Midtrans\Snap::getSnapToken($params);

        $_SESSION['snap_token'] = $snapToken;
        $_SESSION['order_id'] = $order_id;
        $_SESSION['total'] = $total;

        header("Location: bayar_online.php");
        exit;
    }

} catch (Exception $e) {
    mysqli_rollback($conn);
    echo "<script>alert('Checkout gagal: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
    exit;
}
?>
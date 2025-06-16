<?php
include '../koneksi.php';

if (!isset($_GET['id_pesan'])) {
    echo json_encode(['status' => 'invalid']);
    exit;
}

$id = intval($_GET['id_pesan']);
$q = mysqli_query($conn, "SELECT status FROM pemesanan WHERE id_pesan = $id LIMIT 1");

if ($d = mysqli_fetch_assoc($q)) {
    echo json_encode(['status' => $d['status']]);
} else {
    echo json_encode(['status' => 'not_found']);
}
?>

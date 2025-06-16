<?php
include "../koneksi.php";
require_once '../admin/phpqrcode/qrlib.php'; // pastikan path-nya benar

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nomor_meja = mysqli_real_escape_string($conn, $_POST['nomor_meja']);
    $status = 'kosong'; // Otomatis default ke "kosong"

    // Simpan dulu ke DB
    $query = "INSERT INTO meja (nomor_meja, status) VALUES ('$nomor_meja', '$status')";
    if (mysqli_query($conn, $query)) {
        $id_meja = mysqli_insert_id($conn); // Ambil ID terakhir (opsional, tapi bisa disimpan)

        // GUNAKAN nomor_meja untuk generate link
        $link = "http://localhost/projek1/pages/pilih_menu.php?meja=" . urlencode($nomor_meja);

        // Pastikan folder barcode ada
        $folderPath = "../uploads/barcode/";
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        $barcodeFile = 'meja_' . preg_replace('/[^a-zA-Z0-9]/', '_', $nomor_meja) . '_' . time() . '.png';
        $filePath = $folderPath . $barcodeFile;

        // Generate QR
        QRcode::png($link, $filePath, QR_ECLEVEL_H, 5);

        // Simpan nama file barcode ke DB
        $updateQuery = "UPDATE meja SET barcode_image = '$barcodeFile' WHERE id_meja = $id_meja";
        mysqli_query($conn, $updateQuery);

        header("Location: meja.php?success=1");
        exit();
    } else {
        echo "Gagal menambahkan meja: " . mysqli_error($conn);
    }
}
?>

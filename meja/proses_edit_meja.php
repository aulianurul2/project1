<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../login.php");
    exit();
}

include "../koneksi.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_meja = intval($_POST['id_meja']);
    $nomor_meja = mysqli_real_escape_string($conn, $_POST['nomor_meja']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $valid_status = ['kosong', 'diproses', 'selesai'];

    if ($id_meja <= 0 || empty($nomor_meja) || !in_array($status, $valid_status)) {
        $_SESSION['error'] = "Data tidak valid!";
        header("Location: data_meja.php");
        exit();
    }

    // Ambil barcode lama
    $result = mysqli_query($conn, "SELECT barcode_image FROM meja WHERE id_meja = $id_meja");
    $data = mysqli_fetch_assoc($result);
    $barcode_name = $data['barcode_image'];

    $target_dir = "../uploads/barcode/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    if (isset($_FILES['barcode_image']) && $_FILES['barcode_image']['name'] != "") {
        $file_name = time() . "_" . basename($_FILES['barcode_image']['name']);
        $target_file = $target_dir . $file_name;
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($file_type, $allowed_types)) {
            $_SESSION['error'] = "Format gambar tidak didukung.";
            header("Location: meja.php");
            exit();
        }

        if (move_uploaded_file($_FILES['barcode_image']['tmp_name'], $target_file)) {
            // Hapus file lama jika ada
            if ($barcode_name && file_exists($target_dir . $barcode_name)) {
                unlink($target_dir . $barcode_name);
            }
            $barcode_name = $file_name;
        } else {
            $_SESSION['error'] = "Gagal mengupload gambar baru.";
            header("Location: meja.php");
            exit();
        }
    }

    $query = "UPDATE meja SET nomor_meja='$nomor_meja', status='$status', barcode_image='$barcode_name' WHERE id_meja=$id_meja";
    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Meja berhasil diperbarui.";
    } else {
        $_SESSION['error'] = "Gagal memperbarui meja: " . mysqli_error($conn);
    }
    header("Location: meja.php");
    exit();
}

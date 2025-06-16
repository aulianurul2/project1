<?php
$host = "localhost"; // atau 127.0.0.1
$user = "root";
$pass = "";
$db   = "ui_project2";

// Buat koneksi
$conn = new mysqli($host, $user, $pass, $db);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>

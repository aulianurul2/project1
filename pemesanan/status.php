<?php

include '../koneksi.php';

if (!isset($_GET['id_pesan'])) {
   
    exit;
}

$id_pesan = intval($_GET['id_pesan']);

// Ambil waktu diproses
$query = mysqli_query($conn, "SELECT waktu_diproses FROM pemesanan WHERE id_pesan = $id_pesan AND status = 'diproses'");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    header('Location: ../index.php');
    exit;
}

$waktu_diproses = strtotime($data['waktu_diproses']);
$sekarang = time();
$sisa_detik = 1800 - ($sekarang - $waktu_diproses); // 1800 detik = 30 menit

// Jika sudah lewat 30 menit, hapus dan redirect
if ($sisa_detik <= 0) {
    mysqli_query($conn, "DELETE FROM pemesanan WHERE id_pesan = $id_pesan");
    header('Location: ../index.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Status Pesanan</title>
    <style>
        .notif {
            background-color: #d1e7dd;
            color: #0f5132;
            padding: 20px;
            margin: 30px auto;
            width: 70%;
            border-radius: 10px;
            font-family: Arial, sans-serif;
            text-align: center;
        }
        .timer {
            font-size: 24px;
            font-weight: bold;
            margin-top: 10px;
        }
    </style>
    <script>
        let waktuTersisa = <?= $sisa_detik ?>;

        function updateTimer() {
            let menit = Math.floor(waktuTersisa / 60);
            let detik = waktuTersisa % 60;
            detik = detik < 10 ? '0' + detik : detik;
            document.getElementById("timer").innerHTML = `${menit}:${detik}`;

            if (waktuTersisa <= 0) {
                window.location.href = "../index.php";
            }

            waktuTersisa--;
        }

        setInterval(updateTimer, 1000);
        window.onload = updateTimer;
    </script>
</head>
<body>
    <div class="notif">
        <h2>Estimasi Waktu Pesanan</h2>
        <div class="timer" id="timer">00:00</div>
    </div>
</body>
</html>

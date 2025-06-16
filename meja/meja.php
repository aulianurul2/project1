<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../../login.php");
    exit();
}

include "../koneksi.php";

// Update status meja berdasarkan pesanan terakhir
$sql_meja = "SELECT id_meja, nomor_meja FROM meja";
$result_meja = mysqli_query($conn, $sql_meja);

if ($result_meja && mysqli_num_rows($result_meja) > 0) {
    while ($row = mysqli_fetch_assoc($result_meja)) {
        $id_meja = $row['id_meja'];
        $nomor_meja = mysqli_real_escape_string($conn, $row['nomor_meja']);

        $sql_cek = "SELECT p.status 
                    FROM pemesanan p
                    JOIN detail_pesanan dp ON p.id_pesan = dp.id_pesan
                    WHERE dp.no_meja = '$nomor_meja'
                    ORDER BY p.id_pesan DESC 
                    LIMIT 1";
        $result_cek = mysqli_query($conn, $sql_cek);

        $status_meja = "kosong"; // default

        if ($result_cek && mysqli_num_rows($result_cek) > 0) {
            $pesan = mysqli_fetch_assoc($result_cek);
            $status_pesanan = $pesan['status'];

            // Cek status pesanan dan atur status meja
            if ($status_pesanan == 'diproses') {
                $status_meja = 'diproses';
            } elseif ($status_pesanan == 'selesai') {
                $status_meja = 'selesai';
            } elseif ($status_pesanan == 'selesai_keluar') {
                $status_meja = 'kosong'; // reset ke default "kosong"
            }
        }

        $sql_update = "UPDATE meja SET status = '$status_meja' WHERE id_meja = $id_meja";
        mysqli_query($conn, $sql_update);
    }
}

// Ambil data meja dan urutkan berdasarkan nomor_meja
$query_tampil_meja = "SELECT * FROM meja ORDER BY nomor_meja ASC";
$result = mysqli_query($conn, $query_tampil_meja);
if (!$result) {
    die("Query gagal: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Meja</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,700,900" rel="stylesheet">
    <link href="../assets/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
</head>
<body id="page-top">

<div id="wrapper">
    <?php include '../layout/sidebar.php'; ?>
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <?php include '../layout/navbar.php'; ?>
            <div class="container-fluid">

                <h1 class="h3 mb-2 text-gray-800">Data Meja</h1>
                <img src="https://cdn-icons-png.flaticon.com/512/3388/3388642.png" alt="Ilustrasi Meja" width="120" class="mb-3">

                <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#tambahMejaModal">
                    <i class="fas fa-plus"></i> Tambah Meja
                </button>

                <div class="card shadow mb-4">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>ID Meja</th>
                                        <th>Nomor Meja</th>
                                        <th>Status</th>
                                        <th>Barcode</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                                        <tr>
                                            <td><?= $row['id_meja'] ?></td>
                                            <td><?= htmlspecialchars($row['nomor_meja']) ?></td>
                                            <td>
                                                <?php
                                                $status = $row['status'];
                                                $badge = [
                                                    'kosong' => 'success',
                                                    'diproses' => 'warning',
                                                    'selesai' => 'info',
                                                    'dipesan' => 'danger'
                                                ];
                                                $label = ucfirst($status);
                                                $badgeClass = $badge[$status] ?? 'secondary';
                                                echo "<span class='badge badge-$badgeClass'>$label</span>";
                                                ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($row['barcode_image'])) : ?>
                                                    <img src="../uploads/barcode/<?= htmlspecialchars($row['barcode_image']) ?>" alt="Barcode" width="100">
                                                <?php else : ?>
                                                    <span class="text-muted">Belum ada</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#editMejaModal<?= $row['id_meja'] ?>">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>
                                                <a href="hapus_meja.php?id=<?= $row['id_meja'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin hapus meja ini?')">
                                                    <i class="fas fa-trash"></i> Hapus
                                                </a>
                                                 <a href="cetak_barcode.php?id=<?= $row['id_meja'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('apakah ingin mencetak?')">
                                                    <i class="fas fa-print"></i> cetak
                                                </a>
                                            </td>
                                        </tr>

                                        <!-- Modal Edit -->
                                        <div class="modal fade" id="editMejaModal<?= $row['id_meja'] ?>" tabindex="-1" role="dialog">
                                            <div class="modal-dialog" role="document">
                                                <form action="proses_edit_meja.php" method="POST" enctype="multipart/form-data">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Edit Meja</h5>
                                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <input type="hidden" name="id_meja" value="<?= $row['id_meja'] ?>">
                                                            <div class="form-group">
                                                                <label>Nomor Meja</label>
                                                                <input type="text" name="nomor_meja" class="form-control" value="<?= htmlspecialchars($row['nomor_meja']) ?>" required>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Status</label>
                                                                <select name="status" class="form-control" required>
                                                                    <option value="">-- Pilih Status --</option>
                                                                    <option value="kosong" <?= $row['status'] == 'kosong' ? 'selected' : '' ?>>Kosong</option>
                                                                    <option value="diproses" <?= $row['status'] == 'diproses' ? 'selected' : '' ?>>Diproses</option>
                                                                    <option value="selesai" <?= $row['status'] == 'selesai' ? 'selected' : '' ?>>Selesai</option>
                                                                </select>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Ganti Barcode (opsional)</label>
                                                                <input type="file" name="barcode_image" class="form-control-file">
                                                                <?php if ($row['barcode_image']) : ?>
                                                                    <small class="text-muted">Barcode saat ini: <?= htmlspecialchars($row['barcode_image']) ?></small>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Meja -->
<div class="modal fade" id="tambahMejaModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form action="proses_tambah_meja.php" method="POST" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Meja</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nomor Meja</label>
                        <input type="text" name="nomor_meja" class="form-control" required>
                    </div>
                    <input type="hidden" name="status" value="kosong">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Tambah</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Script -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
<script src="../assets/js/sb-admin-2.min.js"></script>

</body>
</html>

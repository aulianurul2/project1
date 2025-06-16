<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
include "../koneksi.php";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Menu - Terrace A</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,700,900" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="../assets/css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">
<div id="wrapper">
    <?php include '../layout/sidebar.php'; ?>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <?php include '../layout/navbar.php'; ?>

            <div class="container-fluid">
                <h1 class="h3 text-gray-800 mb-4">Kelola Menu</h1>

                <!-- Form Tambah Menu -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Tambah Menu Baru</h6>
                    </div>
                    <div class="card-body">
                        <form action="proses_tambah_menu_admin.php" method="POST" enctype="multipart/form-data" onsubmit="return validateImage()">
                            <div class="form-group">
                                <label>Nama Makanan/Minuman</label>
                                <input type="text" name="nama_produk" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Harga (Rp)</label>
                                <input type="number" name="harga" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Stok</label>
                                <input type="number" name="stok" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Kategori</label>
                                <select name="kategori" class="form-control" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    <option value="makanan">Makanan</option>
                                    <option value="minuman">Minuman</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Gambar (maksimal 8MB)</label>
                                <input type="file" name="gambar" id="gambar" class="form-control-file" accept="image/*" required>
                                <div id="alert-size" class="text-danger mt-2" style="display: none;">
                                    Ukuran file terlalu besar! Maksimum 8MB.
                                </div>
                                <img id="preview-gambar" src="#" alt="Preview Gambar" style="display:none; max-width: 200px; margin-top: 10px;" />
                            </div>
                            <button type="submit" class="btn btn-primary">Tambahkan</button>
                        </form>
                    </div>
                </div>

                <!-- Tabel Daftar Menu -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Daftar Menu</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="tabelMenu" class="table table-bordered" width="100%" cellspacing="0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>ID Menu</th>
                                        <th>Gambar</th>
                                        <th>Nama</th>
                                        <th>Kategori</th>
                                        <th>Harga</th>
                                        <th>Stok</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $query = mysqli_query($conn, "SELECT * FROM menu ORDER BY id_menu DESC");
                                    while ($menu = mysqli_fetch_assoc($query)) {
                                        echo "<tr>
                                            <td>{$menu['id_menu']}</td>
                                            <td><img src='../uploads/menu/{$menu['gambar']}' width='60'></td>
                                            <td>{$menu['nama_produk']}</td>
                                            <td>" . ucfirst($menu['kategori']) . "</td>
                                            <td>Rp " . number_format($menu['harga']) . "</td>
                                            <td>{$menu['stok']}</td>
                                            <td>
                                                <a href='edit.php?id={$menu['id_menu']}' class='btn btn-warning btn-sm'>Edit</a>
                                                <a href='hapus.php?id={$menu['id_menu']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Hapus menu ini?\")'>Hapus</a>
                                            </td>
                                        </tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- JS CDN dan Custom -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="../assets/js/sb-admin-2.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

<!-- Inisialisasi DataTables -->
<script>
$(document).ready(function () {
    $('#tabelMenu').DataTable({
        "pageLength": 10,
        "order": [[0, "desc"]], // Urutkan berdasarkan kolom ID Menu secara descending
        "language": {
            "lengthMenu": "Tampilkan _MENU_ menu per halaman",
            "zeroRecords": "Menu tidak ditemukan",
            "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ menu",
            "infoEmpty": "Tidak ada menu tersedia",
            "infoFiltered": "(difilter dari total _MAX_ menu)",
            "search": "Cari:",
            "paginate": {
                "first": "Pertama",
                "last": "Terakhir",
                "next": "Berikutnya",
                "previous": "Sebelumnya"
            }
        }
    });
});
</script>

<!-- Validasi Ukuran & Preview Gambar -->
<script>
function validateImage() {
    const fileInput = document.getElementById('gambar');
    const file = fileInput.files[0];
    const maxSize = 8 * 1024 * 1024;
    const alertBox = document.getElementById('alert-size');

    if (file && file.size > maxSize) {
        alertBox.style.display = 'block';
        return false;
    } else {
        alertBox.style.display = 'none';
        return true;
    }
}

document.getElementById("gambar").addEventListener("change", function () {
    const file = this.files[0];
    const preview = document.getElementById("preview-gambar");

    if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
            preview.src = e.target.result;
            preview.style.display = "block";
        }
        reader.readAsDataURL(file);
    } else {
        preview.src = "#";
        preview.style.display = "none";
    }
});
</script>

</body>
</html>

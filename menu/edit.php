<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../../login.php");
    exit();
}

include "../koneksi.php";

$id = $_GET['id'];
$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM menu WHERE id_menu = $id"));
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit Menu</title>

    <!-- Custom fonts and styles -->
    <link href="../assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,700,900" rel="stylesheet">
    <link href="../assets/css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <?php include '../layout/sidebar.php'; ?>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <?php include '../layout/navbar.php'; ?>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <h1 class="h3 mb-4 text-gray-800">Edit Menu</h1>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="card shadow mb-4">
                                <div class="card-body">
                                    <form action="proses_edit_menu.php" method="POST" enctype="multipart/form-data">
                                        <input type="hidden" name="id" value="<?= $data['id_menu'] ?>">

                                        <div class="form-group">
                                            <label>Nama Produk</label>
                                            <input type="text" class="form-control" name="nama_produk" value="<?= $data['nama_produk'] ?>" required>
                                        </div>

                                        <div class="form-group">
                                            <label>Harga (Rp)</label>
                                            <input type="number" class="form-control" name="harga" value="<?= $data['harga'] ?>" required>
                                        </div>

                                        <div class="form-group">
                                            <label>Stok</label>
                                            <input type="number" class="form-control" name="stok" value="<?= $data['stok'] ?>" required>
                                        </div>

                                        <div class="form-group">
                                            <label>Kategori</label>
                                            <select class="form-control" name="kategori" required>
                                                <option value="makanan" <?= $data['kategori'] == 'makanan' ? 'selected' : '' ?>>Makanan</option>
                                                <option value="minuman" <?= $data['kategori'] == 'minuman' ? 'selected' : '' ?>>Minuman</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label>Gambar Saat Ini</label><br>
                                            <img src="/projek1/uploads/menu/<?= $data['gambar'] ?>" width="100">
                                        </div>

                                        <div class="form-group">
                                            <label>Ganti Gambar (Opsional)</label>
                                            <input type="file" class="form-control" name="gambar">
                                        </div>

                                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                        <a href="/projek1/menu/index.php?page=menu" class="btn btn-secondary ml-2">Batal</a>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scripts -->
    <script src="../../vendor/jquery/jquery.min.js"></script>
    <script src="../../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../../vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="../../assets/js/sb-admin-2.min.js"></script>

</body>

</html>

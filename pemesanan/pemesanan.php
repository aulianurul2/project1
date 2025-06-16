<?php
session_start();
include '../koneksi.php';

if (isset($_POST['bayar_cash'])) {
    $id_pesan = $_POST['id_pesan'];
    $total = $_POST['total_tagihan'];
    $uang = $_POST['uang_diberikan'];
    $waktu = date("Y-m-d H:i:s");

    if ($uang < $total) {
        $_SESSION['error'] = "Uang yang diberikan kurang dari total!";
    } else {
        $simpan = mysqli_query($conn, "INSERT INTO pembayaran (id_pesan, metode, waktu_bayar, total_bayar) 
            VALUES ('$id_pesan', 'cash', '$waktu', '$uang')");

        if ($simpan) {
            mysqli_query($conn, "UPDATE pemesanan SET status = 'diproses' WHERE id_pesan = '$id_pesan'");
            $_SESSION['success'] = "Pembayaran cash berhasil. Kembalian: Rp " . number_format($uang - $total, 0, ',', '.');
        } else {
            $_SESSION['error'] = "Gagal menyimpan pembayaran cash.";
        }
    }

    header("Location: pemesanan.php");
    exit();
}

$query = "
    SELECT 
        p.id_pesan,
        p.status,
        p.tanggal,
        GROUP_CONCAT(CONCAT(dp.produk, ' x', dp.jml_beli) SEPARATOR ', ') AS produk_all,
        SUM(dp.jml_beli * dp.harga_satuan) AS total_harga,
        GROUP_CONCAT(DISTINCT dp.no_meja ORDER BY dp.no_meja SEPARATOR ', ') AS no_meja_all,
        pb.metode,
        pb.total_bayar,
        pb.waktu_bayar
    FROM pemesanan p
    JOIN detail_pesanan dp ON p.id_pesan = dp.id_pesan
    LEFT JOIN pembayaran pb ON p.id_pesan = pb.id_pesan
    WHERE p.status IN ('menunggu pembayaran', 'diproses', 'selesai', 'selesai_keluar')
    GROUP BY p.id_pesan
    ORDER BY p.id_pesan DESC
";

$pesanan = mysqli_query($conn, $query);
if (!$pesanan) {
    die("Query gagal: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Data Pemesanan</title>
  <link href="../assets/css/sb-admin-2.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css" rel="stylesheet" />
</head>
<body id="page-top">
<div id="wrapper">
  <?php include '../layout/sidebar.php'; ?>
  <div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
      <?php include '../layout/navbar.php'; ?>
      <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Data Pemesanan</h1>

        <?php if (isset($_SESSION['success'])): ?>
          <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
          <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <div class="card shadow mb-4">
          <div class="card-body">
            <div class="table-responsive">
              <table id="dtPemesanan" class="table table-bordered">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>No Meja</th>
                    <th>Produk</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Metode</th>
                    <th>Waktu Bayar</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                <?php while($r = mysqli_fetch_assoc($pesanan)): ?>
                  <tr>
                    <td><?= htmlspecialchars($r['id_pesan']) ?></td>
                    <td><?= htmlspecialchars($r['no_meja_all']) ?></td>
                    <td><?= htmlspecialchars($r['produk_all']) ?></td>
                    <td>Rp <?= number_format($r['total_harga'], 0, ',', '.') ?></td>
                    <td>
                      <?php
                      $badgeClass = [
                          'menunggu pembayaran' => 'warning',
                          'diproses' => 'primary',
                          'selesai' => 'success',
                          'selesai_keluar' => 'dark'
                      ];
                      $statusText = ucwords(str_replace('_', ' ', $r['status']));
                      $badge = $badgeClass[$r['status']] ?? 'secondary';
                      echo "<span class='badge badge-$badge'>$statusText</span>";
                      ?>
                    </td>
                    <td><?= $r['status'] != 'menunggu pembayaran' ? htmlspecialchars($r['metode'] ?? '-') : '-' ?></td>
                    <td><?= $r['status'] != 'menunggu pembayaran' ? htmlspecialchars($r['waktu_bayar'] ?? '-') : '-' ?></td>
                    <td><?= htmlspecialchars($r['tanggal']) ?></td>
                    <td>
                      <?php if ($r['status'] == 'menunggu pembayaran' && $r['metode'] == 'cash' && empty($r['waktu_bayar'])): ?>
                        <form action="pemesanan.php" method="POST" class="form-inline">
                          <input type="hidden" name="id_pesan" value="<?= $r['id_pesan'] ?>">
                          <input type="hidden" name="total_tagihan" value="<?= $r['total_harga'] ?>">
                          <input type="number" step="100" name="uang_diberikan" class="form-control form-control-sm mb-1 mr-1" placeholder="Uang Diberikan" required style="width:120px;">
                          <button type="submit" name="bayar_cash" class="btn btn-sm btn-success mb-1">Bayar Cash</button>
                        </form>
                      <?php elseif (in_array($r['status'], ['menunggu pembayaran', 'diproses', 'selesai'])): ?>
                        <button class="btn btn-sm btn-primary" data-toggle="modal"
                          data-target="#modalUpdate" data-id="<?= $r['id_pesan'] ?>">
                          Update Status
                        </button>
                      <?php else: ?>
                        <span class="badge badge-secondary">-</span>
                      <?php endif; ?>
                    </td>
                  </tr>
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

<!-- Modal Update Status -->
<div class="modal fade" id="modalUpdate" tabindex="-1" role="dialog" aria-labelledby="modalUpdateLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form action="update_status.php" method="POST">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalUpdateLabel">Update Status Pemesanan</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id_pesan" id="id_pesan" />
          <div class="form-group">
            <label>Status Baru</label>
            <select name="status_baru" class="form-control" required>
              <option value="">-- Pilih Status --</option>
              <option value="diproses">Diproses</option>
              <option value="selesai">Selesai</option>
              <option value="selesai_keluar">Selesai Keluar</option>
              <option value="dibatalkan">Dibatalkan</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" name="update" class="btn btn-primary">Update</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script>
$(document).ready(function () {
  $('#dtPemesanan').DataTable({
    order: [[0, 'desc']]
  });

  $('#modalUpdate').on('show.bs.modal', function (e) {
    const id = $(e.relatedTarget).data('id');
    $('#id_pesan').val(id);
  });
});
</script>
</body>
</html>

<?php
if (!isset($_SESSION)) session_start();
include "../koneksi.php";

// Handle AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['metode'])) {
  header('Content-Type: application/json');
  $action = $_POST['action'] ?? '';

  // Hapus item
  if ($action === 'hapus' && isset($_POST['id'])) {
    $id = $_POST['id'];

    if (!isset($_SESSION['cart'])) {
      echo json_encode(['status' => 'error', 'message' => 'Cart belum dibuat']);
      exit;
    }

    if (isset($_SESSION['cart'][$id])) {
      unset($_SESSION['cart'][$id]);
      echo json_encode(['status' => 'success']);
    } else {
      echo json_encode(['status' => 'error', 'message' => 'Item tidak ditemukan']);
    }
    exit;
  }

  // Update quantity
  if ($action === 'update' && isset($_POST['cart'])) {
    foreach ($_POST['cart'] as $item) {
      $id = $item['id'];
      $qty = max(1, (int)$item['qty']);
      if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]['qty'] = $qty;
      }
    }
    echo json_encode(['status' => 'updated']);
    exit;
  }
}

// Tampilkan isi keranjang
$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
  // echo "<p style='text-align:center; padding:10px;'>Keranjang kosong.</p>";
  return;
}

// Ambil data menu dari database
$ids = implode(",", array_map('intval', array_keys($cart)));
$result = mysqli_query($conn, "SELECT * FROM menu WHERE id_menu IN ($ids)");
$menuData = [];
while ($row = mysqli_fetch_assoc($result)) {
  $menuData[$row['id_menu']] = $row;
}

$grand_total = 0;
?>

<div class="cart-content" style="max-height: 400px; overflow-y: auto; padding: 10px;">
  <?php foreach ($cart as $id => $item): ?>
    <?php
      $produk = $menuData[$id];
      $qty = $item['qty'];
      $subtotal = $produk['harga'] * $qty;
      $grand_total += $subtotal;
    ?>
    <div class="cart-item" data-id="<?= $id ?>" style="display: flex; gap: 10px; margin-bottom: 10px; align-items:center;">
      <button class="btn-hapus" data-id="<?= $id ?>" style="cursor:pointer; background: crimson; color: white; border: none; border-radius: 4px; padding: 4px 6px;">🗑️</button>

      <img src="../uploads/menu/<?= htmlspecialchars($produk['gambar']) ?>" alt="<?= htmlspecialchars($produk['nama_produk']) ?>" style="width: 60px; height: 60px; object-fit: cover; border-radius: 5px;">
      <div class="item-detail" style="flex-grow:1;">
        <strong><?= htmlspecialchars($produk['nama_produk']) ?></strong><br>
        <small>Harga: Rp <?= number_format($produk['harga']) ?></small><br>
        <small>Stok: <?= (int)$produk['stok'] ?></small><br>
        <small>Catatan: <?= htmlspecialchars($item['note']) ?></small><br>
        <small>Subtotal: Rp <?= number_format($subtotal) ?></small><br>
        <small>Jumlah pesan :</small>
        <input type="number" class="input-qty" value="<?= $qty ?>" min="1" style="width: 50px; margin-top: 5px;">
      </div>
    </div>
    <hr style="border-top: dashed 1px #ccc;">
  <?php endforeach; ?>
</div>

<div style="margin-top: 10px; text-align: center;">
  <strong>Total: Rp <?= number_format($grand_total) ?></strong><br><br>

  <!-- ✅ FORM checkout sekarang beneran jalan -->
  <form action="checkout.php" method="POST" id="form-checkout">
    <label>Metode Pembayaran:</label><br>
    <input type="radio" name="metode" value="cash" required> Bayar di kasir<br>
    <input type="radio" name="metode" value="online"> Bayar Online<br><br>

    <button type="submit" style="margin-top: 10px; background-color: #5b3a29; color: white; padding: 6px 10px; border-radius: 5px; border: none; cursor: pointer;">
      ✅ Checkout
    </button>
  </form>

  <button id="btn-update" style="margin-top: 10px; background-color: #5b3a29; color: white; border: none; padding: 6px 10px; border-radius: 5px; cursor:pointer;">
    🔁 Perbarui
  </button>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  // Hapus item
  $(document).on('click', '.btn-hapus', function () {
    const id = $(this).data('id');
    if (!id) {
      alert('ID tidak valid');
      return;
    }

    if (confirm('Yakin ingin menghapus item ini dari keranjang?')) {
      $.ajax({
        type: 'POST',
        url: '../componen/cart_component.php',
        data: { action: 'hapus', id: id },
        success: function (res) {
          if (res.status === 'success') {
            alert('Item dihapus!');
            location.reload();
          } else {
            alert('Gagal menghapus: ' + (res.message ?? 'Tidak diketahui'));
          }
        },
        error: function (xhr, status, err) {
          console.error('AJAX error:', err);
          alert('Terjadi kesalahan saat menghapus item.');
        }
      });
    }
  });

  // Perbarui jumlah
  $(document).on('click', '#btn-update', function () {
    let cartData = [];
    $('.cart-item').each(function () {
      let id = $(this).data('id');
      let qty = $(this).find('.input-qty').val();
      cartData.push({ id: id, qty: qty });
    });

    $.post('../componen/cart_component.php', {
      action: 'update',
      cart: cartData
    }, function (res) {
      if (res.status === 'updated') {
        alert('Keranjang berhasil diperbarui!');
        location.reload();
      } else {
        alert('Gagal memperbarui keranjang!');
      }
    });
  });
</script>

<div style="margin-top: 15px; text-align: center;">
  <a href="cart.php" style="text-decoration: none; color: #5b3a29;">🛒 Lihat Semua</a>
</div>

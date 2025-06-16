<?php
session_start();
include "../koneksi.php";

$cart = $_SESSION['cart'] ?? [];
if (!$cart) {
  echo "<p style='text-align: center; font-family: Poppins, sans-serif; color: #fff; background:#000;'>Keranjang kosong. <a href='pilih_menu.php' style='color:#e3c598;'>Pilih Menu</a></p>";
  exit;
}

$ids = implode(',', array_keys($cart));
$q = mysqli_query($conn, "SELECT * FROM menu WHERE id_menu IN ($ids)");
$menuData = [];
while ($m = mysqli_fetch_assoc($q)) $menuData[$m['id_menu']] = $m;

// Proses update qty
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['qty'])) {
  foreach ($_POST['qty'] as $id => $qty) {
    $qty = (int)$qty;
    if ($qty <= 0) {
      unset($_SESSION['cart'][$id]);
    } else {
      $_SESSION['cart'][$id]['qty'] = $qty;
    }
  }
  header("Location: cart.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Keranjang</title>
  <link rel="stylesheet" href="css/style.css">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #000;
      margin: 0;
      padding: 40px 0;
      color: #fff;
    }

    .cart-container {
      background: #111;
      max-width: 800px;
      margin: auto;
      padding: 30px 40px;
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(255,255,255,0.05);
    }

    h2 {
      text-align: center;
      color: #e3c598;
      margin-bottom: 25px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
      color: #fff;
    }

    th, td {
      padding: 12px 8px;
      border-bottom: 1px solid #333;
      text-align: center;
    }

    th {
      background-color: #222;
      font-weight: 600;
      color: #e3c598;
    }

    input[type="number"] {
      width: 60px;
      padding: 6px;
      text-align: center;
      border: 1px solid #555;
      border-radius: 4px;
      background-color: #000;
      color: #fff;
    }

    .btn {
      background: #5b3a29;
      color: #fff;
      border: none;
      padding: 10px 18px;
      border-radius: 6px;
      cursor: pointer;
      margin: 10px 6px;
      font-weight: bold;
    }

    .btn i {
      vertical-align: middle;
      margin-right: 6px;
    }

    .btn:hover {
      background-color: #8d6246;
    }

    .link-back {
      display: block;
      margin-top: 10px;
      text-align: center;
      color: #e3c598;
      text-decoration: none;
    }

    .link-back:hover {
      text-decoration: underline;
    }

    .total-row td {
      font-weight: bold;
      background: #222;
      color: #e3c598;
    }

    .action-buttons {
      display: flex;
      justify-content: center;
      gap: 20px;
      flex-wrap: wrap;
    }
  </style>
  <script src="https://unpkg.com/feather-icons"></script>
</head>
<body>

<div class="cart-container">
  <h2><i data-feather="shopping-cart"></i> Keranjang Anda</h2>
  <form method="POST" action="cart.php">
    <table>
      <thead>
        <tr>
          <th>Menu</th>
          <th>Harga</th>
          <th>Qty</th>
          <th>Subtotal</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $total = 0;
        foreach ($cart as $id => $item):
          if (!isset($menuData[$id])) continue;
          $row = $menuData[$id];
          $qty = $item['qty'];
          $sub = $row['harga'] * $qty;
          $total += $sub;
        ?>
        <tr>
          <td><?= htmlspecialchars($row['nama_produk']) ?></td>
          <td>Rp <?= number_format($row['harga']) ?></td>
          <td><input type="number" name="qty[<?= $id ?>]" value="<?= $qty ?>" min="1"></td>
          <td>Rp <?= number_format($sub) ?></td>
        </tr>
        <?php endforeach; ?>
        <tr class="total-row">
          <td colspan="3">Total</td>
          <td>Rp <?= number_format($total) ?></td>
        </tr>
      </tbody>
    </table>

    <div class="action-buttons">
      <button type="submit" class="btn"></i>Perbarui Keranjang</button>
      <a href="pilih_menu.php" class="btn"></i>Kembali</a>
      <form method="POST" action="checkout.php" style="display:inline;">
        <button type="submit" name="checkout" onclick="return confirm('Lanjutkan ke Checkout?')" style="background-color: #5b3a29; color: white; padding: 6px 10px; border-radius: 5px; border:none; cursor:pointer;">✅ Checkout</button>
      </form>

    </div>
  </form>
</div>

<script>feather.replace();</script>
</body>
</html>

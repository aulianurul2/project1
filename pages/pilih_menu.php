<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php
session_start();
include "../koneksi.php";

if (isset($_GET['meja'])) {
    $_SESSION['no_meja'] = intval($_GET['meja']);
    $_SESSION['valid'] = true;           // Set validasi scan QR
    $_SESSION['scan_time'] = time();     // Set waktu scan QR
}

if (isset($_GET['hapus'])) {
    $hapus_id = intval($_GET['hapus']);
    if (isset($_SESSION['cart'][$hapus_id])) {
        unset($_SESSION['cart'][$hapus_id]);
        header("Location: pilih_menu.php");
        exit;
    }
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Cek apakah pelanggan sudah scan QR dan sesi belum expired
if (!isset($_SESSION['valid']) || !$_SESSION['valid']) {
    echo "<div style='padding:20px; text-align:center; background:#f8d7da; color:#842029;'>
        <h2>Anda belum melakukan scan QR. Silakan scan QR terlebih dahulu untuk memesan.</h2>
        <a>Scan QR Di Meja Anda</a>
    </div>";
    exit;
}

$scan_time = $_SESSION['scan_time'] ?? 0;
$current_time = time();
$max_duration = 5 * 60; // 30 menit dalam detik

if (($current_time - $scan_time) > $max_duration) {
    unset($_SESSION['valid']);
    unset($_SESSION['scan_time']);
    unset($_SESSION['no_meja']);  // Hapus nomor meja saat expired
    echo "<div style='padding:20px; text-align:center; background:#fff3cd; color:#664d03;'>
        <h2>Waktu pemesanan Anda telah habis. Silakan scan QR ulang untuk memesan lagi.</h2>
        <a>Scan QR Di Meja Anda</a>
    </div>";
    exit;
}


// Jika sampai sini, berarti sesi valid dan belum expired

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_menu'])) {
    $id = intval($_POST['id_menu']);
    $catatan = trim($_POST['catatan'] ?? "");

    if (!isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id] = ['qty' => 1, 'note' => $catatan];
    } else {
        $_SESSION['cart'][$id]['qty'] += 1;
        if ($catatan !== '') {
            $_SESSION['cart'][$id]['note'] = $catatan;
        }
    }

    header("Location: pilih_menu.php");
    exit;
}
?>
</head>
</html>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pilih Menu</title>
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: #000;
      margin: 0;
      padding-top: 80px;
      color: white;
    }
    .container {
      padding: 40px 60px;
      max-width: 1200px;
      margin: auto;
    }
    h2 {
      text-align: center;
      margin-bottom: 30px;
      color: white;
    }
    .menu-grid {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      justify-content: center;
    }
    .card {
      background: white;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      padding: 20px;
      width: 220px;
      text-align: center;
      transition: transform 0.3s;
      color: #333;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }
    .card:hover {
      transform: translateY(-5px);
    }
    .card img {
      width: 100%;
      height: 160px;
      object-fit: cover;
      border-radius: 8px;
    }
    .card h4 {
      margin: 15px 0 8px 0;
      color: #5b3a29;
      font-size: 16px;
    }
    .card p {
      margin: 5px 0;
      font-size: 14px;
    }
    .card form input[type="text"] {
      width: 100%;
      padding: 6px;
      margin-top: 8px;
      border: 1px solid #ccc;
      border-radius: 5px;
      font-size: 13px;
    }
    .card button {
      margin-top: 10px;
      padding: 8px 14px;
      background: #5b3a29;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 13px;
      transition: background-color 0.3s ease;
    }
    .card button:hover:not(:disabled) {
      background: #8d6246;
    }
    .card.habis {
      background-color: #ffe5e5;
      border: 2px solid #ff4d4d;
      opacity: 0.7;
      cursor: not-allowed;
    }
    .card.habis button,
    .card.habis input[type="text"] {
      background-color: #eee;
      cursor: not-allowed;
      pointer-events: none;
    }
    .cart-badge {
      position: absolute;
      top: -5px;
      right: -8px;
      background-color: red;
      color: white;
      border-radius: 50%;
      padding: 2px 6px;
      font-size: 10px;
    }
    .kategori-heading {
      font-size: 24px;
      font-weight: 700;
      color: #f5deb3;
      margin: 30px 0 10px 10px;
      text-transform: uppercase;
      letter-spacing: 1px;
      text-align: center;
    }

    /* ===== RESPONSIVE MOBILE ===== */
    @media (max-width: 768px) {
      .container {
        padding: 20px;
      }
      .menu-grid {
        flex-direction: column;
        align-items: center;
      }
      .card {
        width: 90%;
        padding: 16px;
      }
      .card img {
        height: 130px;
      }
      .card h4 {
        font-size: 15px;
      }
      .card p {
        font-size: 13px;
      }
      .card form input[type="text"] {
        font-size: 12px;
        padding: 5px;
      }
      .card button {
        font-size: 12px;
        padding: 7px 12px;
      }
      .kategori-heading {
        font-size: 20px;
      }
    }
  </style>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;700&display=swap" rel="stylesheet" />

  <!-- Feather Icons -->
  <script src="https://unpkg.com/feather-icons"></script>

  <!-- My Style -->
  <link rel="stylesheet" href="/projek1/assets/css/style.css" />
</head>
<body>

<?php include('layout/navbar.php')?>
<div class="container">
  <h2>Menu yang Tersedia</h2>
  <?php
  $kategori_list = ['makanan', 'minuman'];
  foreach ($kategori_list as $kategori):
      $kategori_esc = mysqli_real_escape_string($conn, $kategori);
      $query = mysqli_query($conn, "SELECT * FROM menu WHERE kategori='$kategori_esc' ORDER BY nama_produk ASC");
      if (mysqli_num_rows($query) > 0):
  ?>
    <h3 class="kategori-heading">Kategori: <?= ucfirst(htmlspecialchars($kategori)) ?></h3>

    <div class="menu-grid">
      <?php while ($menu = mysqli_fetch_assoc($query)):
        $habis = ($menu['stok'] <= 0);
      ?>
        <div class="card <?= $habis ? 'habis' : '' ?>">
          <img src="../uploads/menu/<?= htmlspecialchars($menu['gambar']) ?>"
               alt="Gambar <?= htmlspecialchars($menu['nama_produk']) ?>"
               loading="lazy"
          />
          <h4><?= htmlspecialchars($menu['nama_produk']) ?></h4>
          <p>Rp <?= number_format($menu['harga'], 0, ',', '.') ?></p>
          <p>Stok: <?= (int)$menu['stok'] ?></p>

          <?php if ($habis): ?>
            <p style="color:red; font-weight:bold;">Stok Habis</p>
          <?php endif; ?>

          <form method="POST" action="">
            <input type="hidden" name="id_menu" value="<?= (int)$menu['id_menu'] ?>" />
            <input type="text" name="catatan" placeholder="Catatan" <?= $habis ? 'disabled' : '' ?> autocomplete="off" />
            <button type="submit" <?= $habis ? 'disabled title="Stok habis!"' : '' ?>>Pesan</button>
          </form>
        </div>
      <?php endwhile; ?>
    </div>
  <?php
      endif;
  endforeach;
  ?>
</div>

<script>
  feather.replace();

  const cartBtn = document.querySelector("#shopping-cart-button");
  const cartBox = document.querySelector("#cart-box");
  cartBtn.addEventListener("click", function (e) {
    e.preventDefault();
    cartBox.style.display = (cartBox.style.display === "none" || cartBox.style.display === "") ? "block" : "none";
  });
</script>

<script src="/projek1/assets/js/script.js"></script>
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="Mid-client-roIYIet_30f5wzP9"></script>

</body>
</html>

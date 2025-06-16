<?php
session_start();
include "koneksi.php";
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Cek apakah pelanggan sudah scan QR dan sesi belum expired
// if (!isset($_SESSION['valid']) || !$_SESSION['valid']) {
//     // Belum scan QR
//     echo "<div style='padding:20px; text-align:center; background:#f8d7da; color:#842029;'>
//         <h2>Anda belum melakukan scan QR. Silakan scan QR terlebih dahulu untuk memesan.</h2>
//         <a>Scan QR Di Meja Anda</a>
//     </div>";
//     exit; // hentikan load halaman berikutnya
// }

// $scan_time = $_SESSION['scan_time'] ?? 0;
// $current_time = time();
// $max_duration = 30 * 60; // 30 menit dalam detik

// if (($current_time - $scan_time) > $max_duration) {
//     // Sesi scan sudah expired, hapus session valid dan scan_time
//     unset($_SESSION['valid']);
//     unset($_SESSION['scan_time']);
//     echo "<div style='padding:20px; text-align:center; background:#fff3cd; color:#664d03;'>
//         <h2>Waktu pemesanan Anda telah habis. Silakan scan QR ulang untuk memesan lagi.</h2>
//         <a>Scan QR Di Meja Anda</a>
//     </div>";
//     exit; // hentikan load halaman berikutnya
// }

// Jika sampai sini, berarti sesi valid dan belum expired

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Terrace a</title>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,300;0,400;0,700;1,700&display=swap"
    rel="stylesheet">

  <!-- Feather Icons -->
  <script src="https://unpkg.com/feather-icons"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-somehash..." crossorigin="anonymous" referrerpolicy="no-referrer" />

  <!-- My Style -->
  <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

  <nav class="navbar">
  <a href="#" class="navbar-logo">
    <img src="assets/img/logof.jpg" alt="Logo TerraceA" style="height: 30px; vertical-align: middle;" />
    Terrace<span>A</span>.
  </a>
  <div class="navbar-nav">
    <a href="#home">Beranda</a>
    <a href="pages/pilih_menu.php">Pilih Menu</a>
    <a href="#about">Tentang Kami</a>
   <a href="pages/detail_pesanan.php">Riwayat Pesanan</a>
   <a>Meja: <?= isset($_SESSION['no_meja']) ? htmlspecialchars($_SESSION['no_meja']) : '-' ?></a>

  </div>
  <div class="navbar-extra">
    <a href="#" id="search-button"><i data-feather="search"></i></a>

    <?php $jumlah_keranjang = !empty($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?>
    <a href="#" id="shopping-cart-button" style="position: relative;">
      <i data-feather="shopping-cart"></i>
      <?php if ($jumlah_keranjang > 0): ?>
        <span class="cart-badge"><?= $jumlah_keranjang ?></span>
      <?php endif; ?>
    </a>

    <a href="#" id="hamburger-menu"><i data-feather="menu"></i></a>
  </div>
</nav>
  <!-- Navbar end -->
   

  <!-- Hero Section start -->
  <section class="hero" id="home">
    <!-- <div class="mask-container"> -->
      <main class="content">
        <h1>Mari Nikmati Secangkir <span>Kopi</span></h1>
        <p>Buat pengalaman dan cerita bersama Kami.</p>
      </main>
    </div>
  </section>
  <!-- Hero Section end -->

  <!-- About Section start -->
  <section id="about" class="about">
  <h2><span>Tentang</span> Kami</h2>
  <div class="row">
    <div class="tentang-gambar">
      <img src="/projek1/assets/img/wkp.jpg" alt="Tentang Kami" style="max-width: 100%; border-radius: 10px;">
    </div>
    <div class="content">
      <h2>Tentang Terrace A</h2>
      <p>Warkop Terrace A adalah tempat nongkrong dengan cita rasa nusantara dan suasana klasik modern. Kami hadir untuk menemani waktumu, dari pagi hingga malam.</p>
    </div>
  </div>
</section>

  <!-- About Section end -->

  <!-- Menu Section start -->
  <section class="menu" id="menu">
  <h2><span>Menu</span> Tersedia</h2>
    <div class="menu-list">
      <?php
      $query = mysqli_query($conn, "SELECT * FROM menu");
      while ($menu = mysqli_fetch_assoc($query)) {
        echo "
        <div class='menu-card'>
          <img src='uploads/menu/{$menu['gambar']}' class='menu-card-img' alt='{$menu['nama_produk']}'>
          <h3 class='menu-card-title'>{$menu['nama_produk']}</h3>
          <p class='menu-card-price'>Rp " . number_format($menu['harga']) . "</p>
        </div>
        ";
      }
      ?>
    </div>
  </section>

  </div>
</section>
  <!-- Menu Section end -->

  <!-- Contact Section start -->
  <section id="contact" class="contact">
    <h2><span>Kontak</span> Kami</h2>
    <p><a style="color: white; font-weight: 300;">Jl. Flamboyan, Sukamelang, Kec. Subang, Kabupaten Subang, Jawa Barat 41211</a></p>
    <p><a style="color: white; font-weight: 300;">WhatsApp: <a href="tel:+628977663872" style="color: white; font-weight: 300;">+62 897-7663-872</a></p>
    <p><a style="color: white; font-weight: 300;">Instagram: <a href="https://instagram.com/terracee_a" style="color: white; font-weight:300" target="_blank">@terracee_a</a></p>

    <div class="row">
      <iframe
        src="https://www.google.com/maps/embed?pb=!1m21!1m12!1m3!1d495.46818378471863!2d107.77172731915657!3d-6.553783815967394!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!4m6!3e6!4m0!4m3!3m2!1d-6.553874415117893!2d107.77169245044004!5e0!3m2!1sid!2sid!4v1748952426475!5m2!1sid!2sid" 
        allowfullscreen="" loading="lazy" style="width: 100%; height: 300px; border: 0;" referrerpolicy="no-referrer-when-downgrade" class="map"></iframe>
    </div>
  </section>
  <!-- Contact Section end -->
      <?php
    include "koneksi.php"; // sesuaikan path

    // Proses simpan komentar
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['komen'])) {
      $nama = mysqli_real_escape_string($conn, $_POST['nama_komen']);
      $komentar = mysqli_real_escape_string($conn, $_POST['komentar']);
      $rating = (int) $_POST['rating'];

      if ($nama && $komentar && $rating >= 1 && $rating <= 5) {
        mysqli_query($conn, "INSERT INTO komentar_rating (nama, komentar, rating) VALUES ('$nama', '$komentar', $rating)");
      }
    }

    // Ambil komentar
    $komen = mysqli_query($conn, "SELECT * FROM komentar_rating ORDER BY tanggal DESC LIMIT 10");
    ?>

    <!-- Form Komentar -->
    <section style="padding: 50px; background: #111; color: white;">
      <h2 style="text-align: center;">Komentar & Rating</h2>
      <form method="POST" style="max-width: 600px; margin: auto;">
        <input type="text" name="nama_komen" placeholder="Nama Anda" required style="width:100%;padding:10px;margin:10px 0;">
        <textarea name="komentar" placeholder="Tulis komentar..." required style="width:100%;padding:10px;margin:10px 0;"></textarea>
        <label for="rating">Rating:</label>
        <select name="rating" required style="padding:10px;margin:10px 0;">
          <option value="">Pilih Bintang</option>
          <?php for ($i = 1; $i <= 5; $i++): ?>
            <option value="<?= $i ?>"><?= $i ?> ⭐</option>
          <?php endfor; ?>
        </select>
        <button type="submit" name="komen" style="padding: 10px 20px; background: #c18b5f; border: none; color: white;">Kirim</button>
      </form>

      <!-- Tampilkan Komentar -->
      <div style="margin-top: 30px;">
        <?php while ($row = mysqli_fetch_assoc($komen)): ?>
          <div style="background: #222; padding: 15px; margin-bottom: 10px; border-left: 4px solid #c18b5f;">
            <strong><?= htmlspecialchars($row['nama']) ?></strong> - 
            <?= str_repeat('⭐', $row['rating']) ?>
            <p><?= nl2br(htmlspecialchars($row['komentar'])) ?></p>
            <small style="color: gray;"><?= date("d M Y H:i", strtotime($row['tanggal'])) ?></small>
          </div>
        <?php endwhile; ?>
      </div>
    </section>


  <!-- Footer start -->
  <footer>
    <div class="links">
      <a href="#home">Home</a>
      <a href="#about">Tentang Kami</a>
      <a href="#menu">Menu</a>
      <a href="#contact">Kontak</a>
    </div>

    <div class="credit">
      <p>Created by <a href="">Kelompok 5</a>. | &copy; 2025.</p>
    </div>
  </footer>
  <!-- Footer end -->

  <!-- Modal Box Item Detail start -->
  <div class="modal" id="item-detail-modal">
    <div class="modal-container">
      <a href="#" class="close-icon"><i data-feather="x"></i></a>
      <div class="modal-content">
        <img src="img/products/1.jpg" alt="Product 1">
        <div class="product-content">
          <h3>Product 1</h3>
          <p>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Provident, tenetur cupiditate facilis obcaecati
            ullam maiores minima quos perspiciatis similique itaque, esse rerum eius repellendus voluptatibus!</p>
          <div class="product-stars">
            <i data-feather="star" class="star-full"></i>
            <i data-feather="star" class="star-full"></i>
            <i data-feather="star" class="star-full"></i>
            <i data-feather="star" class="star-full"></i>
            <i data-feather="star"></i>
          </div>
          <div class="product-price">IDR 30K <span>IDR 55K</span></div>
          <a href="#"><i data-feather="shopping-cart"></i> <span>add to cart</span></a>
        </div>
      </div>
    </div>
  </div>
  <!-- Modal Box Item Detail end -->

  <!-- Feather Icons -->
  <script>
    feather.replace()
  </script>

  <!-- My Javascript -->
  <script src="/projek1/assets/js/script.js"></script>
</body>

</html>
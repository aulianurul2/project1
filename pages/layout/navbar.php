<nav class="navbar">
  <a href="#" class="navbar-logo">
    <img src="/projek1/assets/img/logof.jpg" alt="Logo TerraceA" style="height: 30px; vertical-align: middle;" />
    Terrace<span>A</span>.
  </a>
  <div class="navbar-nav">
    <a href="../index.php#home">Beranda</a>
    <a href="/projek1/pages/pilih_menu.php">Pilih Menu</a>
    <a href="../index.php#about">Tentang Kami</a> 
   <a href="/projek1/pages/detail_pesanan.php">Riwayat Pesanan</a>
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

  <div class="search-form" style="display:none;">
    <input type="search" id="search-box" placeholder="Cari menu..." />
    <label for="search-box"><i data-feather="search"></i></label>
  </div>

  <div class="shopping-cart" id="cart-box" style="display:none;">
    <?php include '../componen/cart_component.php'; ?>
  </div>
</nav>

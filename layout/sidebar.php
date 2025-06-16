<?php
$role = $_SESSION['role'] ?? 'guest';
$username = $_SESSION['username'] ?? 'Guest';
?>

 <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
                <div class="sidebar-brand-icon">
                    <img src="../assets/img/pinguin.jpg" alt="Logo" style="width: 50px; height: 50px; border-radius: 50%;">
                </div>
                <div class="sidebar-brand-text mx-3">Terrace A</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item hover">
                <a class="nav-link" href="../admin/index_admin.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>
            <li class="nav-item hover">
                <a class="nav-link" href="../meja/meja.php">
                    <i class="fas fa-fw fa-users"></i>
                    <span>Meja</span></a>
            </li>
            <li class="nav-item hover">
                <a class="nav-link" href="../menu/index.php">
                    <i class="fas fa-fw fa-boxes"></i>
                    <span>Menu</span></a>
            </li>
            <li class="nav-item hover">
                <a class="nav-link" href="../pemesanan/pemesanan.php">
                    <i class="fas fa-fw fa-clipboard-list"></i>
                    <span>Pemesanan</span></a>
            </li>
            <li class="nav-item hover">
                <a class="nav-link" href="../laporan/laporan.php">
                    <i class="fas fa-fw fa-money-check"></i>
                    <span>Laporan</span></a>
            </li>
            <li class="nav-item hover">
                <a class="nav-link" href="../logout.php"class="btn btn-sm btn-danger" onclick="return confirm('apakah benar ingin logout?')">
                    <i class="fas fa-fw fa-money-check"></i>
                    <span>logout</span></a>
            </li>
            <hr class="sidebar-divider my-0">
        </ul>
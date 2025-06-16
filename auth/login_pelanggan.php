<?php
// Optional: session_start(); bisa ditambahkan jika ingin simpan login nantinya
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Login Pelanggan</title>
  <link rel="stylesheet" href="/projek1/assets/css/style.css"> <!-- pastikan ini sudah ada -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;700&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background-color: #000;
      background-image: url('../projek1/img/coffe.jpg'); /* optional jika ada background login */
      background-size: cover;
      background-position: center;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .login-box {
      background: rgba(255, 255, 255, 0.95);
      border-radius: 12px;
      padding: 30px 30px 20px;
      text-align: center;
      box-shadow: 0 4px 20px rgba(0,0,0,0.4);
      width: 320px;
    }

    .login-box img {
      width: 110px;
      height: 110px;
      object-fit: contain;
      margin-bottom: 15px;
    }

    .login-box h2 {
      margin-bottom: 20px;
      color: #5b3a29;
    }

    .login-box input[type="text"],
    .login-box input[type="number"] {
      width: 100%;
      padding: 12px;
      margin-bottom: 12px;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-size: 14px;
    }

    .login-box button {
      width: 100%;
      padding: 10px;
      background-color: #5b3a29;
      border: none;
      border-radius: 6px;
      color: #fff;
      font-weight: bold;
      cursor: pointer;
    }

    .login-box button:hover {
      background-color: #7a543b;
    }

    .admin-link {
      position: absolute;
      top: 20px;
      right: 30px;
      color: white;
      font-size: 14px;
      text-decoration: none;
      font-weight: bold;
    }

    .admin-link:hover {
      text-decoration: underline;
      color: #ffdd95;
    }
  </style>
</head>
<body>
  <a href="login_admin.php" class="admin-link">Login Admin</a>

  <div class="login-box">
    <img src="/projek1/assets/img/logof.jpg" alt="Logo Terrace A">
    <h2>TERRACE <span style="color:#5b3a29;">A</span></h2>
    <form method="POST" action="proses_login.php">
      <input type="text" name="username" placeholder="Nama Anda" required>
      <input type="number" name="no_meja" value="<?= $_GET['meja'] ?? '' ?>" placeholder="No Meja" required>
      <button type="submit">Lanjut</button>
    </form>
  </div>
</body>
</html>

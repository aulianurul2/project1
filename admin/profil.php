<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../login.php");
    exit();
}

include "../koneksi.php";

// Ambil username admin dari session
$username_admin = $_SESSION['admin']['username'];

// Ambil data admin dari database
$query = "SELECT * FROM user WHERE username = '$username_admin'";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

// Proses form jika dikirim
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_username = mysqli_real_escape_string($conn, $_POST['username']);
    $new_password = mysqli_real_escape_string($conn, $_POST['password']);

    if (!empty($new_username) && !empty($new_password)) {
        $update_query = "UPDATE user SET username = '$new_username', password = '$new_password' WHERE username = '$username_admin'";
        if (mysqli_query($conn, $update_query)) {
            // Update session
            $_SESSION['admin']['username'] = $new_username;
            $_SESSION['admin']['password'] = $new_password;

            echo "<script>alert('Profil berhasil diperbarui!'); window.location.href='profil.php';</script>";
            exit();
        } else {
            echo "<script>alert('Gagal memperbarui profil.');</script>";
        }
    } else {
        echo "<script>alert('Username dan Password tidak boleh kosong!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Profil Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Profil Admin</h2>
    <div class="card shadow p-4">
        <form method="POST">
            <div class="form-group">
                <label>Username Baru</label>
                <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($data['username']) ?>" required>
            </div>
            <div class="form-group">
                <label>Password Baru</label>
                <input type="text" name="password" class="form-control" placeholder="Masukkan password baru" value="<?= htmlspecialchars($data['password']) ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="index_admin.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</div>
</body>
</html>

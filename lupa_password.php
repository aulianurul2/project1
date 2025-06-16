<?php
require 'koneksi.php'; // Pastikan koneksi ke database

$msg = "";
$showResetForm = false;
$username = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['cari'])) {
        $username = strip_tags($_POST['username']);
        $stmt = $conn->prepare("SELECT * FROM user WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $showResetForm = true;
        } else {
            $msg = "Username tidak ditemukan.";
        }
        $stmt->close();
    } elseif (isset($_POST['reset'])) {
        $username = strip_tags($_POST['username']);
        $newpass = strip_tags($_POST['pass']); // Ambil dari field form "pass"

        $stmt = $conn->prepare("UPDATE user SET password = ? WHERE username = ?");
        $stmt->bind_param("ss", $newpass, $username);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo "<script>alert('Password berhasil direset.'); window.location='login.php';</script>";
            exit;
        } else {
            $msg = "Gagal reset password.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #6a9dd9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .wrapper-reset {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
            max-width: 400px;
            width: 100%;
            text-align: center;
        }
        h3 {
            color: #333;
            margin-bottom: 20px;
        }
        input {
            width: 100%;
            padding: 12px;
            border: 1.5px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            box-sizing: border-box;
            margin-bottom: 15px;
        }
        button {
            background-color: #007BFF;
            color: white;
            padding: 12px;
            width: 100%;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            cursor: pointer;
            font-weight: bold;
        }
        button:hover {
            background-color: #0056b3;
        }
        .back-link {
            margin-top: 15px;
            font-size: 14px;
        }
        .back-link a {
            color: #007BFF;
            text-decoration: none;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
        .error-msg {
            color: red;
            margin-bottom: 10px;
        }
        .form-group {
            position: relative;
        }
        .toggle-password {
            position: absolute;
            top: 50%;
            right: 12px;
            transform: translateY(-50%);
            cursor: pointer;
            color: #888;
        }
    </style>
</head>
<body>
    <div class="wrapper-reset">
        <h3><i class="fas fa-unlock-alt"></i> Reset Password</h3>

        <?php if ($msg): ?>
            <div class="error-msg"><?= $msg ?></div>
        <?php endif; ?>

        <?php if (!$showResetForm): ?>
            <form method="POST">
                <input type="text" name="username" placeholder="Masukkan Username" required>
                <button type="submit" name="cari">Cari Akun</button>
            </form>
        <?php else: ?>
            <form method="POST">
                <input type="hidden" name="username" value="<?= htmlspecialchars($username) ?>">
                <div class="form-group">
                    <input type="password" name="pass" placeholder="Password Baru" id="password" required>
                    <span class="toggle-password" onclick="togglePassword()">
                        <i class="fas fa-eye" id="iconEye"></i>
                    </span>
                </div>
                <button type="submit" name="reset">Reset Password</button>
            </form>
        <?php endif; ?>

        <div class="back-link">
            <a href="login.php"><i class="fas fa-arrow-left"></i> Kembali ke Login</a>
        </div>
    </div>

    <script>
    function togglePassword() {
        var input = document.getElementById("password");
        var icon = document.getElementById("iconEye");

        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        } else {
            input.type = "password";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        }
    }
    </script>
</body>
</html>

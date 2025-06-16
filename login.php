<?php
@ob_start();
session_start();

if (isset($_POST['proses'])) {
    require 'koneksi.php';

    $username = strip_tags($_POST['user']);
    $password = strip_tags($_POST['pass']);

    $stmt = $conn->prepare("SELECT * FROM user WHERE username = ? AND password = ? AND role_ = 'admin'");

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();

    $result = $stmt->get_result();
    $jumlah = $result->num_rows;

    if ($jumlah > 0) {
        $hasil = $result->fetch_assoc();
        $_SESSION['admin'] = $hasil;
        echo '<script>alert("Login Sukses"); window.location="admin/index_admin.php";</script>';
    } else {
        echo '<script>alert("Login Gagal: Username/Password salah atau bukan admin"); history.go(-1);</script>';
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Admin</title>
    <!-- GANTI INI dengan CDN Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: rgb(110, 153, 217);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .wrapper-login {
            background: #fff;
            padding: 40px 30px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
            max-width: 400px;
            width: 100%;
            text-align: center;
        }
        h4 {
            color: #333;
            margin-bottom: 30px;
        }
        .form-group {
            position: relative;
            margin-bottom: 20px;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 12px 45px 12px 15px;
            border: 1.5px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }
        input[type="text"]:focus, input[type="password"]:focus {
            border-color: #007BFF;
            outline: none;
        }
        .toggle-password {
            position: absolute;
            top: 50%;
            right: 15px;
            transform: translateY(-50%);
            cursor: pointer;
            color: #888;
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
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #0056b3;
        }
        .forgot-password {
            margin-top: 15px;
            font-size: 14px;
        }
        .forgot-password a {
            color: #007BFF;
            text-decoration: none;
        }
        .forgot-password a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="wrapper-login">
        <h4><b>Login Admin</b></h4>
        <form method="POST">
            <div class="form-group">
                <input type="text" name="user" placeholder="Username" required autofocus>
            </div>
            <div class="form-group">
                <input type="password" name="pass" placeholder="Password" id="password" required>
                <span class="toggle-password" onclick="togglePassword()">
                    <i class="fas fa-eye" id="iconEye"></i>
                </span>
            </div>
            <button name="proses" type="submit">
                 login
            </button>
        </form>
        <div class="forgot-password">
            <a href="lupa_password.php">Lupa Password?</a>
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

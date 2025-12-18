<?php
session_start();
require_once "../config/koneksi.php";

// 🔗 Inisialisasi koneksi database lewat class
$db = new DBConnection();
$conn = $db->getConnection();

// Jika form login disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // ✅ Ambil data user + role berdasarkan username
    $query = "SELECT u.*, r.nama_role AS role 
              FROM user u 
              JOIN role r ON u.idrole = r.idrole 
              WHERE u.username='$username' 
              LIMIT 1";

    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $data = $result->fetch_assoc();

        // ✅ Verifikasi password (bcrypt / password_hash)
        if (password_verify($password, $data['password'])) {
            // Simpan sesi login
            $_SESSION['username'] = $data['username'];
            $_SESSION['role'] = $data['role'];
            $_SESSION['iduser'] = $data['iduser'];
            $_SESSION['login_time'] = time();

            // ✅ Arahkan sesuai role
            if ($data['role'] == 'superadmin') {
                header("Location: ../interface/dashboard_superadmin.php");
            } elseif ($data['role'] == 'admin') {
                header("Location: ../interface/dashboard_admin.php");
            } else {
                header("Location: ../interface/index.php");
            }
            exit;
        } else {
            $error = "❌ Password salah! Silakan coba lagi.";
        }
    } else {
        $error = "❌ Username tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login | Sistem Pengadaan Barang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* 🌌 Background gradasi khas palet kamu */
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: radial-gradient(circle at top left, #4B0082, #C13584 40%, #E94057 70%, #F27121 90%, #FFD54F);
            overflow: hidden;
        }

        /* 🔮 Elemen dekorasi seperti bola */
        .bubble {
            position: absolute;
            border-radius: 50%;
            background: rgba(255,255,255,0.08);
            animation: float 8s ease-in-out infinite;
        }
        @keyframes float {
            0%,100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        /* 💫 Kartu login modern */
        .login-card {
            position: relative;
            z-index: 5;
            width: 380px;
            padding: 40px 35px;
            border-radius: 25px;
            background: linear-gradient(145deg, rgba(75, 0, 130, 0.95), rgba(193, 53, 132, 0.85));
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(15px);
            text-align: center;
            color: #fff;
            animation: fadeIn 1s ease-in-out;
        }

        .login-card h2 {
            font-weight: 700;
            color: #FFD54F;
            margin-bottom: 25px;
        }

        .form-label {
            font-weight: 500;
            color: #FFEAA7;
            text-align: left;
            display: block;
        }

        .form-control {
            background: rgba(255,255,255,0.15);
            border: none;
            color: #fff;
            border-radius: 8px;
            padding: 12px;
        }

        .form-control::placeholder {
            color: rgba(255,255,255,0.7);
        }

        .btn-login {
            background: linear-gradient(90deg, #FFD54F, #F27121);
            color: #4B0082;
            font-weight: 600;
            border: none;
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            margin-top: 15px;
            transition: 0.3s;
        }

        .btn-login:hover {
            transform: scale(1.03);
            background: linear-gradient(90deg, #FFF176, #FFB300);
            color: #4B0082;
        }

        .error {
            background-color: rgba(255, 0, 0, 0.2);
            color: #FFD54F;
            padding: 8px;
            border-radius: 6px;
            margin-bottom: 10px;
            font-size: 14px;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ✨ Bubble positions */
        .bubble:nth-child(1){ width:120px; height:120px; top:10%; left:8%; }
        .bubble:nth-child(2){ width:80px; height:80px; bottom:15%; left:15%; }
        .bubble:nth-child(3){ width:100px; height:100px; top:20%; right:12%; }
        .bubble:nth-child(4){ width:150px; height:150px; bottom:10%; right:10%; }
    </style>
</head>
<body>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>

    <div class="login-card">
        <h2>🔐 Login Sistem</h2>
        <?php if (!empty($error)) echo "<div class='error'>$error</div>"; ?>
        <form method="POST" action="">
            <div class="mb-3 text-start">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" placeholder="Masukkan username" required>
            </div>
            <div class="mb-3 text-start">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
            </div>
            <button type="submit" class="btn-login">Masuk</button>
        </form>
    </div>
</body>
</html>

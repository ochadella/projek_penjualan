<?php
session_start();
require_once("../config/koneksi.php"); // koneksi ke DB

// 🔗 buat instance dari class DBConnection dan ambil koneksi mentah
$db = new DBConnection();
$conn = $db->getConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // cek username
    $query = "SELECT * FROM user WHERE username = '$username' LIMIT 1";
    $result = mysqli_query($conn, $query);

    // 🧩 Tambahan untuk cek apakah query error
    if (!$result) {
        die("❌ Query error: " . mysqli_error($conn));
    }

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        // 🧩 Tambahan untuk debug data user & password input
        var_dump($user);
        var_dump($password);
        exit;

        // cek password (jika pakai hash bcrypt)
        if (password_verify($password, $user['password'])) {
            // cek status
            if ($user['status'] == 'Aktif') {
                // simpan data user ke session
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['nama'] = $user['nama'];

                // arahkan ke halaman sesuai role
                if ($user['role'] == 'superadmin') {
                    header("Location: ../views/superadmin/user/DataUser.php");
                } elseif ($user['role'] == 'admin') {
                    header("Location: ../views/admin/barang/DataBarang.php");
                } else {
                    header("Location: ../interface/dashboard.php");
                }
                exit();
            } else {
                $_SESSION['error'] = "Akun tidak aktif!";
            }
        } else {
            $_SESSION['error'] = "Username atau password salah!";
        }
    } else {
        $_SESSION['error'] = "Username tidak ditemukan!";
    }

    header("Location: login.php");
    exit();
}
?>

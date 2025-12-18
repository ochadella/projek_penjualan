<?php
require_once __DIR__ . '/../../../config/koneksi.php';

$db = new DBConnection();
$conn = $db->getConnection();

$message = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'];
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
  $role = $_POST['role'];

  // ✅ Cari ID kosong terkecil
  $result = $conn->query("
    SELECT MIN(t1.iduser + 1) AS next_id
    FROM user t1
    WHERE NOT EXISTS (SELECT 1 FROM user t2 WHERE t2.iduser = t1.iduser + 1)
  ");

  $row = $result->fetch_assoc();
  $newId = $row['next_id'] ?? 1; // Kalau tabel masih kosong, mulai dari 1

  // ✅ Pastikan role valid
  $checkRole = $conn->query("SELECT idrole FROM role WHERE idrole = '$role'");
  if ($checkRole->num_rows === 0) {
    $message = 'Role tidak ditemukan di tabel role!';
  } else {
    // ✅ Tambah user
    $query = "INSERT INTO user (iduser, username, password, idrole, status)
              VALUES ('$newId', '$username', '$password', '$role', 'aktif')";

    if ($conn->query($query)) {
      $message = "User berhasil ditambahkan dengan ID $newId";
      $success = true;
    } else {
      $message = "Gagal menambah user: {$conn->error}";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Tambah User — Superadmin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #4B0082, #C13584, #E94057, #F27121, #FFD54F);
      background-size: 300% 300%;
      animation: gradientFlow 12s ease infinite;
      color: #fff;
      min-height: 100vh;
    }
    @keyframes gradientFlow {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }
    .card {
      background: rgba(43,15,74,0.85);
      border: none;
      border-radius: 16px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.25);
    }
    /* 🌟 Label warna putih */
    label {
      color: #fff;
    }
    .btn-submit {
      background: #FFD54F;
      color: #4B0082;
      font-weight: 600;
      border: none;
      border-radius: 10px;
      padding: 8px 18px;
    }
    .btn-submit:hover { background: #4B0082; color: #fff; }
  </style>
</head>
<body class="d-flex align-items-center justify-content-center">
  <div class="card p-4" style="width:420px;">
    <h3 class="text-center text-warning mb-3">Tambah User</h3>
    <form method="POST">
      <div class="mb-3">
        <label>Username</label>
        <input type="text" name="username" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>Role</label>
        <select name="role" class="form-select">
          <option value="1">Superadmin</option>
          <option value="2">Admin</option>
        </select>
      </div>
      <button type="submit" class="btn-submit w-100">Simpan</button>
      <a href="DataUser.php" class="btn btn-secondary w-100 mt-2">Kembali</a>
    </form>
  </div>

  <?php if ($message): ?>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const alertBox = document.createElement('div');
      alertBox.textContent = <?= json_encode($message) ?>;
      alertBox.style.position = 'fixed';
      alertBox.style.top = '50%';
      alertBox.style.left = '50%';
      alertBox.style.transform = 'translate(-50%, -50%)';
      alertBox.style.padding = '18px 28px';
      alertBox.style.borderRadius = '10px';
      alertBox.style.fontWeight = '600';
      alertBox.style.fontSize = '1rem';
      alertBox.style.zIndex = '9999';
      alertBox.style.transition = 'all 0.4s ease';
      alertBox.style.background = <?= $success ? "'#4caf50'" : "'#f44336'" ?>;
      alertBox.style.color = '#fff';
      alertBox.style.boxShadow = '0 4px 20px rgba(0,0,0,0.3)';
      alertBox.style.textAlign = 'center';
      alertBox.style.maxWidth = '90%';
      alertBox.style.wordWrap = 'break-word';
      document.body.appendChild(alertBox);

      alertBox.style.opacity = '0';
      setTimeout(() => alertBox.style.opacity = '1', 50);
      setTimeout(() => {
        alertBox.style.opacity = '0';
        setTimeout(() => alertBox.remove(), 400);
      }, 3000);
    });
  </script>
  <?php endif; ?>
</body>
</html>

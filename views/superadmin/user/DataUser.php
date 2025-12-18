<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

// panggil koneksi & class
require_once __DIR__ . '/../../../config/koneksi.php';
require_once __DIR__ . '/../../../classes/User.php';

$db = new DBConnection();
$conn = $db->getConnection();

// update data user jika form modal dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
  $id = $_POST['iduser'];
  $username = $_POST['username'];
  $role = $_POST['role'];

  // cek kolom role yang tersedia
  $checkColumn = $conn->query("SHOW COLUMNS FROM user LIKE 'idrole'");
  $roleColumn = ($checkColumn->num_rows > 0) ? 'idrole' : 'role_id';

  $update = $conn->query("UPDATE user SET username='$username', $roleColumn='$role' WHERE iduser='$id'");
  if ($update) {
    echo json_encode(['success' => true, 'message' => '✅ User berhasil diperbarui!']);
    exit;
  } else {
    echo json_encode(['success' => false, 'message' => '❌ Gagal memperbarui user!']);
    exit;
  }
}

// tambah user jika form tambah dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_user'])) {
  $username = $_POST['username'];
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
  $role = $_POST['role'];

  // cari iduser kosong terkecil
  $result = $conn->query("
    SELECT MIN(t1.iduser + 1) AS next_id
    FROM user t1
    WHERE NOT EXISTS (SELECT 1 FROM user t2 WHERE t2.iduser = t1.iduser + 1)
  ");
  $row = $result->fetch_assoc();
  $newId = $row['next_id'] ?? 1;

  // validasi role
  $checkRole = $conn->query("SELECT idrole FROM role WHERE idrole = '$role'");
  if ($checkRole->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => '❌ Role tidak ditemukan di tabel role!']);
    exit;
  }

  // tambah user
  $query = "INSERT INTO user (iduser, username, password, idrole, status)
            VALUES ('$newId', '$username', '$password', '$role', 'aktif')";
  if ($conn->query($query)) {
    echo json_encode(['success' => true, 'message' => '✅ User berhasil ditambahkan!']);
    exit;
  } else {
    echo json_encode(['success' => false, 'message' => "❌ Gagal menambah user: {$conn->error}"]);
    exit;
  }
}

// ambil data user
$user = new User($conn);
$dataUser = $user->tampilUser();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Kelola User — Superadmin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      margin: 0;
      background: linear-gradient(135deg, #4B0082 0%, #C13584 25%, #E94057 50%, #F27121 75%, #FFD54F 100%);
      background-size: 300% 300%;
      animation: gradientFlow 12s ease infinite;
      color: #fff;
      min-height: 100vh;
      overflow-x: hidden;
    }
    @keyframes gradientFlow {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }
    .navbar {
      background: rgba(20, 20, 35, 0.75);
      backdrop-filter: blur(12px);
      box-shadow: 0 3px 12px rgba(0, 0, 0, 0.3);
      padding: 10px 40px;
    }
    .navbar-brand {
      color: #FFD54F !important;
      font-weight: 700;
      font-size: 1.3rem;
    }
    .nav-link {
      color: #FFD54F !important;
      font-weight: 500;
      margin-left: 18px;
      transition: 0.3s;
    }
    .nav-link:hover {
      color: #fff !important;
      background-color: rgba(255, 213, 79, 0.2);
      border-radius: 8px;
    }
    .btn-logout {
      background-color: #FFD54F;
      color: #4B0082;
      font-weight: 600;
      border: none;
      border-radius: 8px;
      padding: 6px 18px;
      transition: 0.3s;
      margin-left: 18px;
    }
    .btn-logout:hover {
      background-color: #4B0082;
      color: #fff;
    }
    .table-container {
      background: rgba(43, 15, 74, 0.8);
      backdrop-filter: blur(12px);
      border-radius: 16px;
      padding: 25px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.25);
    }
    th {
      background-color: rgba(255, 213, 79, 0.2);
      color: #FFD54F;
    }
    .btn-add {
      background-color: #FFD54F;
      color: #4B0082;
      font-weight: 600;
      border: none;
      border-radius: 10px;
      padding: 8px 18px;
      transition: 0.3s;
    }
    .btn-add:hover {
      background-color: #4B0082;
      color: #fff;
    }
    .btn-action {
      border: none;
      border-radius: 8px;
      padding: 6px 10px;
      font-size: 0.9rem;
      margin-right: 4px;
      transition: 0.3s;
    }
    .btn-edit { background-color: #6bc7ff; color: #1a1a1a; }
    .btn-edit:hover { background-color: #39a7ff; color: #fff; }
    .btn-delete { background-color: #ff7b7b; color: #1a1a1a; }
    .btn-delete:hover { background-color: #ff4d4d; color: #fff; }
    .btn-reset { background-color: #ffe066; color: #1a1a1a; }
    .btn-reset:hover { background-color: #ffcc33; color: #fff; }
    .btn-back {
      background-color: #9370DB;
      color: #fff;
      font-weight: 600;
      border: none;
      border-radius: 10px;
      padding: 8px 18px;
      transition: 0.3s;
      margin-right: 10px;
    }
    .btn-back:hover {
      background-color: #7B68EE;
      color: #fff;
    }
    footer { text-align:center; padding:25px; color:#fff7d0; font-size:0.9rem; margin-top:40px;}
  </style>
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
      <a class="navbar-brand" href="../../../interface/dashboard_superadmin.php">👑 Superadmin</a>
      <div class="collapse navbar-collapse justify-content-end">
        <ul class="navbar-nav d-flex align-items-center">
          <li class="nav-item"><a class="nav-link" href="../user/DataUser.php">👥 Kelola User</a></li>
          <li class="nav-item"><a class="nav-link" href="../role/ManajemenRole.php">⚙️ Manajemen Role</a></li>
          <li class="nav-item"><a class="nav-link" href="../sistem/LogAktivitas.php">🧾 Log Aktivitas</a></li>
          <li class="nav-item"><a class="nav-link" href="../laporan/LaporanGlobal.php">📊 Laporan Global</a></li>
          <li class="nav-item"><a href="../../../auth/logout.php" class="btn btn-logout">🚪 Logout</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <h1 class="text-center mt-5"><i class="bi bi-people-fill"></i> Kelola User</h1>
  <p class="lead text-center">Lihat, tambahkan, ubah, atau hapus akun pengguna sistem.</p>

  <div class="container mt-4">
    <div class="table-container">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0 text-warning"><i class="bi bi-person-lines-fill"></i> Daftar User</h5>
        <div>
          <a href="../../../interface/dashboard_superadmin.php" class="btn btn-back">
            <i class="bi bi-arrow-left-circle"></i> Kembali
          </a>
          <button class="btn btn-add" data-bs-toggle="modal" data-bs-target="#tambahUserModal">
            <i class="bi bi-plus-circle"></i> Tambah User
          </button>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-hover align-middle text-center">
          <thead>
            <tr>
              <th>No</th>
              <th>Nama</th>
              <th>Username</th>
              <th>Role</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($dataUser)): ?>
              <?php $no=1; foreach($dataUser as $u): ?>
                <?php
                  $iduser = htmlspecialchars($u['iduser'] ?? '');
                  $username = htmlspecialchars($u['username'] ?? '');
                  $role = htmlspecialchars($u['role'] ?? '');
                  $idrole = htmlspecialchars($u['idrole'] ?? ($u['role_id'] ?? ''));
                ?>
                <tr>
                  <td><?= $no++; ?></td>
                  <td><?= $username; ?></td>
                  <td><?= $username; ?></td>
                  <td><?= $role; ?></td>
                  <td>Aktif</td>
                  <td>
                    <button class="btn-action btn-edit" data-bs-toggle="modal" data-bs-target="#editModal"
                      data-id="<?= $iduser; ?>" data-username="<?= $username; ?>" data-role="<?= $idrole; ?>">
                      <i class="bi bi-pencil-square"></i>
                    </button>
                    <button class="btn-action btn-delete" data-id="<?= $iduser; ?>" data-username="<?= $username; ?>">
                      <i class="bi bi-trash3"></i>
                    </button>
                    <button class="btn-action btn-reset" data-id="<?= $iduser; ?>"><i class="bi bi-arrow-repeat"></i></button>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="6" class="text-center text-warning">Belum ada data user.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Modal Edit -->
  <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content" style="background:rgba(43,15,74,0.95); color:white; border-radius:16px;">
        <form id="formEditUser">
          <div class="modal-header border-0">
            <h5 class="modal-title text-warning">Edit User</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="iduser" id="edit-id">
            <div class="mb-3">
              <label>Username</label>
              <input type="text" name="username" id="edit-username" class="form-control" required>
            </div>
            <div class="mb-3">
              <label>Role</label>
              <select name="role" id="edit-role" class="form-select">
                <option value="1">Superadmin</option>
                <option value="2">Admin</option>
                <option value="3">User</option>
              </select>
            </div>
          </div>
          <div class="modal-footer border-0">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" name="update_user" class="btn btn-warning">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Tambah User -->
  <div class="modal fade" id="tambahUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content" style="background:rgba(43,15,74,0.95); color:white; border-radius:16px;">
        <form id="formTambahUser">
          <div class="modal-header border-0">
            <h5 class="modal-title text-warning">Tambah User</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
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
          </div>
          <div class="modal-footer border-0">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" name="tambah_user" class="btn btn-warning">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <footer>© <?= date('Y'); ?> — Superadmin Panel 💛</footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const editModal = document.getElementById('editModal');
    editModal.addEventListener('show.bs.modal', event => {
      const button = event.relatedTarget;
      document.getElementById('edit-id').value = button.getAttribute('data-id');
      document.getElementById('edit-username').value = button.getAttribute('data-username');
      document.getElementById('edit-role').value = button.getAttribute('data-role');
    });

    // ✅ Edit user AJAX tanpa reload
    document.getElementById('formEditUser').addEventListener('submit', function(e) {
      e.preventDefault();
      const formData = new FormData(this);
      formData.append('update_user', '1');

      fetch('DataUser.php', {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        showAlert(data.message, data.success);
        if (data.success) {
          setTimeout(() => location.reload(), 1500);
          bootstrap.Modal.getInstance(document.getElementById('editModal')).hide();
        }
      })
      .catch(() => showAlert('❌ Terjadi kesalahan koneksi.', false));
    });

    // ✅ Tambah user AJAX tanpa reload
    document.getElementById('formTambahUser').addEventListener('submit', function(e) {
      e.preventDefault();
      const formData = new FormData(this);
      formData.append('tambah_user', '1');

      fetch('DataUser.php', {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        showAlert(data.message, data.success);
        if (data.success) {
          setTimeout(() => location.reload(), 1500);
          bootstrap.Modal.getInstance(document.getElementById('tambahUserModal')).hide();
        }
      })
      .catch(() => showAlert('❌ Terjadi kesalahan koneksi.', false));
    });

    // ✅ Reset password AJAX tanpa reload
    document.querySelectorAll('.btn-reset').forEach(btn => {
      btn.addEventListener('click', () => {
        const id = btn.getAttribute('data-id');
        if (confirm('Yakin ingin mereset password user ini ke default (123456)?')) {
          fetch('ResetPassword.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `id=${id}`
          })
          .then(res => res.json())
          .then(data => showAlert(data.message, data.success))
          .catch(() => alert('Terjadi kesalahan koneksi.'));
        }
      });
    });

    // ✅ Hapus user AJAX tanpa reload
    document.querySelectorAll('.btn-delete').forEach(button => {
      button.addEventListener('click', () => {
        const id = button.getAttribute('data-id');
        const username = button.getAttribute('data-username') || '';
        if (!id) {
          showAlert('⚠️ ID user tidak ditemukan di tombol hapus.', false);
          return;
        }
        if (confirm(`Yakin ingin menghapus user "${username || '(tanpa nama)'}"?`)) {
          fetch('HapusUser.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id=${encodeURIComponent(id)}&username=${encodeURIComponent(username)}`
          })
          .then(res => res.json())
          .then(data => {
            showAlert(data.message, data.success);
            if (data.success) setTimeout(() => location.reload(), 1500);
          })
          .catch(() => showAlert('❌ Terjadi kesalahan koneksi ke server.', false));
        }
      });
    });

    // Fungsi alert tengah layar
    function showAlert(message, success) {
      const alertBox = document.createElement('div');
      alertBox.textContent = message;
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
      alertBox.style.background = success ? '#4caf50' : '#f44336';
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
    }
  </script>
</body>
</html>
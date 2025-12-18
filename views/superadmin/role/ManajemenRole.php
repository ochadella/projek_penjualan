<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../../config/koneksi.php';

$db = new DBConnection();
$conn = $db->getConnection();

/* ===============================
   TAMBAH ROLE
=================================*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_role'])) {
  $nama = trim($_POST['nama']);
  $deskripsi = trim($_POST['deskripsi']);

  header('Content-Type: application/json');
  if ($nama === '') {
    echo json_encode(['success' => false, 'message' => 'Nama role tidak boleh kosong!']);
    exit;
  }

  // ✅ Kolom diperbaiki: nama_role bukan nama
  $stmt = $conn->prepare("INSERT INTO role (nama_role, deskripsi) VALUES (?, ?)");
  $stmt->bind_param('ss', $nama, $deskripsi);
  if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Role berhasil ditambahkan!']);
  } else {
    echo json_encode(['success' => false, 'message' => 'Gagal menambahkan role: ' . $conn->error]);
  }
  exit;
}

/* ===============================
   UPDATE ROLE
=================================*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_role'])) {
  $id = $_POST['idrole'];
  $nama = trim($_POST['nama']);
  $deskripsi = trim($_POST['deskripsi']);

  header('Content-Type: application/json');
  if ($nama === '') {
    echo json_encode(['success' => false, 'message' => 'Nama role tidak boleh kosong!']);
    exit;
  }

  // ✅ Sama, pakai nama_role
  $stmt = $conn->prepare("UPDATE role SET nama_role=?, deskripsi=? WHERE idrole=?");
  $stmt->bind_param('ssi', $nama, $deskripsi, $id);
  if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Role berhasil diperbarui!']);
  } else {
    echo json_encode(['success' => false, 'message' => 'Gagal memperbarui role: ' . $conn->error]);
  }
  exit;
}

/* ===============================
   HAPUS ROLE
=================================*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus_role'])) {
  $id = $_POST['idrole'];
  header('Content-Type: application/json');
  $hapus = $conn->query("DELETE FROM role WHERE idrole='$id'");
  if ($hapus) {
    echo json_encode(['success' => true, 'message' => 'Role berhasil dihapus!']);
  } else {
    echo json_encode(['success' => false, 'message' => 'Gagal menghapus role: ' . $conn->error]);
  }
  exit;
}

/* ===============================
   AMBIL DATA ROLE
=================================*/
$roles = $conn->query("SELECT idrole, nama_role AS nama, deskripsi FROM role ORDER BY idrole ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Manajemen Role — Superadmin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
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
      0% {background-position: 0% 50%;}
      50% {background-position: 100% 50%;}
      100% {background-position: 0% 50%;}
    }
    .navbar {
      background: rgba(20, 20, 35, 0.75);
      backdrop-filter: blur(12px);
      box-shadow: 0 3px 12px rgba(0,0,0,0.3);
      padding: 10px 40px;
    }
    .navbar-brand { color: #FFD54F !important; font-weight: 700; font-size: 1.3rem; }
    .nav-link { color: #FFD54F !important; font-weight: 500; margin-left: 18px; transition: 0.3s; }
    .nav-link:hover { color: #fff !important; background-color: rgba(255,213,79,0.2); border-radius: 8px; }
    .btn-logout { background-color: #FFD54F; color: #4B0082; font-weight: 600; border: none; border-radius: 8px; padding: 6px 18px; transition: 0.3s; margin-left: 18px; }
    .btn-logout:hover { background-color: #4B0082; color: #fff; }
    .table-container {
      background: rgba(43,15,74,0.8);
      backdrop-filter: blur(12px);
      border-radius: 16px;
      padding: 25px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.25);
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
    .btn-add:hover { background-color: #4B0082; color: #fff; }
    .btn-action { border: none; border-radius: 8px; padding: 6px 10px; font-size: 0.9rem; margin-right: 4px; transition: 0.3s; }
    .btn-edit { background-color: #6bc7ff; color: #1a1a1a; }
    .btn-edit:hover { background-color: #39a7ff; color: #fff; }
    .btn-delete { background-color: #ff7b7b; color: #1a1a1a; }
    .btn-delete:hover { background-color: #ff4d4d; color: #fff; }
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

  <h1 class="text-center mt-5"><i class="bi bi-gear-wide-connected"></i> Manajemen Role</h1>
  <p class="lead text-center">Kelola daftar peran pengguna sistem.</p>

  <div class="container mt-4">
    <div class="table-container">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0 text-warning"><i class="bi bi-person-badge-fill"></i> Daftar Role</h5>
        <div>
          <a href="../../../interface/dashboard_superadmin.php" class="btn btn-back">
            <i class="bi bi-arrow-left-circle"></i> Kembali
          </a>
          <button class="btn btn-add" data-bs-toggle="modal" data-bs-target="#tambahRoleModal">
            <i class="bi bi-plus-circle"></i> Tambah Role
          </button>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-hover align-middle text-center">
          <thead>
            <tr>
              <th>ID</th>
              <th>Nama Role</th>
              <th>Deskripsi</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($roles->num_rows > 0): ?>
              <?php while($r = $roles->fetch_assoc()): ?>
                <tr>
                  <td><?= htmlspecialchars($r['idrole']); ?></td>
                  <td><?= htmlspecialchars($r['nama']); ?></td>
                  <td><?= htmlspecialchars($r['deskripsi'] ?? '-'); ?></td>
                  <td>
                    <button class="btn-action btn-edit" data-bs-toggle="modal" data-bs-target="#editRoleModal"
                      data-id="<?= $r['idrole']; ?>"
                      data-nama="<?= htmlspecialchars($r['nama']); ?>"
                      data-deskripsi="<?= htmlspecialchars($r['deskripsi'] ?? ''); ?>">
                      <i class="bi bi-pencil-square"></i>
                    </button>
                    <button class="btn-action btn-delete" data-id="<?= $r['idrole']; ?>">
                      <i class="bi bi-trash3"></i>
                    </button>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr><td colspan="4" class="text-warning">Belum ada role yang terdaftar.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Modal Tambah Role -->
  <div class="modal fade" id="tambahRoleModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content" style="background:rgba(43,15,74,0.95); color:white; border-radius:16px;">
        <form method="POST">
          <div class="modal-header border-0">
            <h5 class="modal-title text-warning">Tambah Role</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label>Nama Role</label>
              <input type="text" name="nama" class="form-control" required>
            </div>
            <div class="mb-3">
              <label>Deskripsi</label>
              <textarea name="deskripsi" class="form-control"></textarea>
            </div>
          </div>
          <div class="modal-footer border-0">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" name="tambah_role" class="btn btn-warning">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Edit Role -->
  <div class="modal fade" id="editRoleModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content" style="background:rgba(43,15,74,0.95); color:white; border-radius:16px;">
        <form method="POST">
          <div class="modal-header border-0">
            <h5 class="modal-title text-warning">Edit Role</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="idrole" id="edit-idrole">
            <div class="mb-3">
              <label>Nama Role</label>
              <input type="text" name="nama" id="edit-nama" class="form-control" required>
            </div>
            <div class="mb-3">
              <label>Deskripsi</label>
              <textarea name="deskripsi" id="edit-deskripsi" class="form-control"></textarea>
            </div>
          </div>
          <div class="modal-footer border-0">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" name="update_role" class="btn btn-warning">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <footer>© <?= date('Y'); ?> — Superadmin Panel 💛</footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const editModal = document.getElementById('editRoleModal');
    editModal.addEventListener('show.bs.modal', event => {
      const button = event.relatedTarget;
      document.getElementById('edit-idrole').value = button.getAttribute('data-id');
      document.getElementById('edit-nama').value = button.getAttribute('data-nama');
      document.getElementById('edit-deskripsi').value = button.getAttribute('data-deskripsi');
    });

    document.querySelectorAll('.btn-delete').forEach(btn => {
      btn.addEventListener('click', () => {
        const id = btn.getAttribute('data-id');
        if (confirm('Yakin ingin menghapus role ini?')) {
          fetch('', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `hapus_role=1&idrole=${id}`
          })
          .then(res => res.json())
          .then(data => {
            showAlert(data.message, data.success);
            if (data.success) setTimeout(() => location.reload(), 1500);
          })
          .catch(() => showAlert('❌ Gagal koneksi ke server.', false));
        }
      });
    });

    document.querySelectorAll('form').forEach(form => {
      form.addEventListener('submit', e => {
        const submitBtn = e.submitter;
        if (submitBtn && (submitBtn.name === 'tambah_role' || submitBtn.name === 'update_role')) {
          e.preventDefault();
          const formData = new FormData(form);
          fetch('', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
              showAlert(data.message, data.success);
              if (data.success) {
                const modal = bootstrap.Modal.getInstance(form.closest('.modal'));
                modal.hide();
                setTimeout(() => location.reload(), 1500);
              }
            })
            .catch(() => showAlert('❌ Gagal koneksi ke server.', false));
        }
      });
    });

    function showAlert(message, success) {
      const alertBox = document.createElement('div');
      alertBox.textContent = message;
      alertBox.style.position = 'fixed';
      alertBox.style.bottom = '50px';
      alertBox.style.left = '50%';
      alertBox.style.transform = 'translateX(-50%)';
      alertBox.style.padding = '16px 28px';
      alertBox.style.borderRadius = '10px';
      alertBox.style.fontWeight = '600';
      alertBox.style.fontSize = '1rem';
      alertBox.style.zIndex = '9999';
      alertBox.style.background = success ? '#4caf50' : '#f44336';
      alertBox.style.color = '#fff';
      alertBox.style.boxShadow = '0 4px 20px rgba(0,0,0,0.3)';
      document.body.appendChild(alertBox);
      setTimeout(() => alertBox.remove(), 2500);
    }
  </script>
</body>
</html>

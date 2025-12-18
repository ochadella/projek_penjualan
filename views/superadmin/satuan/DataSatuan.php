<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../../config/koneksi.php';

$db = new DBConnection();
$conn = $db->getConnection();

$popupMessage = "";

/* ----------------------------------------
   HANDLE TAMBAH SATUAN (MAX ID + 1)
---------------------------------------- */
if (isset($_POST['tambah_satuan'])) {
    $nama   = trim($_POST['nama_satuan'] ?? '');
    $status = isset($_POST['status_satuan']) ? 'Aktif' : 'Nonaktif';

    if ($nama !== '') {

        // ✅ Ambil ID terbesar
        $result = $conn->query("SELECT MAX(idsatuan) AS max_id FROM satuan");
        $data   = $result->fetch_assoc();
        $nextId = ($data['max_id'] ?? 0) + 1;

        // ✅ Insert dengan ID manual
        $stmt = $conn->prepare("INSERT INTO satuan (idsatuan, nama_satuan, status) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $nextId, $nama, $status);
        $stmt->execute();
        $stmt->close();

        $popupMessage = "✅ Satuan berhasil ditambahkan (ID: $nextId)";
    } else {
        $popupMessage = "⚠️ Nama satuan wajib diisi";
    }
}

/* ----------------------------------------
   HANDLE EDIT SATUAN
---------------------------------------- */
if (isset($_POST['edit_satuan'])) {
    $id     = intval($_POST['idsatuan'] ?? 0);
    $nama   = trim($_POST['nama_satuan'] ?? '');
    $status = isset($_POST['status_satuan']) ? 'Aktif' : 'Nonaktif';

    if ($id > 0 && $nama !== '') {
        $stmt = $conn->prepare("UPDATE satuan SET nama_satuan = ?, status = ? WHERE idsatuan = ?");
        $stmt->bind_param("ssi", $nama, $status, $id);
        $stmt->execute();
        $stmt->close();
        $popupMessage = "✏️ Data satuan berhasil diperbarui";
    } else {
        $popupMessage = "⚠️ Data tidak lengkap untuk edit satuan";
    }
}

/* ----------------------------------------
   HANDLE HAPUS SATUAN
---------------------------------------- */
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    if ($id > 0) {
        $conn->query("DELETE FROM satuan WHERE idsatuan = $id");
        $popupMessage = "🗑️ Satuan berhasil dihapus";
    }
}

// Ambil data satuan
$query  = "SELECT * FROM satuan ORDER BY idsatuan ASC";
$satuan = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>⚖️ Data Satuan — Superadmin</title>
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
    .navbar-brand { color:#FFD54F !important; font-weight:700; font-size:1.3rem; }
    .nav-link { color:#FFD54F !important; font-weight:500; margin-left:18px; transition:0.3s; }
    .nav-link:hover { color:#fff !important; background-color:rgba(255,213,79,0.2); border-radius:8px; }
    .btn-logout { background:#FFD54F; color:#4B0082; font-weight:600; border:none; border-radius:8px; padding:6px 18px; transition:0.3s; margin-left:18px; }
    .btn-logout:hover { background:#4B0082; color:#fff; }

    .table-container {
      background: rgba(43,15,74,0.8);
      backdrop-filter: blur(12px);
      border-radius: 16px;
      padding: 25px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.25);
    }

    .btn-add {
      background-color:#FFD54F;
      color:#4B0082;
      font-weight:600;
      border:none;
      border-radius:10px;
      padding:8px 18px;
      transition:0.3s;
    }
    .btn-add:hover { background:#4B0082; color:#fff; }

    .btn-back {
      background-color:#9370DB;
      color:#fff;
      font-weight:600;
      border:none;
      border-radius:10px;
      padding:8px 18px;
      transition:0.3s;
      margin-right:10px;
    }
    .btn-back:hover {
      background-color:#7B68EE;
      color:#fff;
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
          <li class="nav-item"><a class="nav-link" href="../barang/DataBarang.php">📦 Data Barang</a></li>
          <li class="nav-item"><a class="nav-link" href="../vendor/DataVendor.php">🏢 Vendor</a></li>
          <li class="nav-item"><a class="nav-link" href="../pengadaan/DataPengadaan.php">🧾 Pengadaan</a></li>
          <li class="nav-item"><a href="../../../auth/logout.php" class="btn btn-logout">🚪 Logout</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <h1 class="text-center mt-5"><i class="bi bi-basket"></i> Data Satuan</h1>
  <p class="lead text-center">Daftar satuan barang yang digunakan dalam sistem.</p>

  <div class="container mt-4">
    <div class="table-container">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0 text-warning"><i class="bi bi-grid"></i> Daftar Satuan</h5>
        <div>
          <a href="../../../interface/dashboard_superadmin.php" class="btn btn-back">
            <i class="bi bi-arrow-left-circle"></i> Kembali
          </a>
          <button class="btn btn-add" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="bi bi-plus-circle"></i> Tambah Satuan
          </button>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-hover align-middle text-center">
          <thead>
            <tr>
              <th>ID Satuan</th>
              <th>Nama Satuan</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($satuan && $satuan->num_rows > 0): ?>
              <?php while ($row = $satuan->fetch_assoc()): ?>
                <tr>
                  <td><?= htmlspecialchars($row['idsatuan']); ?></td>
                  <td><?= htmlspecialchars($row['nama_satuan']); ?></td>
                  <td><?= htmlspecialchars($row['status']); ?></td>
                  <td>
                    <button
                      class="btn btn-sm btn-warning btn-edit"
                      data-id="<?= $row['idsatuan']; ?>"
                      data-nama="<?= htmlspecialchars($row['nama_satuan'], ENT_QUOTES); ?>"
                      data-status="<?= htmlspecialchars($row['status'], ENT_QUOTES); ?>"
                    >
                      <i class="bi bi-pencil-square"></i> Edit
                    </button>
                    <a href="DataSatuan.php?hapus=<?= $row['idsatuan']; ?>"
                       class="btn btn-sm btn-danger"
                       onclick="return confirm('Yakin ingin menghapus satuan ini?');">
                      <i class="bi bi-trash3"></i> Hapus
                    </a>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr><td colspan="4" class="text-warning">Belum ada data satuan.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Modal Tambah -->
  <div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content text-dark">
        <form method="POST" action="DataSatuan.php">
          <div class="modal-header bg-warning">
            <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle"></i> Tambah Satuan Baru</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <label class="fw-semibold">Nama Satuan</label>
            <input type="text" name="nama_satuan" class="form-control mb-2" placeholder="cth: pcs, kg" required>

            <div class="form-check mt-2">
              <input class="form-check-input" type="checkbox" name="status_satuan" id="status_satuan_tambah" checked>
              <label class="form-check-label fw-semibold" for="status_satuan_tambah">Aktif</label>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" name="tambah_satuan" class="btn btn-success">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Edit -->
  <div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content text-dark">
        <form method="POST" action="DataSatuan.php">
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square"></i> Edit Satuan</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="idsatuan" id="edit_idsatuan">

            <label class="fw-semibold">Nama Satuan</label>
            <input type="text" name="nama_satuan" id="edit_nama_satuan" class="form-control mb-2" required>

            <div class="form-check mt-2">
              <input class="form-check-input" type="checkbox" name="status_satuan" id="edit_status_satuan">
              <label class="form-check-label fw-semibold" for="edit_status_satuan">Aktif</label>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" name="edit_satuan" class="btn btn-primary">Simpan Perubahan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Popup Notification -->
  <?php if ($popupMessage): ?>
  <div class="modal fade show" id="popupModal" tabindex="-1" style="display:block; background-color:rgba(0,0,0,0.6);">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content text-center" style="color:#000;">
        <div class="modal-body py-4">
          <p class="fw-semibold mb-3"><?= htmlspecialchars($popupMessage) ?></p>
          <button class="btn btn-outline-danger" onclick="window.location.href='DataSatuan.php'">Tutup</button>
        </div>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <footer>© <?= date('Y'); ?> — Superadmin Panel 💛</footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const editButtons = document.querySelectorAll('.btn-edit');
      const modalEdit = new bootstrap.Modal(document.getElementById('modalEdit'));

      editButtons.forEach(btn => {
        btn.addEventListener('click', () => {
          document.getElementById('edit_idsatuan').value = btn.dataset.id;
          document.getElementById('edit_nama_satuan').value = btn.dataset.nama;
          document.getElementById('edit_status_satuan').checked = btn.dataset.status === 'Aktif';
          modalEdit.show();
        });
      });
    });
  </script>
</body>
</html>

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../../config/koneksi.php';

$db = new DBConnection();
$conn = $db->getConnection();

$popupMessage = ""; 

/* ----------------------------------------
   HANDLE TAMBAH VENDOR
---------------------------------------- */
if (isset($_POST['tambah_vendor'])) {
    $nama    = trim($_POST['nama_vendor'] ?? '');
    $badan   = trim($_POST['badan_hukum'] ?? '');
    $alamat  = trim($_POST['alamat_vendor'] ?? '');
    $telepon = trim($_POST['telepon_vendor'] ?? '');
    $status  = isset($_POST['status_vendor']) ? 'Aktif' : 'Nonaktif';

    if ($nama !== '' && $badan !== '' && $alamat !== '' && $telepon !== '') {
        $stmt = $conn->prepare("
            INSERT INTO vendor (nama_vendor, badan_hukum, alamat_vendor, telepon_vendor, status)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("sssss", $nama, $badan, $alamat, $telepon, $status);
        $stmt->execute();
        $stmt->close();

        $popupMessage = "✅ Vendor berhasil ditambahkan";
    } else {
        $popupMessage = "⚠️ Semua field wajib diisi";
    }
}

/* ----------------------------------------
   HANDLE EDIT VENDOR
---------------------------------------- */
if (isset($_POST['edit_vendor'])) {
    $id      = intval($_POST['idvendor'] ?? 0);
    $nama    = trim($_POST['nama_vendor'] ?? '');
    $badan   = trim($_POST['badan_hukum'] ?? '');
    $alamat  = trim($_POST['alamat_vendor'] ?? '');
    $telepon = trim($_POST['telepon_vendor'] ?? '');
    $status  = isset($_POST['status_vendor']) ? 'Aktif' : 'Nonaktif';

    if ($id > 0 && $nama !== '' && $badan !== '' && $alamat !== '' && $telepon !== '') {
        $stmt = $conn->prepare("
            UPDATE vendor 
            SET nama_vendor = ?, badan_hukum = ?, alamat_vendor = ?, telepon_vendor = ?, status = ?
            WHERE idvendor = ?
        ");
        $stmt->bind_param("sssssi", $nama, $badan, $alamat, $telepon, $status, $id);
        $stmt->execute();
        $stmt->close();

        $popupMessage = "✏️ Data vendor berhasil diperbarui";
    } else {
        $popupMessage = "⚠️ Data tidak lengkap untuk edit vendor";
    }
}

/* ----------------------------------------
   HANDLE HAPUS VENDOR
---------------------------------------- */
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    if ($id > 0) {
        $conn->query("DELETE FROM vendor WHERE idvendor = $id");
        $popupMessage = "🗑️ Vendor berhasil dihapus";
    }
}

/* ----------------------------------------
   AMBIL DATA VENDOR DARI VIEW
---------------------------------------- */
$query  = "SELECT * FROM view_vendor_admin ORDER BY idvendor ASC";
$vendor = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>🏢 Data Vendor — Superadmin</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
/* ❗ CSS ASLI — TIDAK DIUBAH */
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

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg">
  <div class="container-fluid">
    <a class="navbar-brand">🏢 Superadmin</a>
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

<h1 class="text-center mt-5"><i class="bi bi-building"></i> Data Vendor</h1>
<p class="lead text-center">Daftar vendor pemasok barang dalam sistem.</p>

<div class="container mt-4">
  <div class="table-container">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="mb-0 text-warning"><i class="bi bi-people-fill"></i> Daftar Vendor</h5>
      <div>
        <a href="../../../interface/dashboard_superadmin.php" class="btn btn-back">
          <i class="bi bi-arrow-left-circle"></i> Kembali
        </a>
        <button class="btn btn-add" data-bs-toggle="modal" data-bs-target="#modalTambah">
          <i class="bi bi-plus-circle"></i> Tambah Vendor
        </button>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-hover align-middle text-center">
        <thead>
          <tr>
            <th>ID Vendor</th>
            <th>Nama Vendor</th>
            <th>Badan Hukum</th>
            <th>Alamat</th>
            <th>Telepon</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>

        <tbody>
        <?php if ($vendor && $vendor->num_rows > 0): ?>
          <?php while ($row = $vendor->fetch_assoc()): ?>
          <tr>
            <td><?= $row['idvendor'] ?></td>
            <td><?= htmlspecialchars($row['nama_vendor']) ?></td>
            <td><?= htmlspecialchars($row['badan_hukum']) ?></td>
            <td><?= htmlspecialchars($row['alamat_vendor']) ?></td>
            <td><?= htmlspecialchars($row['telepon_vendor']) ?></td>
            <td><?= htmlspecialchars($row['status']) ?></td>
            <td>
              <button 
                  class="btn btn-sm btn-warning btn-edit"
                  data-id="<?= $row['idvendor'] ?>"
                  data-nama="<?= htmlspecialchars($row['nama_vendor'], ENT_QUOTES) ?>"
                  data-badan="<?= htmlspecialchars($row['badan_hukum'], ENT_QUOTES) ?>"
                  data-alamat="<?= htmlspecialchars($row['alamat_vendor'], ENT_QUOTES) ?>"
                  data-telepon="<?= htmlspecialchars($row['telepon_vendor'], ENT_QUOTES) ?>"
                  data-status="<?= htmlspecialchars($row['status'], ENT_QUOTES) ?>"
                >
                <i class="bi bi-pencil-square"></i> Edit
              </button>

              <a href="DataVendor.php?hapus=<?= $row['idvendor'] ?>" 
                 class="btn btn-sm btn-danger"
                 onclick="return confirm('Yakin ingin menghapus vendor ini?');">
                <i class="bi bi-trash3"></i> Hapus
              </a>
            </td>
          </tr>
          <?php endwhile; ?>

        <?php else: ?>
          <tr><td colspan="7" class="text-warning">Belum ada data vendor.</td></tr>
        <?php endif; ?>
        </tbody>

      </table>
    </div>
  </div>
</div>

<!-- Modal Tambah Vendor -->
<div class="modal fade" id="modalTambah" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content text-dark">
      <form method="POST">
        <div class="modal-header bg-warning">
          <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle"></i> Tambah Vendor Baru</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">

          <label class="fw-semibold">Nama Vendor</label>
          <input type="text" name="nama_vendor" class="form-control mb-2" required>

          <label class="fw-semibold">Badan Hukum</label>
          <select name="badan_hukum" class="form-control mb-2" required>
            <option value="PT">PT</option>
            <option value="CV">CV</option>
            <option value="UD">UD</option>
          </select>

          <label class="fw-semibold">Alamat</label>
          <textarea name="alamat_vendor" class="form-control mb-2" required></textarea>

          <label class="fw-semibold">Telepon</label>
          <input type="text" name="telepon_vendor" class="form-control mb-2" required>

          <div class="form-check mt-2">
            <input class="form-check-input" type="checkbox" name="status_vendor" checked>
            <label class="form-check-label fw-semibold">Aktif</label>
          </div>

        </div>

        <div class="modal-footer">
          <button class="btn btn-success" name="tambah_vendor">Simpan</button>
        </div>

      </form>
    </div>
  </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="modalEdit" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content text-dark">
      <form method="POST">

        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square"></i> Edit Vendor</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">

          <input type="hidden" name="idvendor" id="edit_idvendor">

          <label class="fw-semibold">Nama Vendor</label>
          <input type="text" name="nama_vendor" id="edit_nama" class="form-control mb-2" required>

          <label class="fw-semibold">Badan Hukum</label>
          <select name="badan_hukum" id="edit_badan" class="form-control mb-2" required>
            <option value="PT">PT</option>
            <option value="CV">CV</option>
            <option value="UD">UD</option>
          </select>

          <label class="fw-semibold">Alamat</label>
          <textarea name="alamat_vendor" id="edit_alamat" class="form-control mb-2" required></textarea>

          <label class="fw-semibold">Telepon</label>
          <input type="text" name="telepon_vendor" id="edit_telepon" class="form-control mb-2" required>

          <div class="form-check mt-2">
            <input class="form-check-input" type="checkbox" name="status_vendor" id="edit_status">
            <label class="form-check-label fw-semibold">Aktif</label>
          </div>

        </div>

        <div class="modal-footer">
          <button class="btn btn-primary" name="edit_vendor">Simpan Perubahan</button>
        </div>

      </form>
    </div>
  </div>
</div>

<!-- Popup -->
<!-- Popup -->
<?php if (!empty($popupMessage)): ?>
<div class="modal fade show" style="display:block;background:rgba(0,0,0,0.6);">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center">
      <div class="modal-body">

        <?php 
          // Jika popupMessage hanya emoji → supaya tetap muncul tulisan
          $pesan = trim($popupMessage);
          if ($pesan === "✏️")  $pesan = "Data vendor berhasil diperbarui";
          if ($pesan === "⚠️")  $pesan = "Terjadi kesalahan. Mohon cek kembali.";
          if ($pesan === "🗑️")  $pesan = "Vendor berhasil dihapus";
          if ($pesan === "✏️ Data vendor berhasil diperbarui") $pesan = $pesan;
          if ($pesan === "⚠️ Semua field wajib diisi") $pesan = $pesan;
          if ($pesan === "🗑️ Vendor berhasil dihapus") $pesan = $pesan;
          if ($pesan === "✅ Vendor berhasil ditambahkan") $pesan = $pesan;
        ?>

        <p class="fw-semibold mb-3" style="font-size:1.1rem;color:#000;">
          <?= htmlspecialchars($pesan) ?>
        </p>

        <button class="btn btn-danger" onclick="window.location.href='DataVendor.php'">
          Tutup
        </button>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {

  const modalEdit = new bootstrap.Modal(document.getElementById('modalEdit'));

  document.querySelectorAll('.btn-edit').forEach(btn => {
    btn.addEventListener('click', () => {

      document.getElementById('edit_idvendor').value = btn.dataset.id;
      document.getElementById('edit_nama').value = btn.dataset.nama;
      document.getElementById('edit_badan').value = btn.dataset.badan;
      document.getElementById('edit_alamat').value = btn.dataset.alamat;
      document.getElementById('edit_telepon').value = btn.dataset.telepon;
      document.getElementById('edit_status').checked = btn.dataset.status === 'Aktif';

      modalEdit.show();
    });
  });

});
</script>

<footer>© <?= date('Y'); ?> — Admin Panel 💛</footer>

</body>
</html>

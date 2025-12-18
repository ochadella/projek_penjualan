<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../../config/koneksi.php';
$db = new DBConnection();
$conn = $db->getConnection();

// View Dashboard
$conn->query("
    CREATE OR REPLACE VIEW view_dashboard_superadmin AS
    SELECT 
        (SELECT COUNT(*) FROM barang) AS total_barang,
        (SELECT COUNT(*) FROM vendor) AS total_vendor,
        (SELECT COUNT(*) FROM penjualan) AS total_penjualan
");

// Reorder ID Barang
function reorderBarangID($conn) {
    $conn->query("SET @num := 0");
    $conn->query("UPDATE barang SET idbarang = (@num := @num + 1) ORDER BY idbarang");
}

// Margin aktif
function getActiveMargin($conn) {
    $q = $conn->query("SELECT * FROM margin_penjualan WHERE status = 1 LIMIT 1");
    if ($q && $q->num_rows > 0) {
        return $q->fetch_assoc();
    }
    return null;
}

// Hapus Barang
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $conn->query("DELETE FROM barang WHERE idbarang = $id");
    reorderBarangID($conn);
    header("Location: DataBarang.php");
    exit;
}

$margin = getActiveMargin($conn); 
$persen = $margin ? floatval($margin['persen']) : 0;

$query = "
  SELECT 
      b.idbarang,
      b.nama_barang,
      b.jenis,
      s.nama_satuan,
      b.harga_modal AS harga,
      b.status
  FROM barang b
  JOIN satuan s ON b.idsatuan = s.idsatuan
  ORDER BY b.idbarang ASC
";

$barang = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>📦 Data Barang — Admin</title>
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
    .btn-action {
      border: none;
      border-radius: 8px;
      padding: 5px 10px;
      margin: 0 3px;
      transition: 0.3s;
    }
    .btn-edit { background-color: #FFD54F; color: #4B0082; }
    .btn-edit:hover { background-color: #4B0082; color: #fff; }
    .btn-delete { background-color: #E94057; color: #fff; }
    .btn-delete:hover { background-color: #C13584; }
    footer { text-align:center; padding:25px; color:#fff7d0; font-size:0.9rem; margin-top:40px;}
  </style>
</head>
<body>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
      <a class="navbar-brand" href="../../../interface/dashboard_admin.php">📦 Admin</a>
      <div class="collapse navbar-collapse justify-content-end">
        <ul class="navbar-nav d-flex align-items-center">
          <li class="nav-item"><a class="nav-link" href="../barang/DataBarang.php">🛒 Data Barang</a></li>
          <li class="nav-item"><a class="nav-link" href="../penjualan/DataPenjualan.php">💰 Penjualan</a></li>
          <li class="nav-item"><a class="nav-link" href="../laporan/LaporanBarang.php">📊 Laporan</a></li>
          <li class="nav-item"><a href="../../../auth/logout.php" class="btn btn-logout">🚪 Logout</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <h1 class="text-center mt-5"><i class="bi bi-box-seam"></i> Data Barang</h1>
  <p class="lead text-center">Daftar barang beserta satuan, harga, dan status aktifnya.</p>

  <div class="container mt-4">
    <div class="table-container">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0 text-warning"><i class="bi bi-box2-heart"></i> Daftar Barang</h5>
        <div>
          <a href="../../../interface/dashboard_superadmin.php" class="btn btn-back">
            <i class="bi bi-arrow-left-circle"></i> Kembali
          </a>
          <button class="btn btn-add" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="bi bi-plus-circle"></i> Tambah Barang
          </button>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-hover align-middle text-center">
          <thead>
            <tr>
              <th>ID Barang</th>
              <th>Nama Barang</th>
              <th>Jenis</th>
              <th>Nama Satuan</th>
              <th>Harga</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($barang->num_rows > 0): ?>
              <?php while ($row = $barang->fetch_assoc()): 
                $harga_dasar = floatval($row['harga']);
                $harga_jual = $harga_dasar + ($harga_dasar * $persen / 100);
              ?>
                <tr>
                  <td><?= htmlspecialchars($row['idbarang']); ?></td>
                  <td><?= htmlspecialchars($row['nama_barang']); ?></td>
                  <td><?= htmlspecialchars($row['jenis']); ?></td>
                  <td><?= htmlspecialchars($row['nama_satuan']); ?></td>
                  <td>
                    <span class="text-info">Dasar: Rp <?= number_format($harga_dasar,0,',','.'); ?></span><br>
                    <span class="text-warning">Margin: <?= $persen ?>%</span><br>
                    <span class="fw-bold text-success">Jual: Rp <?= number_format($harga_jual,0,',','.'); ?></span>
                  </td>
                  <td>
                    <span class="badge <?= $row['status'] ? 'bg-success' : 'bg-secondary'; ?>">
                      <?= $row['status'] ? 'Aktif' : 'Nonaktif'; ?>
                    </span>
                  </td>
                  <td>
                    <a href="#" data-id="<?= $row['idbarang']; ?>" class="btn-action btn-edit"><i class="bi bi-pencil-square"></i></a>
                    <a href="DataBarang.php?hapus=<?= $row['idbarang']; ?>" class="btn-action btn-delete" onclick="return confirm('Yakin ingin menghapus barang ini?');"><i class="bi bi-trash3"></i></a>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr><td colspan="7" class="text-warning">Belum ada data barang.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Modal Tambah Barang -->
  <div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content text-dark">
        <div class="modal-header bg-warning">
          <h5 class="modal-title fw-bold">Tambah Barang</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form id="formTambahBarang">
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Nama Barang</label>
              <input type="text" name="nama" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Jenis</label>
              <select class="form-control" name="jenis" required>
                <option value="">-- Pilih Jenis --</option>
                <option value="Elektronik">Elektronik</option>
                <option value="Aksesoris">Aksesoris</option>
                <option value="Furniture">Furniture</option>
                <option value="Alat Tulis">Alat Tulis</option>
                <option value="Komputer">Komputer</option>
                <option value="Lainnya">Lainnya</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Satuan</label>
              <select name="idsatuan" class="form-select" required>
                <option value="" disabled selected>— Pilih Satuan —</option>
                <?php
                $qs = $conn->query("SELECT idsatuan, nama_satuan FROM satuan ORDER BY idsatuan ASC");
                while ($s = $qs->fetch_assoc()):
                ?>
                  <option value="<?= $s['idsatuan']; ?>">
                    <?= htmlspecialchars($s['nama_satuan']); ?>
                  </option>
                <?php endwhile; ?>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Harga</label>
              <input type="number" name="harga" class="form-control" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-warning">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Edit Barang -->
  <div class="modal fade" id="modalEditBarang" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content text-white" style="background:#2a0b45;border:none;border-radius:16px;">
        <div class="modal-header border-0">
          <h5 class="modal-title text-warning fw-bold"><i class="bi bi-pencil-square"></i> Edit Barang</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <form id="formEditBarang">
          <div class="modal-body">
            <input type="hidden" name="idbarang" id="edit_idbarang">
            <div class="mb-3">
              <label class="form-label">Nama Barang</label>
              <input type="text" class="form-control" name="nama" id="edit_nama" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Jenis</label>
              <select class="form-control" name="jenis" id="edit_jenis" required>
                <option value="">-- Pilih Jenis --</option>
                <option value="Elektronik">Elektronik</option>
                <option value="Aksesoris">Aksesoris</option>
                <option value="Furniture">Furniture</option>
                <option value="Alat Tulis">Alat Tulis</option>
                <option value="Komputer">Komputer</option>
                <option value="Lainnya">Lainnya</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Harga</label>
              <input type="number" class="form-control" name="harga" id="edit_harga" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Satuan</label>
              <select name="idsatuan" class="form-select" id="edit_idsatuan" required>
                <option value="">— Pilih Satuan —</option>
                <?php
                $qs2 = $conn->query("SELECT idsatuan, nama_satuan FROM satuan ORDER BY idsatuan ASC");
                while ($s2 = $qs2->fetch_assoc()):
                ?>
                  <option value="<?= $s2['idsatuan']; ?>">
                    <?= htmlspecialchars($s2['nama_satuan']); ?>
                  </option>
                <?php endwhile; ?>
              </select>
            </div>
            <div class="form-check mb-3">
              <input class="form-check-input" type="checkbox" name="status" id="edit_status">
              <label class="form-check-label" for="edit_status">Aktif</label>
            </div>
          </div>
          <div class="modal-footer border-0">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-warning fw-semibold">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <footer>© <?= date('Y'); ?> — Admin Panel 💛</footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <script>
document.addEventListener('DOMContentLoaded', () => {
  const modal = new bootstrap.Modal(document.getElementById('modalEditBarang'));
  const formEdit = document.getElementById('formEditBarang');

  // Event listener untuk tombol edit
  document.querySelectorAll('.btn-edit').forEach(btn => {
    btn.addEventListener('click', e => {
      e.preventDefault();
      const id = btn.dataset.id;
      fetch('GetBarang.php?id=' + id)
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            document.getElementById('edit_idbarang').value = data.barang.idbarang;
            document.getElementById('edit_nama').value = data.barang.nama_barang;
            document.getElementById('edit_jenis').value = data.barang.jenis;
            document.getElementById('edit_harga').value = data.barang.harga;
            document.getElementById('edit_idsatuan').value = data.barang.idsatuan;
            document.getElementById('edit_status').checked = data.barang.status == 1;
            modal.show();
          }
        });
    });
  });

  // Submit form edit
  formEdit.addEventListener('submit', e => {
    e.preventDefault();
    const fd = new FormData(formEdit);
    fetch('ProsesEditBarang.php', { method: 'POST', body: fd })
      .then(res => res.json())
      .then(result => {
        const box = document.createElement('div');
        box.textContent = result.message;
        Object.assign(box.style, {
          position: 'fixed', top: '50%', left: '50%', transform: 'translate(-50%, -50%)',
          background: result.success ? '#4CAF50' : '#E94057', color: '#fff',
          padding: '14px 26px', borderRadius: '8px', fontWeight: '600',
          boxShadow: '0 4px 20px rgba(0,0,0,0.3)', zIndex: '9999'
        });
        document.body.appendChild(box);
        setTimeout(() => box.remove(), 2000);
        if (result.success) setTimeout(() => location.reload(), 1600);
      });
  });

  // FORM TAMBAH
  const formTambah = document.getElementById('formTambahBarang');
  if (formTambah) {
    formTambah.addEventListener('submit', async function(e) {
      e.preventDefault();

      const fd = new FormData(this);
      const submitBtn = this.querySelector('button[type="submit"]');
      const originalText = submitBtn.textContent;

      submitBtn.disabled = true;
      submitBtn.textContent = '⏳ Menyimpan...';

      try {
        const res = await fetch('TambahBarang.php', {
          method: 'POST',
          body: fd
        });

        const result = await res.json();
        showNotification(result.message, result.success);

        if (result.success) {
          formTambah.reset();
          const modalEl = document.getElementById('modalTambah');
          const modalInstance = bootstrap.Modal.getInstance(modalEl);
          if (modalInstance) modalInstance.hide();
          setTimeout(() => window.location.reload(), 1500);
        }

      } catch (err) {
        showNotification('❌ ' + err.message, false);
      } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
      }
    });
  }

  function showNotification(message, isSuccess) {
    const box = document.createElement('div');
    box.textContent = message;
    Object.assign(box.style, {
      position: 'fixed', 
      top: '50%', 
      left: '50%', 
      transform: 'translate(-50%, -50%)',
      background: isSuccess ? '#4CAF50' : '#E94057', 
      color: '#fff',
      padding: '16px 28px', 
      borderRadius: '10px', 
      fontWeight: '600',
      boxShadow: '0 6px 25px rgba(0,0,0,0.4)', 
      zIndex: '9999',
      fontSize: '15px'
    });
    document.body.appendChild(box);
    setTimeout(() => box.remove(), 2500);
  }
});
</script>
</body>
</html>

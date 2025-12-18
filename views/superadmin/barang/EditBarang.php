<?php
require_once __DIR__ . '/../../../config/koneksi.php';

$db = new DBConnection();
$conn = $db->getConnection();

$id = $_GET['id'];
$query = $conn->query("SELECT * FROM barang WHERE idbarang='$id'");
$data = $query->fetch_assoc();

$message = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama = $_POST['nama'];
  $jenis = $_POST['jenis'];   // ⬅️ FIELD BARU
  $idsatuan = $_POST['idsatuan'];
  $harga = $_POST['harga'];
  $status = $_POST['status'];

  $update = $conn->query("
      UPDATE barang 
      SET nama='$nama', jenis='$jenis', idsatuan='$idsatuan', harga='$harga', status='$status' 
      WHERE idbarang='$id'
  ");

  if ($update) {
    $message = 'Barang berhasil diupdate!';
    $success = true;
  } else {
    $message = 'Gagal mengupdate barang!';
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Edit Barang — Superadmin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      font-family:'Poppins',sans-serif;
      background:linear-gradient(135deg,#4B0082,#C13584,#E94057,#F27121,#FFD54F);
      background-size:300% 300%;
      animation:gradientFlow 12s ease infinite;
      color:#fff;
      min-height:100vh;
    }
    @keyframes gradientFlow {
      0%{background-position:0% 50%}
      50%{background-position:100% 50%}
      100%{background-position:0% 50%}
    }
    .card {
      background:rgba(43,15,74,0.8);
      backdrop-filter:blur(12px);
      border:none;
      border-radius:16px;
      box-shadow:0 4px 15px rgba(0,0,0,0.25);
    }
    .btn-submit {
      background:#FFD54F;
      color:#4B0082;
      font-weight:600;
      border:none;
      border-radius:10px;
      padding:8px 18px;
    }
    .btn-submit:hover {
      background:#4B0082;
      color:#fff;
    }
    label {
      color: #fff;
    }
  </style>
</head>
<body class="d-flex align-items-center justify-content-center">
  <div class="card p-4" style="width:420px;">
    <h3 class="text-center text-warning mb-3">Edit Barang</h3>
    <form method="POST">
      
      <div class="mb-3">
        <label>Nama Barang</label>
        <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($data['nama']); ?>" required>
      </div>

      <!-- ✅ FIELD JENIS BARANG YANG HILANG -->
      <div class="mb-3">
        <label>Jenis Barang</label>
        <input type="text" name="jenis" class="form-control" value="<?= htmlspecialchars($data['jenis']); ?>" required>
      </div>

      <div class="mb-3">
        <label>Satuan</label>
        <select name="idsatuan" class="form-select" required>
          <?php
          $satuan = $conn->query("SELECT * FROM satuan");
          while ($row = $satuan->fetch_assoc()):
          ?>
            <option value="<?= $row['idsatuan']; ?>" <?= $row['idsatuan'] == $data['idsatuan'] ? 'selected' : ''; ?>>
              <?= htmlspecialchars($row['nama_satuan']); ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="mb-3">
        <label>Harga</label>
        <input type="number" name="harga" class="form-control" value="<?= htmlspecialchars($data['harga']); ?>" required>
      </div>

      <div class="mb-3">
        <label>Status</label>
        <select name="status" class="form-select">
          <option value="aktif" <?= $data['status']=='aktif'?'selected':''; ?>>Aktif</option>
          <option value="nonaktif" <?= $data['status']=='nonaktif'?'selected':''; ?>>Nonaktif</option>
        </select>
      </div>

      <button type="submit" class="btn-submit w-100">Update</button>
      <a href="DataBarang.php" class="btn btn-secondary w-100 mt-2">Kembali</a>
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

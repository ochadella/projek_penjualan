<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Redirect target
$redirect_url = 'DataMargin.php';

require_once __DIR__ . '/../../../config/koneksi.php';
$db = new DBConnection();
$conn = $db->getConnection();

if (!$conn) {
    $_SESSION['alert'] = ["❌ Koneksi database gagal!", "danger"];
    header("Location: $redirect_url");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $persen = floatval($_POST['persen'] ?? 0);
    $tipe_kebijakan = $_POST['tipe_kebijakan'] ?? '';
    $idtarget = intval($_POST['idtarget'] ?? 0);
    $status = intval($_POST['status'] ?? 0);

    $iduser = 1; // ganti sesuai session login

    // ---- VALIDASI ----
    if ($persen <= 0 || empty($tipe_kebijakan)) {
        $_SESSION['alert'] = ["⚠️ Persentase margin & tipe kebijakan wajib diisi!", "warning"];
        header("Location: $redirect_url");
        exit;
    }

    // VALIDASI TARGET
    if ($tipe_kebijakan === 'kategori') {
        $cek = $conn->query("SELECT idkategori FROM kategori WHERE idkategori=$idtarget");
        if ($cek->num_rows == 0) {
            $_SESSION['alert'] = ["❌ Kategori tidak ditemukan!", "danger"];
            header("Location: $redirect_url");
            exit;
        }
    }
    if ($tipe_kebijakan === 'vendor') {
        $cek = $conn->query("SELECT idvendor FROM vendor WHERE idvendor=$idtarget");
        if ($cek->num_rows == 0) {
            $_SESSION['alert'] = ["❌ Vendor tidak ditemukan!", "danger"];
            header("Location: $redirect_url");
            exit;
        }
    }
    if ($tipe_kebijakan === 'global') {
        $idtarget = 0;
    }

    // ===============================
    // ✅ HANYA SATU MARGIN BOLEH AKTIF
    // ===============================
    if ($status == 1) {
        // Nonaktifkan semua margin yang sudah ada
        $conn->query("UPDATE margin_penjualan SET status = 0");
    }

    // ===============================
    // INSERT DATA
    // ===============================
    $sql = "INSERT INTO margin_penjualan 
            (persen, tipe_kebijakan, idtarget, iduser, status, created_at, updated_at)
            VALUES ($persen, '$tipe_kebijakan', $idtarget, $iduser, $status, NOW(), NOW())";

    if ($conn->query($sql)) {
        $_SESSION['alert'] = ["✅ Kebijakan Margin berhasil ditambahkan!", "success"];
    } else {
        $_SESSION['alert'] = ["❌ Gagal menambahkan kebijakan: " . $conn->error, "danger"];
    }

    header("Location: $redirect_url");
    exit;
}

// ========================
// BAGIAN FORM
// ========================

// Ambil data kategori
$queryKategori = "SELECT idkategori, nama_kategori FROM kategori WHERE status = 1 ORDER BY nama_kategori ASC";
$resultKategori = $conn->query($queryKategori);

// Ambil data vendor
$queryVendor = "SELECT idvendor, nama_vendor FROM vendor WHERE status = 1 ORDER BY nama_vendor ASC";
$resultVendor = $conn->query($queryVendor);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>➕ Tambah Kebijakan Margin — Superadmin</title>
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
      padding-top: 50px;
      padding-bottom: 50px;
    }
    @keyframes gradientFlow {
      0% {background-position: 0% 50%;}
      50% {background-position: 100% 50%;}
      100% {background-position: 0% 50%;}
    }

    .form-container {
      background: rgba(43,15,74,0.9);
      backdrop-filter: blur(10px);
      border-radius: 16px;
      padding: 40px;
      box-shadow: 0 8px 30px rgba(0,0,0,0.4);
      max-width: 700px;
      margin: auto;
    }

    .form-label {
      font-weight: 600;
      color: #FFD54F;
    }

    .form-control, .form-select {
      background-color: rgba(255, 255, 255, 0.1);
      border: 1px solid #C13584;
      color: #fff;
      transition: 0.3s;
    }
    .form-control:focus, .form-select:focus {
      background-color: rgba(255, 255, 255, 0.2);
      border-color: #FFD54F;
      box-shadow: 0 0 0 0.25rem rgba(255, 213, 79, 0.4);
      color: #fff;
    }
    .form-control::placeholder {
      color: rgba(255, 255, 255, 0.5);
    }
    .form-select option {
      background-color: #4B0082;
      color: #fff;
    }

    .btn-submit {
      background-color: #E94057;
      color: #fff;
      font-weight: 600;
      border: none;
      border-radius: 10px;
      padding: 10px 25px;
      transition: 0.3s;
    }
    .btn-submit:hover {
      background-color: #C13584;
      color: #fff;
    }

    .btn-cancel {
      background-color: #9370DB;
      color: #fff;
      font-weight: 600;
      border: none;
      border-radius: 10px;
      padding: 10px 25px;
      transition: 0.3s;
    }
    .btn-cancel:hover {
      background-color: #7B68EE;
      color: #fff;
    }

    .form-check-input:checked {
      background-color: #FFD54F;
      border-color: #FFD54F;
    }
  </style>
</head>
<body>

  <div class="form-container">
    <h3 class="text-center mb-4 text-warning"><i class="bi bi-plus-circle"></i> Tambah Kebijakan Margin Baru</h3>
    
    <?php if (isset($_SESSION['alert'])): ?>
      <div class="alert alert-<?= $_SESSION['alert'][1] ?> alert-dismissible fade show" role="alert">
        <?= $_SESSION['alert'][0] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      <?php unset($_SESSION['alert']); ?>
    <?php endif; ?>

    <form method="POST" action="TambahMargin.php">
      
      <div class="mb-3">
        <label class="form-label">Persentase Margin (%)</label>
        <input type="number" step="0.01" min="0.01" max="100" class="form-control" name="persen" placeholder="Contoh: 15.00" required>
        <small class="text-light">Masukkan persentase keuntungan yang diinginkan</small>
      </div>

      <div class="mb-3">
        <label class="form-label">Tipe Kebijakan Margin</label>
        <select class="form-select" id="tipe_kebijakan" name="tipe_kebijakan" required onchange="toggleTarget()">
          <option value="">-- Pilih Tipe Kebijakan --</option>
          <option value="global">Global / Default (Semua Barang)</option>
          <option value="kategori">Berdasarkan Kategori Barang</option>
          <option value="vendor">Berdasarkan Vendor Pemasok</option>
        </select>
      </div>

      <div id="target-container" class="mb-3" style="display:none;">
        <label class="form-label">Pilih Target</label>
        <select class="form-select" id="idtarget" name="idtarget">
          <option value="0">-- Pilih Target --</option>
        </select>
      </div>

      <div class="mb-4">
        <label class="form-label">Status Kebijakan</label>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="status" id="statusAktif" value="1" checked>
          <label class="form-check-label text-success fw-bold" for="statusAktif">
            ✅ Aktif (Langsung Diterapkan ke Harga Jual)
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="status" id="statusNonaktif" value="0">
          <label class="form-check-label text-light" for="statusNonaktif">
            ⚪ Nonaktif (Hanya Draft, Tidak Dipakai)
          </label>
        </div>
        <small class="text-warning d-block mt-2">
          ⚠️ <strong>Penting:</strong> Jika diaktifkan, margin lain yang aktif akan otomatis dinonaktifkan!
        </small>
      </div>

      <div class="d-flex justify-content-between">
        <a href="<?= $redirect_url ?>" class="btn btn-cancel">
          <i class="bi bi-x-circle"></i> Batal
        </a>
        <button type="submit" class="btn btn-submit">
          <i class="bi bi-save"></i> Simpan Kebijakan
        </button>
      </div>

    </form>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const kategoriOptions = `<?php 
        $kategori_html = '<option value="0">-- Pilih Kategori --</option>';
        if ($resultKategori && $resultKategori->num_rows > 0) {
            while ($row = $resultKategori->fetch_assoc()) {
                $kategori_html .= '<option value="'.$row['idkategori'].'">'.htmlspecialchars($row['nama_kategori']).'</option>';
            }
        } else {
            $kategori_html .= '<option value="" disabled>Belum ada kategori aktif</option>';
        }
        echo $kategori_html;
    ?>`;

    const vendorOptions = `<?php 
        $vendor_html = '<option value="0">-- Pilih Vendor --</option>';
        if ($resultVendor && $resultVendor->num_rows > 0) {
            while ($row = $resultVendor->fetch_assoc()) {
                $vendor_html .= '<option value="'.$row['idvendor'].'">'.htmlspecialchars($row['nama_vendor']).'</option>';
            }
        } else {
            $vendor_html .= '<option value="" disabled>Belum ada vendor aktif</option>';
        }
        echo $vendor_html;
    ?>`;

    function toggleTarget() {
        const tipe = document.getElementById('tipe_kebijakan').value;
        const container = document.getElementById('target-container');
        const select = document.getElementById('idtarget');

        // Reset display
        container.style.display = 'none';
        select.innerHTML = '<option value="0">-- Pilih Target --</option>';
        select.removeAttribute('required');

        if (tipe === 'kategori') {
            container.style.display = 'block';
            select.innerHTML = kategoriOptions;
            select.setAttribute('required', 'required');
        }
        else if (tipe === 'vendor') {
            container.style.display = 'block';
            select.innerHTML = vendorOptions;
            select.setAttribute('required', 'required');
        }
    }
  </script>
</body>
</html>
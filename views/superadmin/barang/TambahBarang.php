<?php
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Kirim header JSON dulu
header('Content-Type: application/json; charset=utf-8');

try {
    require_once __DIR__ . '/../../../config/koneksi.php';

    $db = new DBConnection();
    $conn = $db->getConnection();

    if (!$conn) {
        throw new Exception('Koneksi database gagal: ' . mysqli_connect_error());
    }

    // ✅ Hanya boleh POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Akses tidak valid!');
    }

    // ✅ Ambil value dari form
    $nama_barang  = trim($_POST['nama'] ?? '');
    $jenis        = trim($_POST['jenis'] ?? '');
    $idsatuan     = intval($_POST['idsatuan'] ?? 0);
    $harga_modal  = intval(str_replace(['.', ',', 'Rp', ' '], '', $_POST['harga'] ?? '0'));

    $status = 1;
    $jumlah = 1;

    // ✅ Sanitasi
    $nama_barang = $conn->real_escape_string($nama_barang);
    $jenis       = $conn->real_escape_string($jenis);

    // ✅ Validasi input
    if ($nama_barang === '') {
        throw new Exception('⚠️ Nama barang tidak boleh kosong!');
    }

    if ($jenis === '') {
        throw new Exception('⚠️ Jenis barang tidak boleh kosong!');
    }

    if ($idsatuan <= 0) {
        throw new Exception('⚠️ Pilih satuan yang valid!');
    }

    if ($harga_modal <= 0) {
        throw new Exception('⚠️ Harga harus lebih dari 0!');
    }

    // ✅ Cek idsatuan valid di tabel satuan
    $stmtCek = $conn->prepare("SELECT idsatuan FROM satuan WHERE idsatuan = ?");
    if (!$stmtCek) {
        throw new Exception('Error prepare cek satuan: ' . $conn->error);
    }
    
    $stmtCek->bind_param("i", $idsatuan);
    $stmtCek->execute();
    $stmtCek->store_result();

    if ($stmtCek->num_rows === 0) {
        $stmtCek->close();
        throw new Exception('❌ ID Satuan tidak valid!');
    }
    $stmtCek->close();

    // ✅ Dapatkan ID terbesar + 1
    $q = $conn->query("SELECT IFNULL(MAX(idbarang), 0) + 1 AS next_id FROM barang");
    $row = $q->fetch_assoc();
    $next_id = intval($row['next_id']);

    // ✅ INSERT sesuai kolom database (pakai idbarang manual)
    $stmt = $conn->prepare("
        INSERT INTO barang (idbarang, jenis, nama_barang, idsatuan, status, harga_modal)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    if (!$stmt) {
        throw new Exception('Error prepare insert: ' . $conn->error);
    }

    // ✅ Parameter sesuai urutan
    $stmt->bind_param("issiii", $next_id, $jenis, $nama_barang, $idsatuan, $status, $harga_modal);

    // ✅ Eksekusi
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => '✅ Barang berhasil ditambahkan!',
            'id' => $next_id
        ], JSON_UNESCAPED_UNICODE);
        $stmt->close();
    } else {
        throw new Exception('Gagal menambahkan barang: ' . $stmt->error);
    }

} catch (Exception $e) {
    // ✅ Tangkap semua error dan kirim sebagai JSON
    echo json_encode([
        'success' => false,
        'message' => '❌ ' . $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ], JSON_UNESCAPED_UNICODE);
} catch (Error $e) {
    // ✅ Tangkap PHP fatal error
    echo json_encode([
        'success' => false,
        'message' => '❌ PHP Error: ' . $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ], JSON_UNESCAPED_UNICODE);
}
?>

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../../config/koneksi.php';
session_start();

$db = new DBConnection();
$conn = $db->getConnection();

header('Content-Type: application/json');

// Ambil data utama
$iduser = intval($_POST['iduser'] ?? $_SESSION['user_id'] ?? $_SESSION['iduser'] ?? 0);
$ppn = intval($_POST['ppn'] ?? 0);
$total_nilai = intval($_POST['total_nilai'] ?? 0);
$subtotal_nilai = $total_nilai - $ppn;
$timestamp = date('Y-m-d H:i:s');

// Validasi
if ($iduser == 0) {
    echo json_encode(['success' => false, 'message' => 'User tidak valid. Silakan login ulang.']);
    exit;
}

if (!isset($_POST['barang']) || count($_POST['barang']) == 0) {
    echo json_encode(['success' => false, 'message' => 'Detail barang tidak boleh kosong']);
    exit;
}

// Ambil margin aktif
$marginResult = $conn->query("
    SELECT idmargin_penjualan 
    FROM margin_penjualan 
    WHERE status = 1 
    ORDER BY updated_at DESC 
    LIMIT 1
");

if (!$marginResult || $marginResult->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Margin penjualan belum diatur']);
    exit;
}

$margin = $marginResult->fetch_assoc();
$idmargin = $margin['idmargin_penjualan'];

// Mulai transaksi
$conn->begin_transaction();

try {
    // ✅ 1️⃣ INSERT KE TABEL PENJUALAN (HEADER)
    $queryPenjualan = "
        INSERT INTO penjualan (created_at, subtotal_nilai, ppn, total_nilai, iduser, idmargin_penjualan)
        VALUES (?, 0, ?, 0, ?, ?)
    ";
    
    $stmt = $conn->prepare($queryPenjualan);
    $stmt->bind_param("siii", $timestamp, $ppn, $iduser, $idmargin);
    
    if (!$stmt->execute()) {
        throw new Exception($stmt->error);
    }
    
    $idpenjualan = $conn->insert_id; // ✅ ID untuk detail
    $stmt->close();
    
    // ✅ 2️⃣ INSERT DETAIL BARANG
    $insertedCount = 0;
    
    foreach ($_POST['barang'] as $item) {
        $idbarang = intval($item['idbarang'] ?? 0);
        $jumlah = intval($item['jumlah'] ?? 0);
        
        if ($idbarang == 0 || $jumlah == 0) continue;
        
        // Cek stok tersedia
        $stokCheck = $conn->query("
            SELECT IFNULL((
                SELECT stock 
                FROM kartu_stok 
                WHERE idbarang = $idbarang 
                ORDER BY idkartu_stok DESC 
                LIMIT 1
            ), 0) as stok
        ");
        
        if (!$stokCheck) {
            throw new Exception('Gagal cek stok barang ID ' . $idbarang);
        }
        
        $stokRow = $stokCheck->fetch_assoc();
        $stok = intval($stokRow['stok'] ?? 0);
        
        if ($stok < $jumlah) {
            $barangInfo = $conn->query("SELECT nama_barang FROM barang WHERE idbarang = $idbarang")->fetch_assoc();
            throw new Exception("Stok tidak mencukupi untuk " . ($barangInfo['nama_barang'] ?? 'Barang ID ' . $idbarang) . ". Tersedia: $stok");
        }
        
        // Insert detail (harga_satuan dan subtotal akan diisi oleh TRIGGER)
        // PENTING: Sesuaikan nama kolom FK dengan struktur tabel Anda
        // Kemungkinan: idpenjualan, penjualan_idpenjualan, atau id_penjualan
        $queryDetail = "
            INSERT INTO detail_penjualan (harga_satuan, jumlah, subtotal, idpenjualan, idbarang)
            VALUES (0, ?, 0, ?, ?)
        ";
        
        $stmtDetail = $conn->prepare($queryDetail);
        $stmtDetail->bind_param("iii", $jumlah, $idpenjualan, $idbarang);
        
        if (!$stmtDetail->execute()) {
            throw new Exception($stmtDetail->error);
        }
        
        $stmtDetail->close();
        $insertedCount++;
    }
    
    if ($insertedCount == 0) {
        throw new Exception('Tidak ada barang yang berhasil ditambahkan');
    }
    
    // Commit transaksi
    $conn->commit();
    $conn->close();
    
    // ✅ 3️⃣ RESPON BERHASIL
    echo json_encode([
        'success' => true, 
        'message' => "Penjualan berhasil dibuat! $insertedCount barang ditambahkan.",
        'idpenjualan' => $idpenjualan
    ]);
    exit;
    
} catch (Exception $e) {
    // Rollback jika error
    $conn->rollback();
    $conn->close();
    
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage()
    ]);
    exit;
}
?>
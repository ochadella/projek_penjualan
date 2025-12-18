<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../../config/koneksi.php';

header('Content-Type: application/json');

$db = new DBConnection();
$conn = $db->getConnection();

if (!$conn) {
    echo json_encode([
        'success' => false,
        'message' => 'Koneksi database gagal!'
    ]);
    exit;
}

try {
    $idbarang = $_GET['idbarang'] ?? 0;
    
    if (!$idbarang) {
        throw new Exception('ID Barang tidak valid');
    }
    
    // CALL FUNCTION fn_get_harga_jual (sudah termasuk margin!)
    $result = $conn->query("SELECT fn_get_harga_jual($idbarang) as harga_jual");
    
    if (!$result) {
        throw new Exception('Gagal mengambil harga jual: ' . $conn->error);
    }
    
    $row = $result->fetch_assoc();
    $hargaJual = $row['harga_jual'] ?? 0;
    
    // Ambil stok tersedia
    $stokResult = $conn->query("
        SELECT IFNULL((
            SELECT stock 
            FROM kartu_stok 
            WHERE idbarang = $idbarang 
            ORDER BY idkartu_stok DESC 
            LIMIT 1
        ), 0) as stok
    ");
    
    $stokRow = $stokResult->fetch_assoc();
    $stok = $stokRow['stok'] ?? 0;
    
    echo json_encode([
        'success' => true,
        'harga_jual' => $hargaJual,
        'stok_tersedia' => $stok
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
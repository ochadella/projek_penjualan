<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../../config/koneksi.php';
$db = new DBConnection();
$conn = $db->getConnection();

header('Content-Type: application/json');

// ✅ Ambil data utama
$idpengadaan = intval($_POST['idpengadaan'] ?? 0);
$iduser      = intval($_POST['iduser'] ?? 0);
$created_at  = date('Y-m-d H:i:s');
$status      = '1';

// ✅ Validasi
if ($idpengadaan == 0 || $iduser == 0) {
    echo json_encode(['success' => false, 'message' => 'Pengadaan atau User tidak valid']);
    exit;
}

if (!isset($_POST['barang']) || count($_POST['barang']) == 0) {
    echo json_encode(['success' => false, 'message' => 'Detail barang tidak boleh kosong']);
    exit;
}

/* ✅ 1️⃣ INSERT PENERIMAAN */
$queryPenerimaan = "
    INSERT INTO penerimaan (created_at, status, idpengadaan, iduser)
    VALUES (?, ?, ?, ?)
";

$stmt = $conn->prepare($queryPenerimaan);
$stmt->bind_param("ssii", $created_at, $status, $idpengadaan, $iduser);

if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => $stmt->error]);
    exit;
}

$idpenerimaan = $conn->insert_id; // ✅ ID untuk detail_penerimaan
$stmt->close();

/* ✅ 2️⃣ INSERT DETAIL PENERIMAAN */
foreach ($_POST['barang'] as $b) {

    $idbarang      = intval($b['idbarang'] ?? 0);
    $jumlah_terima = intval($b['jumlah_terima'] ?? 0);

    if ($idbarang == 0 || $jumlah_terima == 0) continue;

    // ✅ Ambil harga satuan dari detail_pengadaan (BARANG INI di PENGADAAN INI)
    $qHarga = $conn->query("
        SELECT harga_satuan 
        FROM detail_pengadaan
        WHERE idpengadaan = $idpengadaan
          AND idbarang = $idbarang
        LIMIT 1
    ");

    $rowHarga = $qHarga->fetch_assoc();
    $harga_satuan = intval($rowHarga['harga_satuan'] ?? 0);

    $subtotal_item = $harga_satuan * $jumlah_terima;

    $queryDetail = "
        INSERT INTO detail_penerimaan 
        (idpenerimaan, barang_idbarang, jumlah_terima, harga_satuan_terima, sub_total_terima)
        VALUES (?, ?, ?, ?, ?)
    ";

    $stmtDetail = $conn->prepare($queryDetail);
    $stmtDetail->bind_param("iiiis", $idpenerimaan, $idbarang, $jumlah_terima, $harga_satuan, $subtotal_item);

    if (!$stmtDetail->execute()) {
        echo json_encode(['success' => false, 'message' => $stmtDetail->error]);
        exit;
    }

    $stmtDetail->close();
}

$conn->close();

/* ✅ 3️⃣ TRIGGER AKAN MENJALANKAN:
    ✅ Insert kartu_stok
    ✅ Update status penerimaan
    ✅ Cek sisa barang & update pengadaan jadi 'S' jika selesai
*/

echo json_encode([
    'success' => true,
    'message' => '✅ Penerimaan barang berhasil diproses dan dicatat!'
]);
exit;
?>

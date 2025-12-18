<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../../config/koneksi.php';
$db = new DBConnection();
$conn = $db->getConnection();

header('Content-Type: application/json');

// Ambil data utama
$idvendor   = intval($_POST['idvendor'] ?? 0);
$iduser     = intval($_POST['iduser'] ?? 0);
$subtotal   = intval($_POST['subtotal'] ?? 0);
$total      = intval($_POST['total'] ?? 0);
$ppn        = $total - $subtotal;
$timestamp  = date('Y-m-d H:i:s');
$status     = 'P';

// Validasi
if ($idvendor == 0 || $iduser == 0) {
    echo json_encode(['success' => false, 'message' => 'Vendor atau user tidak valid']);
    exit;
}

if (!isset($_POST['detail']) || count($_POST['detail']) == 0) {
    echo json_encode(['success' => false, 'message' => 'Detail barang tidak boleh kosong']);
    exit;
}

// ✅ 1️⃣ INSERT KE TABEL PENGADAAN
$queryPengadaan = "
    INSERT INTO pengadaan (timestamp, user_iduser, vendor_idvendor, subtotal_nilai, ppn, total_nilai, status)
    VALUES (?, ?, ?, ?, ?, ?, ?)
";

$stmt = $conn->prepare($queryPengadaan);
$stmt->bind_param("siiiiss", $timestamp, $iduser, $idvendor, $subtotal, $ppn, $total, $status);

if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => $stmt->error]);
    exit;
}

$idpengadaan = $conn->insert_id; // ✅ ID untuk detail
$stmt->close();

// ✅ 2️⃣ INSERT DETAIL BARANG
foreach ($_POST['detail'] as $d) {

    $idbarang = intval($d['idbarang'] ?? 0);
    $jumlah   = intval($d['jumlah'] ?? 0);

    if ($idbarang == 0 || $jumlah == 0) continue;

    // Ambil harga satuan dari tabel barang
    $qHarga = $conn->query("SELECT harga_modal FROM barang WHERE idbarang = $idbarang");
    $rowHarga = $qHarga->fetch_assoc();
    $harga_satuan = intval($rowHarga['harga_modal'] ?? 0);

    $subtotal_item = $harga_satuan * $jumlah;

    $queryDetail = "
        INSERT INTO detail_pengadaan (idpengadaan, idbarang, jumlah, harga_satuan, sub_total)
        VALUES (?, ?, ?, ?, ?)
    ";

    $stmtDetail = $conn->prepare($queryDetail);
    $stmtDetail->bind_param("iiiis", $idpengadaan, $idbarang, $jumlah, $harga_satuan, $subtotal_item);

    if (!$stmtDetail->execute()) {
        echo json_encode(['success' => false, 'message' => $stmtDetail->error]);
        exit;
    }

    $stmtDetail->close();
}

$conn->close();

// ✅ 3️⃣ RESPON BERHASIL
echo json_encode(['success' => true]);
exit;
?>

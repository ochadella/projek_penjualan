<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once __DIR__ . '/../../../config/koneksi.php';

$db = new DBConnection();
$conn = $db->getConnection();

$idpenerimaan = $_POST['idpenerimaan'];
$jumlah       = $_POST['jumlah'];
$alasan       = $_POST['alasan'];
$iduser       = $_POST['iduser'] ?? ($_SESSION['iduser'] ?? 1);

// 1️⃣ Insert retur_barang
$q1 = $conn->prepare("
    INSERT INTO retur_barang (created_at, idpenerimaan, iduser)
    VALUES (NOW(), ?, ?)
");
$q1->bind_param("ii", $idpenerimaan, $iduser);
$q1->execute();

$idretur = $conn->insert_id;

// 2️⃣ AMBIL DETAIL PENERIMAAN — tapi kita pastikan BARANG ADA
$q2 = $conn->prepare("
    SELECT iddetail_penerimaan
    FROM detail_penerimaan
    WHERE idpenerimaan = ?
    ORDER BY iddetail_penerimaan ASC
    LIMIT 1
");
$q2->bind_param("i", $idpenerimaan);
$q2->execute();

$res2 = $q2->get_result();
$data2 = $res2->fetch_assoc();

if (!$data2) {
    echo "ERROR: Tidak ada data detail penerimaan.";
    exit;
}

$iddetail = $data2['iddetail_penerimaan'];

// 3️⃣ Insert detail_retur_barang
$q3 = $conn->prepare("
    INSERT INTO detail_retur_barang (jumlah, alasan, idretur, iddetail_penerimaan)
    VALUES (?, ?, ?, ?)
");
$q3->bind_param("isii", $jumlah, $alasan, $idretur, $iddetail);
$q3->execute();

echo "OK";
?>

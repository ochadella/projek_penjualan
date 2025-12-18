<?php
require_once __DIR__ . '/../../../config/koneksi.php';
$db = new DBConnection();
$conn = $db->getConnection();

$idpengadaan = $_GET['idpengadaan'] ?? 0;

if ($idpengadaan == 0) {
    echo json_encode(['success' => false, 'message' => 'ID tidak valid']);
    exit;
}

$query = "
    SELECT 
        dp.idbarang,
        b.nama_barang,
        s.nama_satuan,
        dp.harga_satuan,
        dp.jumlah AS jumlah_pesan,

        COALESCE((
            SELECT SUM(dp2.jumlah_terima)
            FROM detail_penerimaan dp2
            JOIN penerimaan pn ON pn.idpenerimaan = dp2.idpenerimaan
            WHERE pn.idpengadaan = dp.idpengadaan
              AND dp2.barang_idbarang = dp.idbarang
        ), 0) AS jumlah_diterima,

        (dp.jumlah - COALESCE((
            SELECT SUM(dp2.jumlah_terima)
            FROM detail_penerimaan dp2
            JOIN penerimaan pn ON pn.idpenerimaan = dp2.idpenerimaan
            WHERE pn.idpengadaan = dp.idpengadaan
              AND dp2.barang_idbarang = dp.idbarang
        ), 0)) AS sisa,

        v.nama_vendor,
        p.status AS status_pengadaan
    FROM detail_pengadaan dp
    JOIN barang b ON b.idbarang = dp.idbarang
    JOIN satuan s ON s.idsatuan = b.idsatuan
    JOIN pengadaan p ON p.idpengadaan = dp.idpengadaan
    JOIN vendor v ON v.idvendor = p.vendor_idvendor
    WHERE dp.idpengadaan = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $idpengadaan);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while($row = $result->fetch_assoc()) {
    $row['status_pengadaan_text'] = $row['status_pengadaan'] == 'P' ? 'Proses' : 'Selesai';
    $data[] = $row;
}

echo json_encode(['success' => true, 'data' => $data]);

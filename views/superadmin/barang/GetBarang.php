<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../../config/koneksi.php';
$db = new DBConnection();
$conn = $db->getConnection();

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    $result = $conn->query("
        SELECT idbarang, nama_barang, jenis, idsatuan, harga_modal, status 
        FROM barang 
        WHERE idbarang = $id
    ");

    if ($result && $row = $result->fetch_assoc()) {

        echo json_encode([
            'success' => true,
            'barang' => [
                'idbarang'     => $row['idbarang'],
                'nama_barang'  => $row['nama_barang'],
                'jenis'        => $row['jenis'],
                
                // ✅ FIX PENTING
                // Frontend membaca 'harga'
                // jadi harga_modal DIKIRIM sebagai 'harga'
                'harga'        => $row['harga_modal'],

                'idsatuan'     => $row['idsatuan'],
                'status'       => $row['status']
            ]
        ]);

    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Data tidak ditemukan'
        ]);
    }
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'ID tidak dikirim'
    ]);
}
?>

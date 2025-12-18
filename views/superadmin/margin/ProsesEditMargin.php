<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once __DIR__ . '/../../../config/koneksi.php';
$db = new DBConnection();
$conn = $db->getConnection();

if (!$conn) {
    echo json_encode(['success' => false, 'message' => '❌ Koneksi database gagal!']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $idmargin = intval($_POST['idmargin_penjualan'] ?? 0);
    $persen = floatval($_POST['persen'] ?? 0);
    $tipe_kebijakan = $_POST['tipe_kebijakan'] ?? '';
    $idtarget = intval($_POST['idtarget'] ?? 0);
    $status = intval($_POST['status'] ?? 0);

    // Validasi
    if ($idmargin <= 0 || $persen <= 0 || empty($tipe_kebijakan)) {
        echo json_encode(['success' => false, 'message' => '⚠️ Data tidak lengkap!']);
        exit;
    }

    // ✅ LOGIKA PENTING: Jika status = 1, nonaktifkan margin lain
    if ($status == 1) {
        $conn->query("UPDATE margin_penjualan SET status = 0 WHERE idmargin_penjualan != $idmargin");
    }

    // Update margin yang dipilih
    $sql = "UPDATE margin_penjualan 
            SET persen = $persen, 
                tipe_kebijakan = '$tipe_kebijakan', 
                idtarget = $idtarget, 
                status = $status, 
                updated_at = NOW()
            WHERE idmargin_penjualan = $idmargin";

    if ($conn->query($sql)) {
        echo json_encode(['success' => true, 'message' => '✅ Kebijakan margin berhasil diperbarui!']);
    } else {
        echo json_encode(['success' => false, 'message' => '❌ Gagal memperbarui: ' . $conn->error]);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => '❌ Method tidak valid!']);
exit;
?>
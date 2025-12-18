<?php
require_once __DIR__ . '/koneksi.php';

function getActiveMargin() {
    $db = new DBConnection();
    $conn = $db->getConnection();

    // Prioritas: kategori > vendor > global
    // Ambil margin yang status = 1
    $q = $conn->query("
        SELECT *
        FROM margin_penjualan
        WHERE status = 1
        ORDER BY 
            CASE 
                WHEN tipe_kebijakan='kategori' THEN 1
                WHEN tipe_kebijakan='vendor' THEN 2
                WHEN tipe_kebijakan='global' THEN 3
            END
        LIMIT 1
    ");

    return $q->fetch_assoc();
}
?>

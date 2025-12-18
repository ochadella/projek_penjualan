<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../../config/koneksi.php';

header('Content-Type: application/json');

$db = new DBConnection();
$conn = $db->getConnection();

$response = ['success' => false, 'message' => 'Terjadi kesalahan'];

if (isset($_POST['id'])) {
  $id = $_POST['id'];
  $defaultPass = password_hash('123456', PASSWORD_DEFAULT);

  // cek kolom iduser atau id_user
  $checkColumn = $conn->query("SHOW COLUMNS FROM user LIKE 'iduser'");
  $idColumn = ($checkColumn->num_rows > 0) ? 'iduser' : 'id_user';

  $reset = $conn->query("UPDATE user SET password = '$defaultPass' WHERE $idColumn = '$id'");

  if ($reset) {
    $response = ['success' => true, 'message' => 'Password berhasil direset ke default (123456)'];
  } else {
    $response = ['success' => false, 'message' => 'Gagal mereset password user'];
  }
} else {
  $response = ['success' => false, 'message' => 'ID user tidak ditemukan'];
}

echo json_encode($response);
exit;
?>

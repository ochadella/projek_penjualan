<?php
/**
 * 🔗 Class DBConnection untuk Pemrograman Basis Data
 * Project: PBD_UTS
 * Author: Ocha Della
 */

class DBConnection {
    // 🧩 Konfigurasi koneksi ke database
    private string $servername = "127.0.0.1";     // Bisa juga "localhost"
    private string $username   = "root";          // Username default MAMP
    private string $password   = "Konikulaposero25";   // Kosongkan jika tidak pakai password
    private string $dbname     = "PBD_UTS";        // Nama database kamu
    private mysqli $dbconn;

    // 🚀 Koneksi otomatis saat class dipanggil
    public function __construct() {
        $this->init_connect();
    }

    // 🔌 Inisialisasi koneksi ke MySQL
    private function init_connect(): void {
        $this->dbconn = new mysqli(
            $this->servername,
            $this->username,
            $this->password,
            $this->dbname
        );

        if ($this->dbconn->connect_error) {
            die("❌ Koneksi ke database gagal: " . $this->dbconn->connect_error);
        }

        // Set karakter ke UTF-8 agar mendukung teks berbahasa Indonesia
        $this->dbconn->set_charset("utf8mb4");
    }

    // 💬 Jalankan query biasa (SELECT, INSERT, UPDATE, DELETE)
    public function send_query(string $query): array {
        $result = $this->dbconn->query($query);

        if ($this->dbconn->error) {
            return [
                "status"  => "error",
                "message" => $this->dbconn->error,
                "data"    => []
            ];
        } elseif ($result === true) {
            return [
                "status"  => "success",
                "message" => "✅ Query berhasil dijalankan",
                "data"    => []
            ];
        } else {
            return [
                "status"  => "success",
                "message" => "✅ Query berhasil dijalankan",
                "data"    => $result->fetch_all(MYSQLI_ASSOC)
            ];
        }
    }

    // 🧠 Ambil koneksi mentah (berguna di file lain)
    public function getConnection(): mysqli {
        return $this->dbconn;
    }

    // 🚪 Tutup koneksi (opsional)
    public function close_connection(): void {
        if ($this->dbconn) {
            $this->dbconn->close();
        }
    }
}
?>

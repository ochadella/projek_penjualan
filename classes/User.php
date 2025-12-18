<?php
class User
{
    private mysqli $conn;

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }

    // 🔹 Menampilkan semua user dengan nama role
    public function tampilUser(): array
    {
        $sql = "
            SELECT 
                u.iduser,
                u.username,
                u.status,
                r.idrole,
                r.nama_role AS role
            FROM user u
            LEFT JOIN role r ON u.idrole = r.idrole
            ORDER BY u.iduser ASC
        ";

        $result = $this->conn->query($sql);

        if (!$result) {
            die('Gagal mengambil data user: ' . $this->conn->error);
        }

        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        return $users;
    }

    // 🔹 Menambahkan user baru
    public function tambahUser(string $username, string $password, int $idrole, string $status = 'aktif'): bool
    {
        $stmt = $this->conn->prepare("
            INSERT INTO user (username, password, idrole, status)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("ssis", $username, $password, $idrole, $status);
        return $stmt->execute();
    }

    // 🔹 Memperbarui data user
    public function updateUser(int $id, string $username, int $idrole): bool
    {
        $stmt = $this->conn->prepare("
            UPDATE user SET username = ?, idrole = ? WHERE iduser = ?
        ");
        $stmt->bind_param("sii", $username, $idrole, $id);
        return $stmt->execute();
    }

    // 🔹 Reset password user ke default
    public function resetPassword(int $id, string $newPass = '123456'): bool
    {
        $hashed = password_hash($newPass, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("
            UPDATE user SET password = ? WHERE iduser = ?
        ");
        $stmt->bind_param("si", $hashed, $id);
        return $stmt->execute();
    }

    // 🔹 Hapus user secara permanen (bukan nonaktif)
    public function hapusUser(int $id): bool
    {
        // Nonaktifkan sementara foreign key constraint
        $this->conn->query("SET FOREIGN_KEY_CHECKS = 0");

        $stmt = $this->conn->prepare("DELETE FROM user WHERE iduser = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $deleted = $stmt->affected_rows > 0;

        // Aktifkan kembali FK constraint
        $this->conn->query("SET FOREIGN_KEY_CHECKS = 1");

        $stmt->close();
        return $deleted;
    }

    // 🔹 Cari 1 user berdasarkan ID (misal untuk modal edit)
    public function getUserById(int $id): ?array
    {
        $stmt = $this->conn->prepare("
            SELECT iduser, username, idrole, status
            FROM user WHERE iduser = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->num_rows > 0 ? $result->fetch_assoc() : null;
    }
}
?>

<?php
class UserModel {
    private $db;

    public function __construct($db_connection) {
        $this->db = $db_connection;
    }

    // READ: Ambil semua user
    public function getAllUsers() {
        $query = "SELECT id, username, email, role FROM users ORDER BY id DESC";
        $stmt  = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // READ: Ambil satu user berdasarkan ID
    public function getUserById($id) {
        $query = "SELECT id, username, email, role FROM users WHERE id = :id";
        $stmt  = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // UPDATE: Edit data user
    public function updateUser($id, $username, $email, $role, $newPassword = null) {
        // Kalau ada password baru, update sekalian
        if ($newPassword !== null) {
            $query = "UPDATE users SET 
                        username = :username,
                        email    = :email,
                        role     = :role,
                        password = :password
                      WHERE id = :id";
        } else {
            $query = "UPDATE users SET 
                        username = :username,
                        email    = :email,
                        role     = :role
                      WHERE id = :id";
        }

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id',       $id,       PDO::PARAM_INT);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email',    $email);
        $stmt->bindParam(':role',     $role);

        if ($newPassword !== null) {
            $hashed = password_hash($newPassword, PASSWORD_BCRYPT);
            $stmt->bindParam(':password', $hashed);
        }

        return $stmt->execute();
    }

    // DELETE: Hapus user
    public function deleteUser($id) {
        // Jangan boleh hapus diri sendiri
        if ((int)$id === (int)($_SESSION['user_id'] ?? 0)) {
            return ['success' => false, 'message' => 'Tidak bisa menghapus akun sendiri.'];
        }

        $query = "DELETE FROM users WHERE id = :id";
        $stmt  = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return ['success' => true];
    }
}
?>
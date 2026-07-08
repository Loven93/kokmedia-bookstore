<?php
require_once ROOT_DIR . '/models/UserModel.php';

class UserController {
    private $userModel;

    public function __construct($db_connection) {
        $this->userModel = new UserModel($db_connection);
    }

    // Proses edit user
    public function processEditUser($id, $username, $email, $role, $newPassword = '') {
        $id = (int)$id;

        // Validasi
        if ($id <= 0)                return "ID user tidak valid.";
        if (empty(trim($username)))  return "Username wajib diisi.";
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "Format email tidak valid.";
        }
        if (!in_array($role, ['admin', 'pembeli'])) {
            return "Role tidak valid.";
        }
        if (!empty($newPassword) && strlen($newPassword) < 8) {
            return "Password baru minimal 8 karakter.";
        }

        $password = !empty($newPassword) ? $newPassword : null;
        $result   = $this->userModel->updateUser($id, $username, $email, $role, $password);

        return $result ? "success" : "Gagal memperbarui data user.";
    }

    // Proses hapus user
    public function processDeleteUser($id) {
        $id     = (int)$id;
        if ($id <= 0) return "ID user tidak valid.";

        $result = $this->userModel->deleteUser($id);
        return $result['success'] ? "success" : $result['message'];
    }
}
?>
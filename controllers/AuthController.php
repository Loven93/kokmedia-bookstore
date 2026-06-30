<?php
class AuthController {
    private $db;

    public function __construct($db_connection) {
        $this->db = $db_connection;
    }

    // Fungsi untuk Registrasi User Baru
    public function register($username, $password) {
        // Wajib: Hash password sebelum masuk ke database agar aman
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        
        try {
            $query = "INSERT INTO users (username, password) VALUES (:username, :password)";
            $stmt = $this->db->prepare($query);
            
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $hashed_password);
            
            if($stmt->execute()) {
                return true; // Berhasil daftar
            }
            return false;
        } catch(PDOException $e) {
            // Akan masuk ke sini jika username sudah digunakan (karena constraint UNIQUE di database)
            return false; 
        }
    }

    // Fungsi untuk Login
    public function login($username, $password) {
        $query = "SELECT id, username, password, role FROM users WHERE username = :username";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Cek apakah user ada dan password yang diinput cocok dengan hash di database
        if($user && password_verify($password, $user['password'])) {
            // Set Session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            return true;
        }
        return false;
    }

    // Fungsi untuk Logout
    public function logout() {
        session_unset();
        session_destroy();
    }
     public function handleUpload($file) {
    $targetDir = "asset/uploads/";
    $fileName = basename($file["name"]);
    $targetFilePath = $targetDir . time() . "_" . $fileName; // Tambah timestamp agar nama unik
    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

    // Filter ekstensi gambar
    $allowTypes = array('jpg', 'png', 'jpeg');
    if (in_array($fileType, $allowTypes)) {
        if (move_uploaded_file($file["tmp_name"], $targetFilePath)) {
            return $targetFilePath; // Kembalikan path untuk disimpan di DB
        }
    }
    return false;
 }
}
?>
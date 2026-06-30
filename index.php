<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!defined('ROOT_DIR')) {
    define('ROOT_DIR', __DIR__);
}

$route = isset($_GET['route']) ? $_GET['route'] : 'home';

switch ($route) {
    case 'home':
        require_once ROOT_DIR . '/pages/home.php';
        break;

    case 'dashboard':
        // Proteksi Gerbang Admin (Sesuai Bab 5.2.3)
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            $_SESSION['message'] = "Akses ditolak. Anda harus login sebagai Admin.";
            header("Location: ?route=login");
            exit;
        }
        require_once ROOT_DIR . '/pages/dashboard.php';
        break;

    case 'process_add_book':
        require_once ROOT_DIR . '/helpers/Database.php';
        require_once ROOT_DIR . '/controllers/BookController.php';
        
        $db = new Database();
        $controller = new BookController($db->getConnection());
        
        $result = $controller->addBook(
            $_POST['title'] ?? '',
            $_POST['author'] ?? '',
            $_POST['description'] ?? '',
            $_POST['price'] ?? 0,
            $_POST['stock'] ?? 0,
            $_FILES['cover_image'] ?? null
        );
        
        $_SESSION['message'] = ($result === "success") ? "Buku berhasil ditambahkan!" : $result;
        header("Location: ?route=dashboard");
        exit;

    case 'add_to_cart':
        require_once ROOT_DIR . '/helpers/Database.php';
        require_once ROOT_DIR . '/controllers/CartController.php';
        
        $db = new Database();
        $controller = new CartController($db->getConnection());
        $controller->addToCart($_POST['book_id'] ?? 0);
        break;

    case 'cart':
        require_once ROOT_DIR . '/pages/cart.php';
        break;

    case 'checkout':
        require_once ROOT_DIR . '/helpers/Database.php';
        require_once ROOT_DIR . '/controllers/TransactionController.php';
        
        $db = new Database();
        $controller = new TransactionController($db->getConnection());
        $controller->processCheckout();
        break;

    case 'login':
        require_once ROOT_DIR . '/pages/login.php';
        break;

    case 'process_login':
        require_once ROOT_DIR . '/helpers/Database.php';
        
        $db = new Database();
        $conn = $db->getConnection();
        
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        
        try {
            $query = "SELECT * FROM users WHERE username = :username";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // 1. Validasi akun jika password terenkripsi BCRYPT di database
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_role'] = $user['role']; // Mengunci peran 'admin' atau 'pembeli'
                
                if ($user['role'] === 'admin') {
                    header("Location: ?route=dashboard");
                } else {
                    header("Location: ?route=home");
                }
                exit;
                
            // 2. BACKUP UNTUK DEMO UAS: Jika password di DB masih string mentah (belum di-hash)
            } else if ($user && $password === $user['password']) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_role'] = $user['role']; // Mengunci peran 'admin' atau 'pembeli'
                
                if ($user['role'] === 'admin') {
                    header("Location: ?route=dashboard");
                } else {
                    header("Location: ?route=home");
                }
                exit;
            } else {
                $_SESSION['message'] = "Username atau password salah.";
                header("Location: ?route=login");
                exit;
            }
        } catch (Exception $e) {
            $_SESSION['message'] = "Error: " . $e->getMessage();
            header("Location: ?route=login");
            exit;
        }

    case 'logout':
        session_destroy();
        header("Location: ?route=home");
        exit;
    // --- TAMBAHKAN / PERBARUI CASE INI DI DALAM SWITCH index.php ---

    case 'login':
        require_once ROOT_DIR . '/pages/login.php';
        break;

    case 'process_login':
        require_once ROOT_DIR . '/helpers/Database.php';
        
        $db = new Database();
        $conn = $db->getConnection();
        
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        
        try {
            $query = "SELECT * FROM users WHERE username = :username";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Validasi Password Secure menggunakan BCRYPT (Sesuai Bab 5.4 Laporan)
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_role'] = $user['role'];
                
                if ($user['role'] === 'admin') {
                    header("Location: ?route=dashboard");
                } else {
                    header("Location: ?route=home");
                }
                exit;
            } else {
                $_SESSION['message'] = "Username atau password salah.";
                header("Location: ?route=login");
                exit;
            }
        } catch (Exception $e) {
            $_SESSION['message'] = "Error: " . $e->getMessage();
            header("Location: ?route=login");
            exit;
        }

    case 'register':
        require_once ROOT_DIR . '/pages/register.php';
        break;

    case 'process_register':
        require_once ROOT_DIR . '/helpers/Database.php';
        
        $db = new Database();
        $conn = $db->getConnection();
        
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'pembeli'; // Default jika tidak diset
        
        if (empty($username) || empty($password)) {
            $_SESSION['message'] = "Username dan password wajib diisi.";
            header("Location: ?route=register");
            exit;
        }
        
        // Hashing password dengan BCRYPT demi keamanan standar industri
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        
        try {
            $query = "INSERT INTO users (username, password, email, role) VALUES (:username, :password, :email, :role)";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':role', $role);
            
            if ($stmt->execute()) {
                $_SESSION['message'] = "Pendaftaran berhasil! Silakan login.";
                header("Location: ?route=login");
            } else {
                $_SESSION['message'] = "Gagal mendaftarkan akun.";
                header("Location: ?route=register");
            }
            exit;
        } catch (PDOException $e) {
            // Menangkap error jika username duplikat (Error 23505)
            if ($e->getCode() == '23505') {
                $_SESSION['message'] = "Username sudah digunakan. Pilih nama lain.";
            } else {
                $_SESSION['message'] = "Error: " . $e->getMessage();
            }
            header("Location: ?route=register");
            exit;
        }

    default:
        http_response_code(404);
        echo "<h1 style='text-align:center; margin-top:50px;'>404 - Halaman Kokmedia Tidak Ditemukan</h1>";
        break;
}
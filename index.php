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
    // Tampilkan form edit buku
    case 'edit_book':
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            $_SESSION['message'] = "Akses ditolak.";
            header("Location: ?route=login");
            exit;
        }
        require_once ROOT_DIR . '/helpers/Database.php';
        require_once ROOT_DIR . '/models/BookModel.php';

        $db = new Database();
        $bookModel = new BookModel($db->getConnection());
        $book = $bookModel->getBookById((int) ($_GET['id'] ?? 0));

        if (!$book) {
            $_SESSION['message'] = "Buku tidak ditemukan.";
            header("Location: ?route=dashboard");
            exit;
        }
        require_once ROOT_DIR . '/pages/edit_book.php';
        break;

    // Proses form edit buku
    case 'process_edit_book':
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            $_SESSION['message'] = "Akses ditolak.";
            header("Location: ?route=login");
            exit;
        }
        require_once ROOT_DIR . '/helpers/Database.php';
        require_once ROOT_DIR . '/controllers/BookController.php';

        $db = new Database();
        $controller = new BookController($db->getConnection());
        $result = $controller->processEditBook(
            $_POST['id'] ?? 0,
            $_POST['title'] ?? '',
            $_POST['author'] ?? '',
            $_POST['description'] ?? '',
            $_POST['price'] ?? 0,
            $_POST['stock'] ?? 0,
            $_FILES['cover_image'] ?? null
        );

        $_SESSION['message'] = ($result === "success")
            ? "Buku berhasil diperbarui!"
            : $result;
        header("Location: ?route=dashboard");
        exit;

    // Proses hapus buku
    case 'delete_book':
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            $_SESSION['message'] = "Akses ditolak.";
            header("Location: ?route=login");
            exit;
        }
        require_once ROOT_DIR . '/helpers/Database.php';
        require_once ROOT_DIR . '/controllers/BookController.php';

        $db = new Database();
        $controller = new BookController($db->getConnection());
        $result = $controller->processDeleteBook($_POST['id'] ?? 0);

        $_SESSION['message'] = ($result === "success")
            ? "Buku berhasil dihapus."
            : $result;
        header("Location: ?route=dashboard");
        exit;

    // Hapus satu item dari cart
    case 'remove_from_cart':
        require_once ROOT_DIR . '/helpers/Database.php';
        require_once ROOT_DIR . '/controllers/CartController.php';

        $db = new Database();
        $controller = new CartController($db->getConnection());
        $controller->removeFromCart($_POST['book_id'] ?? 0);
        break;

    // Update quantity item di cart
    case 'update_cart':
        require_once ROOT_DIR . '/helpers/Database.php';
        require_once ROOT_DIR . '/controllers/CartController.php';

        $db = new Database();
        $controller = new CartController($db->getConnection());
        $controller->updateQuantity(
            $_POST['book_id'] ?? 0,
            $_POST['quantity'] ?? 0
        );
        break;
    // ===== USER ROUTES =====

    case 'users':
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            $_SESSION['message'] = "Akses ditolak.";
            header("Location: ?route=login");
            exit;
        }
        require_once ROOT_DIR . '/pages/users.php';
        break;

    case 'edit_user':
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            $_SESSION['message'] = "Akses ditolak.";
            header("Location: ?route=login");
            exit;
        }
        require_once ROOT_DIR . '/helpers/Database.php';
        require_once ROOT_DIR . '/models/UserModel.php';
        $db = new Database();
        $userModel = new UserModel($db->getConnection());
        $user = $userModel->getUserById((int) ($_GET['id'] ?? 0));
        if (!$user) {
            $_SESSION['message'] = "User tidak ditemukan.";
            header("Location: ?route=users");
            exit;
        }
        require_once ROOT_DIR . '/pages/edit_user.php';
        break;

    case 'process_edit_user':
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            $_SESSION['message'] = "Akses ditolak.";
            header("Location: ?route=login");
            exit;
        }
        require_once ROOT_DIR . '/helpers/Database.php';
        require_once ROOT_DIR . '/controllers/UserController.php';
        $db = new Database();
        $controller = new UserController($db->getConnection());
        $result = $controller->processEditUser(
            $_POST['id'] ?? 0,
            $_POST['username'] ?? '',
            $_POST['email'] ?? '',
            $_POST['role'] ?? 'pembeli',
            $_POST['new_password'] ?? ''
        );
        $_SESSION['message'] = ($result === "success") ? "User berhasil diperbarui!" : $result;
        header("Location: ?route=users");
        exit;

    case 'delete_user':
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            $_SESSION['message'] = "Akses ditolak.";
            header("Location: ?route=login");
            exit;
        }
        require_once ROOT_DIR . '/helpers/Database.php';
        require_once ROOT_DIR . '/controllers/UserController.php';
        $db = new Database();
        $controller = new UserController($db->getConnection());
        $result = $controller->processDeleteUser($_POST['id'] ?? 0);
        $_SESSION['message'] = ($result === "success") ? "User berhasil dihapus." : $result;
        header("Location: ?route=users");
        exit;

    // ===== ORDER ROUTES =====

    case 'orders':
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['message'] = "Silakan login terlebih dahulu.";
            header("Location: ?route=login");
            exit;
        }
        require_once ROOT_DIR . '/pages/orders.php';
        break;

    case 'order_detail':
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['message'] = "Silakan login terlebih dahulu.";
            header("Location: ?route=login");
            exit;
        }
        require_once ROOT_DIR . '/helpers/Database.php';
        require_once ROOT_DIR . '/models/OrderModel.php';
        $db = new Database();
        $orderModel = new OrderModel($db->getConnection());
        $order = $orderModel->getOrderDetail((int) ($_GET['id'] ?? 0));
        if (!$order) {
            $_SESSION['message'] = "Order tidak ditemukan.";
            header("Location: ?route=orders");
            exit;
        }
        // Pembeli hanya boleh lihat order milik sendiri
        $isAdmin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
        if (!$isAdmin && $order['user_id'] !== $_SESSION['user_id']) {
            $_SESSION['message'] = "Akses ditolak.";
            header("Location: ?route=orders");
            exit;
        }
        require_once ROOT_DIR . '/pages/order_detail.php';
        break;

    case 'update_order_status':
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            $_SESSION['message'] = "Akses ditolak.";
            header("Location: ?route=login");
            exit;
        }
        require_once ROOT_DIR . '/helpers/Database.php';
        require_once ROOT_DIR . '/models/OrderModel.php';
        $db = new Database();
        $orderModel = new OrderModel($db->getConnection());
        $result = $orderModel->updateOrderStatus(
            (int) ($_POST['id'] ?? 0),
            $_POST['status'] ?? ''
        );
        $_SESSION['message'] = $result ? "Status order diperbarui." : "Gagal memperbarui status.";
        header("Location: ?route=orders");
        exit;

    case 'delete_order':
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            $_SESSION['message'] = "Akses ditolak.";
            header("Location: ?route=login");
            exit;
        }
        require_once ROOT_DIR . '/helpers/Database.php';
        require_once ROOT_DIR . '/models/OrderModel.php';
        $db = new Database();
        $orderModel = new OrderModel($db->getConnection());
        $result = $orderModel->deleteOrder((int) ($_POST['id'] ?? 0));
        $_SESSION['message'] = $result['success'] ? "Order berhasil dihapus." : $result['message'];
        header("Location: ?route=orders");
        exit;

    default:
        http_response_code(404);
        echo "<h1 style='text-align:center; margin-top:50px;'>404 - Halaman Kokmedia Tidak Ditemukan</h1>";
        break;
}
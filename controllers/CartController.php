<?php
require_once ROOT_DIR . '/models/BookModel.php';

class CartController {
    private $bookModel;

    public function __construct($db_connection) {
        $this->bookModel = new BookModel($db_connection);
    }

    // Method addToCart() lama tidak berubah
    public function addToCart($book_id) {
        $book_id = (int)$book_id;
        if ($book_id <= 0) {
            $_SESSION['message'] = "ID buku tidak valid.";
            header("Location: ?route=home");
            exit;
        }

        $book = $this->bookModel->getBookById($book_id);

        if (!$book || $book['stock'] <= 0) {
            $_SESSION['message'] = "Buku tidak ditemukan atau stok habis.";
            header("Location: ?route=home");
            exit;
        }

        $_SESSION['cart'] = $_SESSION['cart'] ?? [];

        if (isset($_SESSION['cart'][$book_id])) {
            if ($_SESSION['cart'][$book_id]['quantity'] < $book['stock']) {
                $_SESSION['cart'][$book_id]['quantity']++;
            } else {
                $_SESSION['message'] = "Batas maksimal stok tercapai.";
                header("Location: ?route=home");
                exit;
            }
        } else {
            $_SESSION['cart'][$book_id] = [
                'book_id'     => $book_id,
                'title'       => $book['title'],
                'price'       => $book['price'],
                'quantity'    => 1,
                'cover_image' => $book['cover_image']
            ];
        }

        $_SESSION['message'] = "Buku '" . htmlspecialchars($book['title']) . "' ditambahkan ke keranjang!";
        header("Location: ?route=home");
        exit;
    }

    // Hapus satu item dari cart
    public function removeFromCart($book_id) {
        $book_id = (int)$book_id;

        if (isset($_SESSION['cart'][$book_id])) {
            $title = $_SESSION['cart'][$book_id]['title'];
            unset($_SESSION['cart'][$book_id]);
            $_SESSION['message'] = "'" . htmlspecialchars($title) . "' dihapus dari keranjang.";
        } else {
            $_SESSION['message'] = "Item tidak ditemukan di keranjang.";
        }

        header("Location: ?route=cart");
        exit;
    }

    // Update quantity item di cart
    public function updateQuantity($book_id, $quantity) {
        $book_id  = (int)$book_id;
        $quantity = (int)$quantity;

        if (!isset($_SESSION['cart'][$book_id])) {
            $_SESSION['message'] = "Item tidak ditemukan di keranjang.";
            header("Location: ?route=cart");
            exit;
        }

        // Kalau quantity 0 atau kurang, hapus item
        if ($quantity <= 0) {
            unset($_SESSION['cart'][$book_id]);
            $_SESSION['message'] = "Item dihapus dari keranjang.";
            header("Location: ?route=cart");
            exit;
        }

        // Validasi quantity tidak melebihi stok
        $book = $this->bookModel->getBookById($book_id);
        if (!$book) {
            $_SESSION['message'] = "Buku tidak ditemukan.";
            header("Location: ?route=cart");
            exit;
        }

        if ($quantity > $book['stock']) {
            $_SESSION['message'] = "Jumlah melebihi stok tersedia ({$book['stock']} unit).";
            header("Location: ?route=cart");
            exit;
        }

        $_SESSION['cart'][$book_id]['quantity'] = $quantity;
        header("Location: ?route=cart");
        exit;
    }
}
?>
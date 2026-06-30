<?php
require_once ROOT_DIR . '/models/BookModel.php';

class CartController {
    private $bookModel;

    public function __construct($db_connection) {
        $this->bookModel = new BookModel($db_connection);
    }

    public function addToCart($book_id) {
        $book = $this->bookModel->getBookById($book_id);
        
        if (!$book || $book['stock'] <= 0) {
            $_SESSION['message'] = "Buku tidak ditemukan atau stok habis.";
            header("Location: ?route=home");
            exit;
        }

        // Inisialisasi struktur keranjang jika belum ada (Sesuai Bab 5.2.1 Laporan)
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Jika buku sudah ada di keranjang, tambahkan quantity
        if (isset($_SESSION['cart'][$book_id])) {
            if ($_SESSION['cart'][$book_id]['quantity'] < $book['stock']) {
                $_SESSION['cart'][$book_id]['quantity']++;
            } else {
                $_SESSION['message'] = "Tidak bisa menambah jumlah. Batas maksimal stok tercapai.";
                header("Location: ?route=home");
                exit;
            }
        } else {
            // Masukkan data line item baru
            $_SESSION['cart'][$book_id] = [
                'title' => $book['title'],
                'price' => $book['price'],
                'quantity' => 1,
                'cover_image' => $book['cover_image']
            ];
        }

        $_SESSION['message'] = "Buku '" . $book['title'] . "' dimasukkan ke keranjang belanja!";
        header("Location: ?route=home");
        exit;
    }
}
?>
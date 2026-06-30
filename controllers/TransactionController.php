<?php
class TransactionController {
    private $db;

    public function __construct($db_connection) {
        $this->db = $db_connection;
    }

    public function processCheckout() {
        if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
            $_SESSION['message'] = "Keranjang belanja Anda kosong.";
            header("Location: ?route=home");
            exit;
        }

        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 2; // Default ID pembeli dummy
        $totalAmount = 0;

        foreach ($_SESSION['cart'] as $item) {
            $totalAmount += $item['price'] * $item['quantity'];
        }

        try {
            // Mulai database transaction untuk keamanan data ACID
            $this->db->beginTransaction();

            // 1. Simpan ke tabel induk (orders)
            $queryOrder = "INSERT INTO orders (user_id, total_amount, status) VALUES (:user_id, :total_amount, 'completed') RETURNING id";
            $stmtOrder = $this->db->prepare($queryOrder);
            $stmtOrder->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmtOrder->bindParam(':total_amount', $totalAmount);
            $stmtOrder->execute();
            
            $order = $stmtOrder->fetch(PDO::FETCH_ASSOC);
            $orderId = $order['id'];

            // 2. Iterasi item dari Session untuk masuk ke tabel anak (order_details)
            foreach ($_SESSION['cart'] as $bookId => $item) {
                $subtotal = $item['price'] * $item['quantity'];

                $queryDetail = "INSERT INTO order_details (order_id, book_id, quantity, subtotal) VALUES (:order_id, :book_id, :quantity, :subtotal)";
                $stmtDetail = $this->db->prepare($queryDetail);
                $stmtDetail->bindParam(':order_id', $orderId, PDO::PARAM_INT);
                $stmtDetail->bindParam(':book_id', $bookId, PDO::PARAM_INT);
                $stmtDetail->bindParam(':quantity', $item['quantity'], PDO::PARAM_INT);
                $stmtDetail->bindParam(':subtotal', $subtotal);
                $stmtDetail->execute();

                // 3. Efisiensi Alur: Potong stok buku di master tabel secara otomatis
                $queryUpdateStock = "UPDATE books SET stock = stock - :quantity WHERE id = :book_id";
                $stmtUpdateStock = $this->db->prepare($queryUpdateStock);
                $stmtUpdateStock->bindParam(':quantity', $item['quantity'], PDO::PARAM_INT);
                $stmtUpdateStock->bindParam(':book_id', $bookId, PDO::PARAM_INT);
                $stmtUpdateStock->execute();
            }

            // Commit transaksi jika seluruh query sukses tanpa error
            $this->db->commit();

            // Kosongkan keranjang belanja setelah sukses (Sesuai Bab 5.2.2 Laporan)
            unset($_SESSION['cart']);
            $_SESSION['message'] = "Transaksi Berhasil! Pembelian Anda telah tercatat di basis data Kokmedia.";
            header("Location: ?route=home");
            exit;

        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['message'] = "Transaksi Gagal: " . $e->getMessage();
            header("Location: ?route=cart");
            exit;
        }
    }
}
?>
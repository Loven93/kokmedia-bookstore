<?php
class OrderModel
{
    private $db;

    public function __construct($db_connection)
    {
        $this->db = $db_connection;
    }

    // READ: Semua order (untuk admin)
    public function getAllOrders()
    {
        $query = "SELECT 
                    o.id,
                    o.total_amount,
                    o.status,
                    o.created_at,
                    u.username
                  FROM orders o
                  JOIN users u ON o.user_id = u.id
                  ORDER BY o.id DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // READ: Order milik satu user (untuk pembeli)
    public function getOrdersByUser($user_id)
    {
        $query = "SELECT id, total_amount, status, created_at
                  FROM orders
                  WHERE user_id = :user_id
                  ORDER BY id DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // READ: Detail satu order beserta item-itemnya
    public function getOrderDetail($order_id)
    {
        $stmtOrder = $this->db->prepare(
            "SELECT o.id, 
                o.user_id,        -- ← TAMBAH INI
                o.total_amount, 
                o.status,
                o.created_at, 
                u.username
         FROM orders o
         JOIN users u ON o.user_id = u.id
         WHERE o.id = :order_id"
        );
        $stmtOrder->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        $stmtOrder->execute();
        $order = $stmtOrder->fetch(PDO::FETCH_ASSOC);

        if (!$order)
            return null;

        $stmtItems = $this->db->prepare(
            "SELECT od.quantity, od.subtotal,
                b.title, b.author, b.cover_image
         FROM order_details od
         JOIN books b ON od.book_id = b.id
         WHERE od.order_id = :order_id"
        );
        $stmtItems->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        $stmtItems->execute();
        $order['items'] = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

        return $order;
    }

    // UPDATE: Ubah status order (admin)
    public function updateOrderStatus($order_id, $status)
    {
        $allowed = ['pending', 'completed', 'cancelled'];
        if (!in_array($status, $allowed))
            return false;

        $query = "UPDATE orders SET status = :status WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $order_id, PDO::PARAM_INT);
        $stmt->bindParam(':status', $status);
        return $stmt->execute();
    }

    // DELETE: Hapus order (admin) — kembalikan stok buku
    public function deleteOrder($order_id)
    {
        try {
            $this->db->beginTransaction();

            // Ambil item dulu untuk kembalikan stok
            $queryItems = "SELECT book_id, quantity FROM order_details 
                           WHERE order_id = :order_id";
            $stmtItems = $this->db->prepare($queryItems);
            $stmtItems->bindParam(':order_id', $order_id, PDO::PARAM_INT);
            $stmtItems->execute();
            $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

            // Kembalikan stok tiap buku
            $stmtStock = $this->db->prepare(
                "UPDATE books SET stock = stock + :qty WHERE id = :book_id"
            );
            foreach ($items as $item) {
                $stmtStock->execute([
                    ':qty' => $item['quantity'],
                    ':book_id' => $item['book_id']
                ]);
            }

            // Hapus order_details dulu (foreign key)
            $this->db->prepare(
                "DELETE FROM order_details WHERE order_id = :id"
            )->execute([':id' => $order_id]);

            // Hapus order induk
            $this->db->prepare(
                "DELETE FROM orders WHERE id = :id"
            )->execute([':id' => $order_id]);

            $this->db->commit();
            return ['success' => true];

        } catch (Exception $e) {
            if ($this->db->inTransaction())
                $this->db->rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
?>
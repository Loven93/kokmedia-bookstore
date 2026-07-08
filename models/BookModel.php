<?php
class BookModel {
    private $db;

    public function __construct($db_connection) {
        $this->db = $db_connection;
    }

    public function getAllBooks() {
        $query = "SELECT * FROM books ORDER BY id DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBookById($id) {
        $query = "SELECT * FROM books WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update data buku
    public function updateBook($id, $title, $author, $description, $price, $stock, $cover_image = null) {
        // Kalau ada cover baru, update sekalian. Kalau tidak, biarkan cover lama
        if ($cover_image !== null) {
            $query = "UPDATE books SET 
                        title = :title,
                        author = :author,
                        description = :description,
                        price = :price,
                        stock = :stock,
                        cover_image = :cover_image
                      WHERE id = :id";
        } else {
            $query = "UPDATE books SET 
                        title = :title,
                        author = :author,
                        description = :description,
                        price = :price,
                        stock = :stock
                      WHERE id = :id";
        }

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id',          $id,          PDO::PARAM_INT);
        $stmt->bindParam(':title',       $title);
        $stmt->bindParam(':author',      $author);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':price',       $price);
        $stmt->bindParam(':stock',       $stock,       PDO::PARAM_INT);

        if ($cover_image !== null) {
            $stmt->bindParam(':cover_image', $cover_image);
        }

        return $stmt->execute();
    }

    // Hapus buku berdasarkan ID, kembalikan path cover untuk dihapus dari server
    public function deleteBook($id) {
        // Ambil path cover dulu sebelum dihapus, untuk cleanup file
        $book = $this->getBookById($id);
        if (!$book) {
            return ['success' => false, 'message' => 'Buku tidak ditemukan.'];
        }

        $query = "DELETE FROM books WHERE id = :id";
        $stmt  = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'success'     => true,
            'cover_image' => $book['cover_image'] // dikembalikan untuk hapus file
        ];
    }
}
?>
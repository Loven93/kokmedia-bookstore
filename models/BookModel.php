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
}
?>
<?php
class BookController {
    private $db;

    public function __construct($db_connection) {
        $this->db = $db_connection;
    }

    public function addBook($title, $author, $description, $price, $stock, $file) {
        $dbImagePath = null;

        if ($file && $file['error'] === 0) {
            $targetDir = ROOT_DIR . "/asset/uploads/covers/";
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            $fileExtension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
            $fileName = uniqid('cover_') . '.' . $fileExtension; // Sesuai Bab 4.5 Laporan
            $targetFilePath = $targetDir . $fileName;
            
            $allowTypes = array('jpg', 'png', 'jpeg', 'webp');
            if (!in_array($fileExtension, $allowTypes)) {
                return "Format gambar harus JPG, PNG, atau WEBP.";
            }

            if (move_uploaded_file($file["tmp_name"], $targetFilePath)) {
                $dbImagePath = "asset/uploads/covers/" . $fileName;
            } else {
                return "Gagal mengunggah gambar sampul.";
            }
        }

        try {
            $query = "INSERT INTO books (title, author, description, cover_image, price, stock) 
                      VALUES (:title, :author, :description, :cover_image, :price, :stock)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':author', $author);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':cover_image', $dbImagePath);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':stock', $stock);
            
            return $stmt->execute() ? "success" : "Gagal menyimpan data.";
        } catch(PDOException $e) {
            return "Error: " . $e->getMessage();
        }
    }
}
?>
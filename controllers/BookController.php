<?php
require_once ROOT_DIR . '/models/BookModel.php';

class BookController {
    private $db;
    private $bookModel;

    public function __construct($db_connection) {
        $this->db        = $db_connection;
        $this->bookModel = new BookModel($db_connection);
    }

    // Method addBook() yang lama tetap tidak berubah
    public function addBook($title, $author, $description, $price, $stock, $file) {
        $dbImagePath = null;

        if ($file && $file['error'] === 0) {
            $uploadResult = $this->handleUpload($file);
            if ($uploadResult === false) {
                return "Gagal mengunggah gambar sampul.";
            }
            $dbImagePath = $uploadResult;
        }

        try {
            $query = "INSERT INTO books (title, author, description, cover_image, price, stock) 
                      VALUES (:title, :author, :description, :cover_image, :price, :stock)";
            $stmt  = $this->db->prepare($query);
            $stmt->bindParam(':title',       $title);
            $stmt->bindParam(':author',      $author);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':cover_image', $dbImagePath);
            $stmt->bindParam(':price',       $price);
            $stmt->bindParam(':stock',       $stock, PDO::PARAM_INT);

            return $stmt->execute() ? "success" : "Gagal menyimpan data buku.";
        } catch (PDOException $e) {
            // Kalau DB gagal, hapus file yang sudah terlanjur diupload
            if ($dbImagePath && file_exists(ROOT_DIR . '/' . $dbImagePath)) {
                unlink(ROOT_DIR . '/' . $dbImagePath);
            }
            return "Error: " . $e->getMessage();
        }
    }

    // Proses edit buku
    public function processEditBook($id, $title, $author, $description, $price, $stock, $file) {
        // Validasi input dasar
        $id    = (int)$id;
        $price = (float)$price;
        $stock = (int)$stock;

        if ($id <= 0)           return "ID buku tidak valid.";
        if (empty(trim($title))) return "Judul buku wajib diisi.";
        if ($price < 0)         return "Harga tidak boleh negatif.";
        if ($stock < 0)         return "Stok tidak boleh negatif.";

        $newCoverPath = null;

        // Kalau ada file baru diupload
        if ($file && $file['error'] === 0) {
            $uploadResult = $this->handleUpload($file);
            if ($uploadResult === false) {
                return "Gagal mengunggah gambar sampul baru.";
            }

            // Hapus cover lama dari server
            $oldBook = $this->bookModel->getBookById($id);
            if ($oldBook && !empty($oldBook['cover_image'])) {
                $oldPath = ROOT_DIR . '/' . $oldBook['cover_image'];
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $newCoverPath = $uploadResult;
        }

        $result = $this->bookModel->updateBook(
            $id, $title, $author, $description, $price, $stock, $newCoverPath
        );

        return $result ? "success" : "Gagal memperbarui data buku.";
    }

    // Proses hapus buku
    public function processDeleteBook($id) {
        $id = (int)$id;
        if ($id <= 0) return "ID buku tidak valid.";

        $result = $this->bookModel->deleteBook($id);

        if (!$result['success']) {
            return $result['message'];
        }

        // Hapus file cover dari server kalau ada
        if (!empty($result['cover_image'])) {
            $coverPath = ROOT_DIR . '/' . $result['cover_image'];
            if (file_exists($coverPath)) {
                unlink($coverPath);
            }
        }

        return "success";
    }

    // Helper upload — dipindah dari addBook agar bisa dipakai ulang
    private function handleUpload($file) {
        $targetDir = ROOT_DIR . "/asset/uploads/covers/";

        if (!is_dir($targetDir)) {
            if (!mkdir($targetDir, 0755, true)) {
                return false;
            }
        }

        // Validasi MIME type dari isi file, bukan ekstensi
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
        $finfo        = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType     = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedMimes)) {
            return false;
        }

        // Validasi ukuran maksimal 2MB
        if ($file['size'] > 2 * 1024 * 1024) {
            return false;
        }

        $ext            = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $fileName       = bin2hex(random_bytes(16)) . '.' . $ext;
        $targetFilePath = $targetDir . $fileName;

        if (!move_uploaded_file($file['tmp_name'], $targetFilePath)) {
            return false;
        }

        return "asset/uploads/covers/" . $fileName;
    }
}
?>
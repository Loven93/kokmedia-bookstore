<?php
class Database {
    private $host = "localhost";
    private $db_name = "postgres"; 
    private $username = "postgres";             
    private $password = "2024"; 
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $dsn = "pgsql:host=" . $this->host . ";dbname=" . $this->db_name;
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            die("Koneksi database gagal: " . $exception->getMessage());
        }
        return $this->conn;
    }
}
?>
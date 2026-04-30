<?php
class Database {
    private $host = "127.0.0.1";
    private $port = "3307";
    private $dbname = "airlines_db";
    private $username = "root";
    private $password = "";
    public function connect(): PDO {
        try {
            $conn = new PDO(
                "mysql:host=$this->host;port=$this->port;dbname=$this->dbname; charset=utf8mb4;",
                $this->username,
                $this->password
            );
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch(PDOException $ex) {
            die("Connection has failed: " . $ex->getMessage());
        }
    }
}
?>
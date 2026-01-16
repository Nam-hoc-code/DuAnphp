<?php
class Database {
    protected $conn = null;

    public function connect() {
        if ($this->conn === null) {
            $this->conn = new mysqli(
                "127.0.0.1",
                "root",
                "",
                "webmusicdb",
                3306
            );

            if ($this->conn->connect_error) {
                die("❌ Kết nối thất bại: " . $this->conn->connect_error);
            }

            $this->conn->set_charset("utf8mb4");
        }

        return $this->conn;
    }
}

<?php
class Database {
    protected $conn;

    public function connect() {
        if ($this->conn === null) {
            $this->conn = new mysqli("localhost", "root", "", "webmusicdb");

            if ($this->conn->connect_error) {
                die("Kết nối thất bại: " . $this->conn->connect_error);
            }

            $this->conn->set_charset("utf8mb4");
        }

        return $this->conn;
    }
}

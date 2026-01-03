<?php
class Database {
    protected $conn; //

    public function connect() {
        $this->conn = new mysqli("localhost", "root", "", "musicwebdb");

        if ($this->conn->connect_error) {
            die("Kết nối thất bại: " . $this->conn->connect_error);
        }

        return $this->conn;
    }
}
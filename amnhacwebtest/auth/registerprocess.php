<?php
require_once "../config/database.php";

$db = new Database();
$conn = $db->connect();

$username = $_POST['username'];
$phone    = $_POST['phone'];
$password = $_POST['password'];

// 1️⃣ Mã hóa mật khẩu
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// 2️⃣ Kiểm tra username đã tồn tại chưa
$checkSql = "SELECT * FROM users WHERE username = ?";
$stmt = $conn->prepare($checkSql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    die("Username đã tồn tại");
}

// 3️⃣ Lưu user mới
$sql = "INSERT INTO users (username, phone, password) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $username, $phone, $hashedPassword);

if ($stmt->execute()) {
    echo "Đăng ký thành công <a href='login.php'>Đăng nhập</a>";
} else {
    echo "Lỗi đăng ký";
}

<?php
require_once "../config/database.php";

$username = trim($_POST['username'] ?? '');
$phone    = trim($_POST['phone'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    die(" Vui lòng nhập đầy đủ thông tin");
}

$db = new Database();
$conn = $db->connect();

/* Kiểm tra username tồn tại */
$checkSql = "SELECT user_id FROM users WHERE username = ?";
$stmt = $conn->prepare($checkSql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    die(" Tên tài khoản đã tồn tại");
}

/* Hash mật khẩu */
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

/* Luôn tạo USER */
$insertSql = "INSERT INTO users (username, phone, password, role) 
              VALUES (?, ?, ?, 'USER')";

$insertStmt = $conn->prepare($insertSql);
$insertStmt->bind_param("sss", $username, $phone, $hashedPassword);

if ($insertStmt->execute()) {
    header("Location: loginform.php");
    exit;
} else {
    echo " Đăng ký thất bại";
}

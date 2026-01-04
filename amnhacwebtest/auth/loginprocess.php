<?php
session_start();
require_once "../config/database.php";

$username = $_POST['username'];
$password = $_POST['password'];

$db = new Database();
$conn = $db->connect();

// Tìm user theo username
$sql = "SELECT * FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
    
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
                                                                 
    // So sánh mật khẩu (đang dùng plain text)
    if ($password === $user['password']) {
    
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] === 'ADMIN') {
            header("Location: ../admin.php");
        } else {
            header("Location: ../user.php");
        }
        exit;

    } else {
        echo "❌ Sai mật khẩu";
    }
} else {
    echo "❌ Username không tồn tại";
}

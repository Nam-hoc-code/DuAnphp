<?php
session_start();
require_once "../config/database.php";

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

$db = new Database();
$conn = $db->connect();

$sql = "SELECT * FROM users WHERE username = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // 
    if (password_verify($password,$user['password'])) {

        $_SESSION['user'] = [
            'id'       => $user['user_id'],
            'username' => $user['username'],
            'role'     => $user['role']
        ];

        // ✅ REDIRECT DÙNG RELATIVE PATH (KHÔNG 404)
        if ($user['role'] === 'ADMIN') {
            header("Location: ../admin/admin_view.php");
        } elseif ($user['role'] === 'ARTIST') {
            header("Location: ../artist/artist_view.php");
        } else {
            header("Location: ../user/home.php");
        }
        exit;

    } else {
        echo "Sai mật khẩu";
    }
} else {
    echo "Tài khoản không tồn tại";
}

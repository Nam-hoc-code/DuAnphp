<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../config/database.php";

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

$db = new Database();
$conn = $db->connect();

$sql = "SELECT * FROM users WHERE username = ? LIMIT 1";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("SQL prepare failed");
}

$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    if (password_verify($password, $user['password'])) {

        session_regenerate_id(true);

        $_SESSION['user'] = [
            'id'       => $user['user_id'],
            'username' => $user['username'],
            'role'     => $user['role']
        ];

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

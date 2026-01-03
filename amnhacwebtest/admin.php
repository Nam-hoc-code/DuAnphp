<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'ADMIN') {
    header("Location: auth/login.php");
    exit;
}
?>

<h2>Trang Admin</h2>
<a href="auth/logout.php">Đăng xuất</a>

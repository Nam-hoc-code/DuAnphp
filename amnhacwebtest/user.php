<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'USER') {
    header("Location: auth/login.php");
    exit;
}
?>

<h2>Xin chào <?php echo $_SESSION['username']; ?></h2>
<a href="auth/logout.php">Đăng xuất</a>

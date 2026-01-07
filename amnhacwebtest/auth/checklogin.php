<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Chưa đăng nhập
if (!isset($_SESSION['user'])) {
    header("Location: ../auth/loginform.php");
    exit;
}

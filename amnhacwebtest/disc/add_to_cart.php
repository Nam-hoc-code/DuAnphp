<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$disc_id = (int)($_POST['disc_id'] ?? 0);

if (!$disc_id) {
    die("Thiếu disc_id");
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

/* ❗ Không thêm trùng */
if (!isset($_SESSION['cart'][$disc_id])) {
    $_SESSION['cart'][$disc_id] = [
        'disc_id' => $disc_id,
        'title' => $_POST['title'],
        'price' => $_POST['price']
    ];
}

header("Location: disclist.php");
exit;

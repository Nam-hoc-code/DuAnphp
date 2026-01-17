<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['user_id'])) {
    die("Bạn chưa đăng nhập");
}

$user_id = $_SESSION['user_id'];
$disc_id = $_POST['disc_id'];

$db = new Database();
$conn = $db->connect();

/* Kiểm tra đĩa tồn tại */
$check = $conn->prepare("
    SELECT d.disc_id
    FROM disc d
    WHERE d.disc_id = ?
");
$check->bind_param("i", $disc_id);
$check->execute();
$result = $check->get_result();

if ($result->num_rows === 0) {
    die("Đĩa không tồn tại");
}

/* Tạo đơn hàng */
$insert = $conn->prepare("
    INSERT INTO disc_orders (disc_id, user_id, created_at)
    VALUES (?, ?, NOW())
");
$insert->bind_param("ii", $disc_id, $user_id);
$insert->execute();

echo "✅ Đặt mua thành công";

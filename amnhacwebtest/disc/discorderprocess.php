<?php
require_once '../config/db.php';
require_once '../auth_check.php';

if (!isset($_POST['disc_id'])) {
    die('Dữ liệu không hợp lệ');
}

$disc_id = (int)$_POST['disc_id'];
$user_id = $_SESSION['user_id'];

/* Kiểm tra disc có tồn tại không */
$check = $conn->prepare("SELECT disc_id FROM disc WHERE disc_id = ?");
$check->execute([$disc_id]);

if ($check->rowCount() === 0) {
    die('Đĩa không tồn tại');
}

/* Lưu đơn hàng */
$sql = "
    INSERT INTO disc_orders (disc_id, user_id, created_at)
    VALUES (?, ?, NOW())
";
$stmt = $conn->prepare($sql);
$stmt->execute([$disc_id, $user_id]);

header("Location: disclist.php?success=1");
exit;

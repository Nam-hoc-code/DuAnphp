<?php
require_once '../auth/check_login.php';
require_once '../config/database.php';

// chỉ cho ADMIN
if (($_SESSION['user']['role'] ?? '') !== 'ADMIN') {
    die('Bạn không có quyền xóa sự kiện');
}

$event_id = $_GET['id'] ?? null;
if (!$event_id) {
    die('Thiếu ID sự kiện');
}

$db = new Database();
$conn = $db->connect();

$sql = "DELETE FROM events WHERE event_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $event_id);

if ($stmt->execute()) {
    header("Location: event_list.php?msg=deleted");
    exit;
} else {
    echo "Xóa thất bại";
}

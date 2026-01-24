<?php
require_once '../auth/check_login.php';
require_once '../config/database.php';

if (($_SESSION['user']['role'] ?? '') !== 'ADMIN') {
    die('Bạn không có quyền');
}

$event_id   = $_POST['event_id'] ?? null;
$name       = trim($_POST['name'] ?? '');
$event_date = $_POST['event_date'] ?? '';
$price      = $_POST['price'] ?? 0;
$buy_url    = trim($_POST['buy_url'] ?? '');

if (!$event_id || !$name || !$event_date) {
    die('Thiếu dữ liệu');
}

$db = new Database();
$conn = $db->connect();

$sql = "UPDATE events 
        SET name = ?, event_date = ?, price = ?, buy_url = ?
        WHERE event_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssisi", $name, $event_date, $price, $buy_url, $event_id);

if ($stmt->execute()) {
    header("Location: event_list.php?msg=updated");
    exit;
} else {
    echo "Cập nhật thất bại";
}

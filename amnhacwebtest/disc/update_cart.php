<?php
require_once "../config/database.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['disc_id']) || !isset($data['qty'])) {
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
    exit;
}

$disc_id = intval($data['disc_id']);
$qty = max(1, intval($data['qty']));

// Cập nhật số lượng trong session
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['disc_id'] == $disc_id) {
            $item['qty'] = $qty;
            break;
        }
    }
}

echo json_encode(['success' => true, 'message' => 'Cập nhật thành công']);
exit;
?>
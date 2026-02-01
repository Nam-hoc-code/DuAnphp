<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $order_id = $data['order_id'] ?? 0;
    $user_id = $_SESSION['user']['user_id'];
    
    if (!$order_id) {
        echo json_encode(['success' => false, 'message' => 'Invalid Order ID']);
        exit;
    }
    
    $db = new Database();
    $conn = $db->connect();
    
    // Check if order exists and belongs to user
    // Also optional: We might want to update the main status to 'completed' as well if logic dictates
    // For now, just update is_received
    
    $sql = "UPDATE disc_orders SET is_received = 1 WHERE order_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $order_id, $user_id);
    
    if ($stmt->execute() && $stmt->affected_rows > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Update failed or no changes']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid method']);
}
?>

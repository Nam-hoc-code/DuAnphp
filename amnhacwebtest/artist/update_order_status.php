<?php
require_once "check_artist.php";
require_once "../config/database.php";

if (!isset($_POST['order_id'], $_POST['status'])) {
    die("Thiếu dữ liệu");
}

$order_id = (int) $_POST['order_id'];
$status   = $_POST['status'];

$allowed = ['confirmed', 'shipping', 'done'];
if (!in_array($status, $allowed)) {
    die("Trạng thái không hợp lệ");
}

$db = new Database();
$conn = $db->connect();

$sql = "
    UPDATE disc_orders
    SET status = ?
    WHERE order_id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $status, $order_id);
$stmt->execute();

header("Location: oders.php");
exit;

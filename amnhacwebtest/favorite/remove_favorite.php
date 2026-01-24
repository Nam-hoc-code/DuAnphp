<?php
require_once '../config/database.php';
require_once '../auth/check_login.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_POST['fav_id'])) {
    die('Thiếu fav_id');
}

$fav_id  = (int)$_POST['fav_id'];
$user_id = $_SESSION['user']['id']; // ✅ ĐÚNG SESSION

$db = new Database();
$conn = $db->connect(); // ✅ TẠO KẾT NỐI mysqli

/* Chỉ cho xóa favorite của chính mình */
$sql = "
    DELETE FROM favorites
    WHERE fav_id = ? AND user_id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $fav_id, $user_id);
$stmt->execute();

header("Location: favorite_list.php");
exit;

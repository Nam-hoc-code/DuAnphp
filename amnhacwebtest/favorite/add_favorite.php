<?php
require_once '../config/database.php';
require_once '../auth/check_login.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_POST['song_id'])) {
    die('Thiếu song_id');
}

$user_id = $_SESSION['user']['id']; // ✅ ĐÚNG SESSION
$song_id = (int)$_POST['song_id'];

$db = new Database();
$conn = $db->connect(); // ✅ mysqli connection

/* Kiểm tra đã favorite chưa */
$checkSql = "SELECT fav_id FROM favorites WHERE user_id = ? AND song_id = ?";
$check = $conn->prepare($checkSql);
$check->bind_param("ii", $user_id, $song_id);
$check->execute();
$check->store_result(); // ✅ BẮT BUỘC với mysqli

if ($check->num_rows === 0) {
    $insertSql = "
        INSERT INTO favorites (user_id, song_id, created_at)
        VALUES (?, ?, NOW())
    ";
    $stmt = $conn->prepare($insertSql);
    $stmt->bind_param("ii", $user_id, $song_id);
    $stmt->execute();
}

header("Location: favorite_list.php");
exit;

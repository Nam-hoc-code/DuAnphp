<?php
session_start();
require_once "../config/database.php";

// 1. Check login
if (!isset($_SESSION['user'])) {
    die("Access denied");
}

$userId = $_SESSION['user']['id'];
$songId = $_POST['song_id'] ?? null;

if (!$songId) {
    die("Thiếu song_id");
}

$db = new Database();
$conn = $db->connect();

// 2. Check đã favorite chưa
$checkSql = "SELECT fav_id FROM favorites WHERE user_id = ? AND song_id = ?";
$stmt = $conn->prepare($checkSql);
$stmt->bind_param("ii", $userId, $songId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Đã favorite → không làm gì
    header("Location: ../song_detail.php?id=" . $songId);
    exit;
}

// 3. Insert favorite
$insertSql = "INSERT INTO favorites (user_id, song_id) VALUES (?, ?)";
$stmt = $conn->prepare($insertSql);
$stmt->bind_param("ii", $userId, $songId);
$stmt->execute();

header("Location: ../song_detail.php?id=" . $songId);
exit;

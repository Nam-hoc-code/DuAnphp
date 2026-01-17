<?php
session_start();
require_once "../config/database.php";

$songId = $_GET['id'] ?? null;
if (!$songId) die("Không có bài hát");

$db = new Database();
$conn = $db->connect();

/* Lấy bài hát */
$sql = "SELECT * FROM songs WHERE song_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $songId);
$stmt->execute();
$song = $stmt->get_result()->fetch_assoc();

if (!$song) die("Bài hát không tồn tại");

/* Check favorite */
$isFavorite = false;
if (isset($_SESSION['user'])) {
    $userId = $_SESSION['user']['id'];

    $favSql = "SELECT fav_id FROM favorites WHERE user_id = ? AND song_id = ?";
    $stmt = $conn->prepare($favSql);
    $stmt->bind_param("ii", $userId, $songId);
    $stmt->execute();

    $isFavorite = $stmt->get_result()->num_rows > 0;
}
?>

<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['user'])) {
    die("Access denied");
}

$userId = $_SESSION['user']['id'];
$songId = $_GET['song_id'] ?? null;

if (!$songId) {
    die("Thiếu song_id");
}

$db = new Database();
$conn = $db->connect();

$sql = "DELETE FROM favorites WHERE user_id = ? AND song_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $userId, $songId);
$stmt->execute();

header("Location: favorite_list.php");
exit;

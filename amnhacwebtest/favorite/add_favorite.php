<?php
require_once '../config/database.php';
require_once '../check_login.php';

if (!isset($_POST['song_id'])) {
    die('Thiếu song_id');
}

$user_id = $_SESSION['user_id'];
$song_id = (int)$_POST['song_id'];

/* Kiểm tra đã favorite chưa */
$check = $conn->prepare(
    "SELECT fav_id FROM favorites WHERE user_id = ? AND song_id = ?"
);
$check->execute([$user_id, $song_id]);

if ($check->rowCount() === 0) {
    $sql = "
        INSERT INTO favorites (user_id, song_id, created_at)
        VALUES (?, ?, NOW())
    ";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$user_id, $song_id]);
}

header("Location: favorite_list.php");
exit;

<?php
session_start();
require_once "check_artist.php";
require_once "../config/database.php";

$db = new Database();
$conn = $db->connect();

$artist_id = $_SESSION['user_id']; // chắc chắn đã tồn tại

// Tổng bài hát
$sql1 = "SELECT COUNT(*) AS total FROM songs WHERE artist_id = ? AND is_deleted = 0";
$stmt1 = $conn->prepare($sql1);
$stmt1->bind_param("i", $artist_id);
$stmt1->execute();
$totalSongs = $stmt1->get_result()->fetch_assoc()['total'];

// Bài chờ duyệt
$sql2 = "SELECT COUNT(*) AS total FROM songs 
         WHERE artist_id = ? AND status = 'PENDING' AND is_deleted = 0";
$stmt2 = $conn->prepare($sql2);
$stmt2->bind_param("i", $artist_id);
$stmt2->execute();
$pendingSongs = $stmt2->get_result()->fetch_assoc()['total'];

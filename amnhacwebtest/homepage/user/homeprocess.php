<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../../config/database.php';

$db = (new Database())->connect();

/* =========================
   DANH SÁCH BÀI HÁT (SIDEBAR)
========================= */
$songList = [];
$sql = "SELECT * FROM songs WHERE is_deleted = 0 ORDER BY created_at DESC";
$result = $db->query($sql);
while ($row = $result->fetch_assoc()) {
    $songList[] = $row;
}

/* =========================
   BÀI HÁT THỊNH HÀNH
========================= */
$trendingSongs = [];
$sql = "SELECT * FROM songs WHERE is_deleted = 0 ORDER BY created_at DESC LIMIT 6";
$result = $db->query($sql);
while ($row = $result->fetch_assoc()) {
    $trendingSongs[] = $row;
}

/* =========================
   NGHỆ SĨ PHỔ BIẾN
========================= */
$popularArtists = [];
$sql = "SELECT DISTINCT artist FROM songs WHERE is_deleted = 0 LIMIT 5";
$result = $db->query($sql);
while ($row = $result->fetch_assoc()) {
    $popularArtists[] = $row;
}

/* =========================
   BÀI HÁT ĐANG ĐƯỢC CHỌN ĐỂ PHÁT
   (qua GET ?song_id=)
========================= */
$currentSong = null;

if (isset($_GET['song_id'])) {
    $song_id = (int) $_GET['song_id'];

    $stmt = $db->prepare(
        "SELECT * FROM songs 
         WHERE song_id = ? AND is_deleted = 0"
    );
    $stmt->bind_param("i", $song_id);
    $stmt->execute();

    $result = $stmt->get_result();
    $currentSong = $result->fetch_assoc();
}

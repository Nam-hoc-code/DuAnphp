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
$sql = "SELECT * FROM songs WHERE is_deleted = 0 LIMIT 6";
$result = $db->query($sql);
while ($row = $result->fetch_assoc()) {
    $trendingSongs[] = $row;
}

/* =========================
   NGHỆ SĨ PHỔ BIẾN
========================= */
$popularArtists = [];
$sql = "SELECT DISTINCT artist FROM songs LIMIT 5";
$result = $db->query($sql);
while ($row = $result->fetch_assoc()) {
    $popularArtists[] = $row;
}



<?php
require_once '../config/database.php';

$db = (new Database())->connect();

/* =========================
   DANH SÁCH BÀI HÁT (SIDEBAR)
========================= */
$sql = "
    SELECT
        s.song_id,
        s.title,
        s.cover_image,
        s.cloud_url,
        u.username AS artist_name
    FROM songs s
    JOIN users u ON s.artist_id = u.user_id
    WHERE s.status = 'APPROVED'
      AND s.is_deleted = 0
    ORDER BY s.created_at DESC
";
$result = $db->query($sql);
$songList = $result->fetch_all(MYSQLI_ASSOC);

/* =========================
   TRENDING SONGS
========================= */
$sql = "
    SELECT
        s.song_id,
        s.title,
        s.cover_image,
        s.cloud_url,
        u.username AS artist_name
    FROM songs s
    JOIN users u ON s.artist_id = u.user_id
    WHERE s.status = 'APPROVED'
      AND s.is_deleted = 0
    ORDER BY s.created_at DESC
    LIMIT 5
";
$result = $db->query($sql);
$trendingSongs = $result->fetch_all(MYSQLI_ASSOC);

/* =========================
   POPULAR ARTISTS
========================= */
$sql = "
    SELECT
        u.username,
        COUNT(*) AS total_songs
    FROM songs s
    JOIN users u ON s.artist_id = u.user_id
    WHERE s.status = 'APPROVED'
      AND s.is_deleted = 0
    GROUP BY u.user_id
    ORDER BY total_songs DESC
    LIMIT 5
";
$result = $db->query($sql);
$popularArtists = $result->fetch_all(MYSQLI_ASSOC);

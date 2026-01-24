<?php
require_once '../config/database.php';

$keyword = trim($_GET['q']);

$db = new Database();
$conn = $db->connect();

/*
 Tìm theo:
 - Tên bài hát
 - Tên nghệ sĩ
*/
$sql = "
    SELECT 
        s.song_id,
        s.title,
        s.cloud_url,
        u.username AS artist_name
    FROM songs s
    JOIN users u ON s.artist_id = u.user_id
    WHERE s.is_deleted = 0
      AND (
          s.title LIKE ?
          OR u.username LIKE ?
      )
    ORDER BY s.created_at DESC
";

$stmt = $conn->prepare($sql);

$search = '%' . $keyword . '%';
$stmt->bind_param("ss", $search, $search);
$stmt->execute();

$result = $stmt->get_result();
$results = $result->fetch_all(MYSQLI_ASSOC);

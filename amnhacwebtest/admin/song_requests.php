<?php
require_once __DIR__ . "/check_admin.php";
require_once __DIR__ . "/../config/database.php";

$db = new Database();
$conn = $db->connect();

$sql = "
SELECT s.song_id, s.title, u.username AS artist, s.created_at
FROM songs s
JOIN users u ON s.artist_id = u.user_id
WHERE s.status = 'PENDING'
ORDER BY s.created_at DESC
";

$result = $conn->query($sql);

echo "<h2>Danh sách bài hát chờ duyệt</h2>";

while ($row = $result->fetch_assoc()) {
    echo "
    <p>
        <b>{$row['title']}</b> - {$row['artist']}
        <a href='approve_song.php?id={$row['song_id']}'>[DUYỆT]</a>
        <a href='reject_song.php?id={$row['song_id']}'>[TỪ CHỐI]</a>
    </p>
    ";
}

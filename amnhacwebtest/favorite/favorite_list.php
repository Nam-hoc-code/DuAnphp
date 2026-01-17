<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['user'])) {
    die("Access denied");
}

$userId = $_SESSION['user']['id'];

$db = new Database();
$conn = $db->connect();

$sql = "
    SELECT f.fav_id, s.song_id, s.title, s.artist_name
    FROM favorites f
    JOIN songs s ON f.song_id = s.song_id
    WHERE f.user_id = ?
    ORDER BY f.created_at DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
?>

<h2>❤️ Bài hát yêu thích</h2>

<?php if ($result->num_rows === 0): ?>
    <p>Chưa có bài hát nào được yêu thích.</p>
<?php else: ?>
    <ul>
        <?php while ($row = $result->fetch_assoc()): ?>
            <li>
                🎵 <?= htmlspecialchars($row['title']) ?> - <?= htmlspecialchars($row['artist_name']) ?>
                |
                <a href="remove_favorite.php?song_id=<?= $row['song_id'] ?>">
                    ❌ Bỏ yêu thích
                </a>
            </li>
        <?php endwhile; ?>
    </ul>
<?php endif; ?>

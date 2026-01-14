<?php
require_once "check_artist.php";
require_once "../config/database.php";

$db = new Database();
$conn = $db->connect();

$artist_id = $_SESSION['user_id'];

$sql = "SELECT * FROM songs 
        WHERE artist_id = ? AND is_deleted = 0
        ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $artist_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<h2>Bài hát của tôi</h2>

<table border="1" cellpadding="8">
<tr>
    <th>Cover</th>
    <th>Tên bài</th>
    <th>Nghe</th>
    <th>Trạng thái</th>
    <th>Hành động</th>
</tr>

<?php while ($row = $result->fetch_assoc()): ?>
<tr>
    <td>
        <img src="<?= $row['cover_image'] ?>" width="80">
    </td>
    <td><?= htmlspecialchars($row['title']) ?></td>
    <td>
        <audio controls src="<?= $row['cloud_url'] ?>"></audio>
    </td>
    <td><?= $row['status'] ?></td>
    <td>
        <?php if ($row['status'] === 'PENDING'): ?>
            <a href="send_request.php?id=<?= $row['song_id'] ?>">
                📤 Gửi duyệt
            </a> |
        <?php endif; ?>

        <a href="delete_song.php?id=<?= $row['song_id'] ?>"
           onclick="return confirm('Xóa bài hát?')">
            🗑 Xóa
        </a>
    </td>
</tr>
<?php endwhile; ?>
</table>

<a href="dash_board.php">⬅ Dashboard</a>

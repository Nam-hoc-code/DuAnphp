<?php
require_once '../config/database.php';
require_once '../check_login.php';

$user_id = $_SESSION['user_id'];

$sql = "
    SELECT 
        f.fav_id,
        s.song_id,
        s.title,
        s.artist
    FROM favorites f
    JOIN songs s ON f.song_id = s.song_id
    WHERE f.user_id = ?
    ORDER BY f.created_at DESC
";
$stmt = $conn->prepare($sql);
$stmt->execute([$user_id]);
$favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Bài hát yêu thích</h2>

<?php if (empty($favorites)): ?>
    <p>Bạn chưa có bài hát yêu thích nào.</p>
<?php else: ?>
<table border="1" cellpadding="10">
    <tr>
        <th>Bài hát</th>
        <th>Nghệ sĩ</th>
        <th></th>
    </tr>

    <?php foreach ($favorites as $fav): ?>
    <tr>
        <td><?= htmlspecialchars($fav['title']) ?></td>
        <td><?= htmlspecialchars($fav['artist']) ?></td>
        <td>
            <form action="remove_favorite.php" method="POST" style="display:inline;">
                <input type="hidden" name="fav_id" value="<?= $fav['fav_id'] ?>">
                <button type="submit">❌ Xóa</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<?php endif; ?>

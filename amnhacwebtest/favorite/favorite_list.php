<?php
require_once '../config/database.php';
require_once '../auth/check_login.php';

$user_id = $_SESSION['user']['id'];

$db = new Database();
$conn = $db->connect();

$sql = "
    SELECT 
        f.fav_id,
        s.song_id,
        s.title,
        s.cloud_url,
        u.username AS artist_name,
        f.created_at
    FROM favorites f
    JOIN songs s ON f.song_id = s.song_id
    JOIN users u ON s.artist_id = u.user_id
    WHERE f.user_id = ?
    ORDER BY f.created_at DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$favorites = $result->fetch_all(MYSQLI_ASSOC);
?>

<h2>🎧 Bài hát yêu thích</h2>

<?php if (empty($favorites)): ?>
    <p>Bạn chưa có bài hát yêu thích nào.</p>
<?php else: ?>

<table border="1" cellpadding="10">
    <tr>
        <th>Bài hát</th>
        <th>Nghệ sĩ</th>
        <th>Hành động</th>
    </tr>

    <?php foreach ($favorites as $fav): ?>
    <tr>
        <!-- CLICK PHÁT NHẠC -->
        <td>
            <a href="#"
               onclick="playSong('<?= htmlspecialchars($fav['cloud_url']) ?>', '<?= htmlspecialchars($fav['title']) ?>'); return false;">
                ▶ <?= htmlspecialchars($fav['title']) ?>
            </a>
        </td>

        <td><?= htmlspecialchars($fav['artist_name']) ?></td>

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

<hr>

<!-- 🎵 PLAYER -->
<h3 id="nowPlaying">Chưa phát bài nào</h3>
<audio id="audioPlayer" controls style="width:100%">
    <source src="" type="audio/mpeg">
</audio>

<script>
function playSong(url, title) {
    const player = document.getElementById('audioPlayer');
    const nowPlaying = document.getElementById('nowPlaying');

    player.src = url;
    player.play();

    nowPlaying.innerText = "🎶 Đang phát: " + title;
}
</script>

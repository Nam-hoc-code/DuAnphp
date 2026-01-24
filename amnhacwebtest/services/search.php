<?php

$results = [];
$keyword = '';

if (isset($_GET['q']) && trim($_GET['q']) !== '') {
    require_once 'searchprocess.php';
}
?>

<h2>🔍 Tìm kiếm</h2>

<form method="GET" action="search.php">
    <input type="text"
           name="q"
           placeholder="Nhập tên bài hát hoặc nghệ sĩ..."
           value="<?= htmlspecialchars($keyword) ?>"
           required>
    <button type="submit">Tìm</button>
</form>

<hr>

<?php if (!empty($keyword)): ?>
    <h3>Kết quả cho: "<strong><?= htmlspecialchars($keyword) ?></strong>"</h3>
<?php endif; ?>

<?php if (!empty($results)): ?>
    <table border="1" cellpadding="10">
        <tr>
            <th>Bài hát</th>
            <th>Nghệ sĩ</th>
            <th>Nghe</th>
        </tr>

        <?php foreach ($results as $song): ?>
        <tr>
            <td><?= htmlspecialchars($song['title']) ?></td>
            <td><?= htmlspecialchars($song['artist_name']) ?></td>
            <td>
                <button onclick="playSong('<?= $song['cloud_url'] ?>', '<?= htmlspecialchars($song['title']) ?>')">
                    ▶ Phát
                </button>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
<?php elseif (!empty($keyword)): ?>
    <p>❌ Không tìm thấy kết quả phù hợp.</p>
<?php endif; ?>

<hr>

<!-- PLAYER -->
<h3 id="nowPlaying">Chưa phát bài nào</h3>
<audio id="audioPlayer" controls style="width:100%"></audio>

<script>
function playSong(url, title) {
    const player = document.getElementById('audioPlayer');
    document.getElementById('nowPlaying').innerText = "🎶 Đang phát: " + title;
    player.src = url;
    player.play();
}
</script>


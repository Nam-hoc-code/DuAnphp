<?php
require_once '../auth/check_login.php';
require_once '../config/database.php';
require_once '../partials/header.php';
require_once '../partials/sidebar.php';

if (!isset($_GET['id'])) {
    die('Thiếu artist id');
}

$artistId = (int)$_GET['id'];
$conn = (new Database())->connect();

/* ===== LẤY ARTIST ===== */
$sql = "SELECT user_id, username, avatar FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $artistId);
$stmt->execute();
$artist = $stmt->get_result()->fetch_assoc();

if (!$artist) {
    die('Artist không tồn tại');
}

/* ===== LẤY BÀI HÁT (ĐÚNG THEO DB) ===== */

$sql = "
    SELECT song_id, title, cover_image, cloud_url
    FROM songs
    WHERE artist_id = ?
      AND status = 'APPROVED'
      AND is_deleted = 0
    ORDER BY created_at DESC

";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $artistId);
$stmt->execute();
$songs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$defaultCover = '../assets/images/default-cover.png';
?>

<main style="margin-left:260px;padding:80px;color:white">

    <h1><?= htmlspecialchars($artist['username']) ?></h1>

    <?php if (!empty($artist['avatar'])): ?>
        <img src="<?= htmlspecialchars('../' . $artist['avatar']) ?>" width="120">
    <?php endif; ?>

    <p><?= count($songs) ?> bài hát</p>

    <hr>

    <h2>Bài hát</h2>

    <?php if (empty($songs)): ?>
        <p>Chưa có bài hát</p>
    <?php else: ?>
        <ul>
            <?php foreach ($songs as $song): ?>
                <li style="margin-bottom:12px">
                    <a href="song_play.php?id=<?= $song['song_id'] ?>" style="color:white">
                        <img 
                            src="<?= htmlspecialchars($song['cover_image'] ?: $defaultCover) ?>"
                            width="60"
                            style="vertical-align:middle"
                        >
                        <?= htmlspecialchars($song['title']) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

</main>

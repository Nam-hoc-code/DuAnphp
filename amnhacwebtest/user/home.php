<?php
require_once '../auth/check_login.php';
require_once '../partials/header.php';
require_once '../partials/sidebar.php';
require_once 'homeprocess.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET['song_id'])) {
    foreach ($songList as $song) {
        if ($song['song_id'] == $_GET['song_id']) {
            $_SESSION['current_song'] = $song;
            break;
        }
    }
}

/* Ảnh mặc định khi thiếu cover */
$defaultCover = '../assets/images/default-cover.png';
?>

<main style="display:flex; gap:20px; padding-bottom:80px">

<!-- ===== DANH SÁCH BÀI HÁT ===== -->
<aside style="width:25%">
    <h3>Danh sách bài hát</h3>

    <?php foreach ($songList as $song): ?>
        <?php
            $cover = (!empty($song['cover_image']))
                ? $song['cover_image']
                : $defaultCover;
        ?>

        <div style="display:flex; align-items:center; gap:10px; margin-bottom:8px">

            <!-- Ảnh bài hát -->
            <img
                src="<?= htmlspecialchars($cover) ?>"
                alt="cover"
                style="width:45px;height:45px;object-fit:cover;border-radius:6px"
            >

            <div style="flex:1">
                <a href="home.php?song_id=<?= $song['song_id'] ?>">
                    ▶ <?= htmlspecialchars($song['title']) ?>
                </a>
            </div>

            <!-- ❤️ FAVORITE -->
            <form action="../favorite/add_favorite.php" method="POST" style="margin:0">
                <input type="hidden" name="song_id" value="<?= $song['song_id'] ?>">
                <button type="submit"
                    style="border:none;background:none;cursor:pointer;font-size:16px">
                    ❤️
                </button>
            </form>
        </div>
    <?php endforeach; ?>
</aside>

<!-- ===== NỘI DUNG CHÍNH ===== -->
<section style="width:75%">
    <h2>Bài hát thịnh hành</h2>

    <?php foreach ($trendingSongs as $song): ?>
        <?php
            $cover = (!empty($song['cover_image']))
                ? $song['cover_image']
                : $defaultCover;
        ?>

        <div style="display:flex; align-items:center; gap:12px; margin-bottom:10px">

            <img
                src="<?= htmlspecialchars($cover) ?>"
                alt="cover"
                style="width:60px;height:60px;object-fit:cover;border-radius:8px"
            >

            <div>
                <strong><?= htmlspecialchars($song['title']) ?></strong><br>
                <small><?= htmlspecialchars($song['artist_name']) ?></small>
            </div>
        </div>
    <?php endforeach; ?>

    <h2>Nghệ sĩ phổ biến</h2>
    <?php foreach ($popularArtists as $artist): ?>
        <div><?= htmlspecialchars($artist['username']) ?></div>
    <?php endforeach; ?>
</section>

</main>

<?php require_once '../partials/player.php'; ?>

<?php
/*********************************
 * AUTH
 *********************************/
// require_once '../auth_check.php';

/*********************************
 * BACKEND LOGIC
 * (Controller nhẹ cho trang home)
 *********************************/
require_once 'homeprocess.php';

/* XỬ LÝ PHÁT NHẠC */
$playSong = null;
if (isset($_GET['song_id'])) {
    foreach ($songList as $song) {
        if ($song['song_id'] == $_GET['song_id']) {
            $playSong = $song;
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Home - Music Website</title>
</head>

<body>

<!-- ===================================== -->
<!-- TOP BAR (Frontend phụ trách giao diện) -->
<!-- ===================================== -->
<header>

    <!-- LOGO -->
    <div>
        <!-- LOGO UI -->
    </div>

    <!-- SEARCH -->
    <form method="GET" action="searchprocess.php">
        <input type="text" name="keyword" placeholder="Tìm kiếm bài hát">
    </form>

    <!-- USER INFO -->
    <div>
        Xin chào, <?= $_SESSION['user']['username'] ?>
        <a href="../../auth/logout.php">Đăng xuất</a>
    </div>

</header>

<!-- ===================================== -->
<!-- MAIN CONTENT -->
<!-- ===================================== -->
<main>

    <!-- ========== SIDEBAR ========== -->
    <aside>
        <h3>Danh sách bài hát</h3>

        <?php foreach ($songList as $song): ?>
            <div>
                <a href="home.php?song_id=<?= $song['song_id'] ?>">
                    ▶ <?= $song['title'] ?>
                </a>
            </div>
        <?php endforeach; ?>
    </aside>

    <!-- ========== CONTENT ========== -->
    <section>

        <!-- MUSIC PLAYER -->
        <?php if ($playSong): ?>
            <h2>Đang phát</h2>
            <p>
                🎵 <b><?= $playSong['title'] ?></b> – <?= $playSong['artist'] ?>
            </p>

            <audio controls autoplay>
                <source src="../../<?= $playSong['file_path'] ?>" type="audio/mpeg">
            </audio>
            <hr>
        <?php else: ?>
            <p>🎧 Chọn bài hát để phát</p>
            <hr>
        <?php endif; ?>

        <!-- TRENDING -->
        <h2>Những bài hát thịnh hành</h2>
        <?php foreach ($trendingSongs as $song): ?>
            <div><?= $song['title'] ?> - <?= $song['artist'] ?></div>
        <?php endforeach; ?>

        <!-- ARTISTS -->
        <h2>Nghệ sĩ phổ biến</h2>
        <?php foreach ($popularArtists as $artist): ?>
            <div><?= $artist['artist'] ?></div>
        <?php endforeach; ?>

    </section>

</main>

</body>
</html>

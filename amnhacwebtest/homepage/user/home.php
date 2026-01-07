<?php
require_once '../../auth/auth_check.php';
require_once 'homeprocess.php';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Home - Music Website</title>
</head>
<body>

<!-- ============================= -->
<!-- TOP BAR -->
<!-- ============================= -->
<header>

    <!-- LOGO -->
    <div>
        <!-- Frontend làm logo -->
    </div>

    <!-- SEARCH -->
    <form method="GET" action="searchprocess.php">
        <input type="text" name="keyword" placeholder="Tìm kiếm bài hát">
    </form>

    <!-- USER -->
    <div>
        <!-- Backend đã có session -->
        Xin chào, <?= $_SESSION['user']['username'] ?>
        <a href="../../auth/logout.php">Đăng xuất</a>
    </div>

</header>

<!-- ============================= -->
<!-- MAIN CONTENT -->
<!-- ============================= -->
<main>

    <!-- SIDEBAR -->
    <aside>
        <h3>Danh sách bài hát</h3>

        <?php foreach ($songList as $song): ?>
            <div>
                <?= $song['title'] ?>
            </div>
        <?php endforeach; ?>
    </aside>

    <!-- CONTENT -->
    <section>

        <!-- TRENDING -->
        <h2>Những bài hát thịnh hành</h2>
        <?php foreach ($trendingSongs as $song): ?>
            <div>
                <?= $song['title'] ?> - <?= $song['artist'] ?>
            </div>
        <?php endforeach; ?>

        <!-- ARTISTS -->
        <h2>Nghệ sĩ phổ biến</h2>
        <?php foreach ($popularArtists as $artist): ?>
            <div>
                <?= $artist['artist'] ?>
            </div>
        <?php endforeach; ?>

    </section>

</main>

</body>
</html>

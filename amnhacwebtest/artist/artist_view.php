
<?php
require_once __DIR__ . "/check_artist.php";
require_once __DIR__ . "/../config/database.php"; 
require_once "dash_board.php"; ?>

<h2>🎤 DASHBOARD NGHỆ SĨ</h2>

<p>🎵 Tổng bài hát: <b><?= $totalSongs ?></b></p>
<p>⏳ Chờ duyệt: <b><?= $pendingSongs ?></b></p>

<hr>

<ul>
    <li><a href="add_song.php">➕ Thêm bài hát</a></li>
    <li><a href="my_songs.php">🎶 Bài hát của tôi</a></li>
    <li><a href="../auth/logout.php">🚪 Đăng xuất</a></li>
</ul>

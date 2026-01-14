<?php 
require_once __DIR__ . "/check_admin.php";
require_once __DIR__ . "/../config/database.php";
require_once "dash_board.php"
?>
<h2>ADMIN DASHBOARD</h2>

<p>👤 Tổng người dùng: <b><?= $totalUsers ?></b></p>
<p>🎵 Tổng bài hát: <b><?= $totalSongs ?></b></p>
<p>⏳ Bài chờ duyệt: <b><?= $pendingSongs ?></b></p>

<hr>

<ul>
    <li><a href="song_requests.php">📥 Duyệt bài hát</a></li>
    <li><a href="../auth/logout.php">🚪 Đăng xuất</a></li>
</ul>

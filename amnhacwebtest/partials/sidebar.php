<aside style="width:220px; float:left; padding:15px; background:#f5f5f5;">
    <ul style="list-style:none; padding:0;">
        <li><a href="/home.php">🏠 Trang chủ</a></li>
        <li><a href="/songs/songlist.php">🎶 Bài hát</a></li>
        <li><a href="/favorite/favorite_list.php">❤️ Yêu thích</a></li>
        <li><a href="/favorite/top_favorite_songs.php">🔥 Top yêu thích</a></li>
        <li><a href="/disc/disclist.php">💿 Mua đĩa</a></li>
        <li><a href="/event/eventlist.php">🎫 Sự kiện</a></li>

        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'artist'): ?>
            <hr>
            <li><a href="/artist/mysongs.php">🎤 Nhạc của tôi</a></li>
        <?php endif; ?>

        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <hr>
            <li><a href="/admin/dashboard.php">🛠 Admin</a></li>
        <?php endif; ?>
    </ul>
</aside>

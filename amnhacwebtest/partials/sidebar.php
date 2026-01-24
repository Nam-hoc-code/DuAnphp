<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<aside style="width:220px; float:left; padding:15px; background:#f5f5f5;">
    <ul style="list-style:none; padding:0;">

        <li><a href="../user/home.php">🏠 Trang chủ</a></li>
        <li><a href="../favorite/favorite_list.php">❤️ Yêu thích</a></li>
        <li><a href="../disc/disclist.php">💿 Mua đĩa</a></li>
        <li><a href="../event/event_list.php">🎫 Sự kiện</a></li>
        <li><a href="../services/search.php">Tìm kiếm</a></li>

        <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'artist'): ?>
            <hr>
            <li><a href="../artist/mysongs.php">🎤 Nhạc của tôi</a></li>
        <?php endif; ?>

        <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>
            <hr>
            <li><a href="../admin/dashboard.php">🛠 Admin</a></li>
        <?php endif; ?>

    </ul>
</aside>

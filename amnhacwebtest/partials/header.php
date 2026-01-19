<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<header style="padding:15px; background:#111; color:#fff;">
    <h2 style="display:inline;">🎵 Music Platform</h2>

    <div style="float:right;">
        <?php if (isset($_SESSION['user_id'])): ?>
            Xin chào, <b><?= $_SESSION['username'] ?? 'User' ?></b>
            | <a href="/logout.php" style="color:#0f0;">Đăng xuất</a>
        <?php else: ?>
            <a href="/login.php" style="color:#0f0;">Đăng nhập</a>
        <?php endif; ?>
    </div>
    <div style="clear:both;"></div>
</header>

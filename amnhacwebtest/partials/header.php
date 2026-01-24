<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<header style="padding:15px; background:#111; color:#fff;">
    <h2 style="display:inline;">🎵 Music Platform</h2>

    <div style="float:right;">
        <?php if (isset($_SESSION['user'])): ?>
            Xin chào, <b><?= htmlspecialchars($_SESSION['user']['username']) ?></b>
            | <a href="../auth/logout.php" style="color:#0f0;">Đăng xuất</a>
        <?php else: ?>
            <a href="../auth/login_form.php" style="color:#0f0;">Đăng nhập</a>
        <?php endif; ?>
    </div>
            
    <div style="clear:both;"></div>
</header>

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$currentSong = $_SESSION['current_song'] ?? null;
?>

<div style="
    position:fixed;
    bottom:0;
    left:0;
    right:0;
    background:#222;
    color:#fff;
    padding:10px;
">
    <?php if ($currentSong): ?>
        <p>
            🎵 <b><?= htmlspecialchars($currentSong['title']) ?></b>
            – <?= htmlspecialchars($currentSong['artist_name']) ?>
        </p>

        <audio controls autoplay style="width:100%">
            <source src="<?= htmlspecialchars($currentSong['cloud_url']) ?>" type="audio/mpeg">
        </audio>
    <?php else: ?>
        <p style="text-align:center;">🎧 Chọn bài hát để phát</p>
    <?php endif; ?>
</div>

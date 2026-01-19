<?php
$songUrl = $_GET['song_url'] ?? null;
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
    <?php if ($songUrl): ?>
        <audio controls autoplay style="width:100%;">
            <source src="<?= htmlspecialchars($songUrl) ?>" type="audio/mpeg">
            Trình duyệt không hỗ trợ audio.
        </audio>
    <?php else: ?>
        <p style="text-align:center;">🎧 Chọn bài hát để phát</p>
    <?php endif; ?>
</div>

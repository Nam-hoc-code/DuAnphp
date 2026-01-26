<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$currentSong = $_SESSION['current_song'] ?? null;
?>

<style>
    .player-bar {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        height: 90px;
        background: var(--player-bg);
        border-top: 1px solid #282828;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 16px;
        z-index: 1100;
    }

    .player-song-info {
        display: flex;
        align-items: center;
        width: 30%;
        gap: 14px;
    }

    .player-song-img {
        width: 56px;
        height: 56px;
        border-radius: 4px;
        object-fit: cover;
        background: #282828;
    }

    .player-song-details {
        display: flex;
        flex-direction: column;
    }

    .player-song-title {
        font-size: 14px;
        font-weight: 600;
        color: #fff;
    }

    .player-song-artist {
        font-size: 12px;
        color: var(--text-sub);
    }

    .player-controls {
        width: 40%;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
    }

    .player-buttons {
        display: flex;
        align-items: center;
        gap: 24px;
        font-size: 18px;
        color: #b3b3b3;
    }

    .player-btn-play {
        width: 32px;
        height: 32px;
        background: #fff;
        color: #000;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
    }

    .player-progress {
        width: 100%;
        max-width: 500px;
    }

    audio {
        width: 100%;
        height: 32px;
        filter: invert(100%) hue-rotate(180deg) brightness(1.5);
    }

    .player-volume {
        width: 30%;
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 12px;
        color: #b3b3b3;
    }

    .empty-player {
        text-align: center;
        width: 100%;
        color: var(--text-sub);
        font-style: italic;
        font-size: 14px;
    }
</style>

<div class="player-bar">
    <?php if ($currentSong): ?>
        <div class="player-song-info">
            <img src="<?= htmlspecialchars($currentSong['cover_image'] ?? '../assets/images/default-cover.png') ?>" 
                 class="player-song-img" alt="cover">
            <div class="player-song-details">
                <span class="player-song-title"><?= htmlspecialchars($currentSong['title']) ?></span>
                <span class="player-song-artist"><?= htmlspecialchars($currentSong['artist_name'] ?? 'Artist') ?></span>
            </div>
        </div>

        <div class="player-controls">
            <div class="player-progress">
                <audio controls autoplay>
                    <source src="<?= htmlspecialchars($currentSong['cloud_url']) ?>" type="audio/mpeg">
                </audio>
            </div>
        </div>

        <div class="player-volume">
            <i class="fa-solid fa-volume-high"></i>
            <div style="width: 100px; height: 4px; background: #4d4d4d; border-radius: 2px; position: relative;">
                <div style="width: 70%; height: 100%; background: var(--spotify-green); border-radius: 2px;"></div>
            </div>
        </div>
    <?php else: ?>
        <div class="empty-player">
            <i class="fa-solid fa-music" style="margin-right: 10px;"></i>
            Chọn một bài hát để bắt đầu trải nghiệm âm nhạc
        </div>
    <?php endif; ?>
</div>
</div> <!-- Close app-container from header -->
</body>
</html>

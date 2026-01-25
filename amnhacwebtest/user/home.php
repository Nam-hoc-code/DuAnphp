<?php
require_once '../auth/check_login.php';
require_once '../partials/header.php';
require_once '../partials/sidebar.php';
require_once 'homeprocess.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET['song_id'])) {
    foreach ($songList as $song) {
        if ($song['song_id'] == $_GET['song_id']) {
            $_SESSION['current_song'] = $song;
            break;
        }
    }
}

/* Ảnh mặc định khi thiếu cover */
$defaultCover = '../assets/images/default-cover.png';
?>

<style>
    .main-content {
        margin-left: 260px;
        padding: 80px 32px 120px 32px;
        width: calc(100% - 260px);
        min-height: 100vh;
        background: linear-gradient(to bottom, #1e1e1e, var(--bg-black) 40%);
    }

    .section-title {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 24px;
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
    }

    .section-title a {
        font-size: 12px;
        text-transform: uppercase;
        color: var(--text-sub);
        letter-spacing: 1px;
    }

    .section-title a:hover { text-decoration: underline; }

    .grid-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 24px;
        margin-bottom: 48px;
    }

    .song-card {
        background: #181818;
        padding: 16px;
        border-radius: 8px;
        transition: background 0.3s ease;
        cursor: pointer;
        position: relative;
    }

    .song-card:hover { background: #282828; }

    .card-img-wrapper {
        position: relative;
        width: 100%;
        padding-bottom: 100%;
        margin-bottom: 16px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.5);
        border-radius: 4px;
        overflow: hidden;
    }

    .card-img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .play-btn-overlay {
        position: absolute;
        bottom: 8px;
        right: 8px;
        width: 48px;
        height: 48px;
        background: var(--spotify-green);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #000;
        font-size: 20px;
        box-shadow: 0 8px 16px rgba(0,0,0,0.3);
        opacity: 0;
        transform: translateY(8px);
        transition: all 0.3s ease;
    }

    .song-card:hover .play-btn-overlay {
        opacity: 1;
        transform: translateY(0);
    }

    .card-title {
        font-weight: 700;
        font-size: 16px;
        margin-bottom: 8px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .card-subtitle {
        color: var(--text-sub);
        font-size: 14px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .list-container {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .list-item {
        display: flex;
        align-items: center;
        padding: 8px 16px;
        border-radius: 4px;
        transition: background 0.2s;
    }

    .list-item:hover { background: rgba(255,255,255,0.1); }

    .list-item img {
        width: 40px;
        height: 40px;
        border-radius: 4px;
        margin-right: 16px;
        object-fit: cover;
    }

    .artist-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: 24px;
    }

    .artist-card {
        background: #181818;
        padding: 16px;
        border-radius: 8px;
        text-align: center;
        transition: background 0.3s;
    }

    .artist-card:hover { background: #282828; }

    .artist-img {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        margin: 0 auto 16px;
        object-fit: cover;
        box-shadow: 0 8px 24px rgba(0,0,0,0.5);
    }

    .fav-btn {
        background: none;
        border: none;
        color: var(--text-sub);
        cursor: pointer;
        font-size: 16px;
        transition: color 0.2s;
    }

    .fav-btn:hover { color: #fff; transform: scale(1.1); }
</style>

<main class="main-content">
    
    <div class="section-title">
        <span>Danh sách bài hát</span>
        <a href="#">Xem tất cả</a>
    </div>

    <div class="grid-container">
        <?php foreach ($songList as $song): ?>
            <?php
                $cover = (!empty($song['cover_image'])) ? $song['cover_image'] : $defaultCover;
            ?>
            <div class="song-card" onclick="window.location.href='home.php?song_id=<?= $song['song_id'] ?>'">
                <div class="card-img-wrapper">
                    <img src="<?= htmlspecialchars($cover) ?>" class="card-img" alt="cover">
                    <div class="play-btn-overlay">
                        <i class="fa-solid fa-play"></i>
                    </div>
                </div>
                <div class="card-title"><?= htmlspecialchars($song['title']) ?></div>
                <div class="card-subtitle">
                    <?= htmlspecialchars($song['artist_name'] ?? 'Nghệ sĩ') ?>
                    <form action="../favorite/add_favorite.php" method="POST" style="display:inline; float:right;" onclick="event.stopPropagation();">
                        <input type="hidden" name="song_id" value="<?= $song['song_id'] ?>">
                        <button type="submit" class="fav-btn" title="Thêm vào yêu thích">
                            <i class="fa-regular fa-heart"></i>
                        </button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="section-title">
        <span>Bài hát thịnh hành</span>
        <a href="#">Khám phá thêm</a>
    </div>

    <div class="list-container">
        <?php foreach ($trendingSongs as $song): ?>
            <?php
                $cover = (!empty($song['cover_image'])) ? $song['cover_image'] : $defaultCover;
            ?>
            <div class="list-item">
                <img src="<?= htmlspecialchars($cover) ?>" alt="cover">
                <div style="flex: 1;">
                    <div style="font-weight: 600;"><?= htmlspecialchars($song['title']) ?></div>
                    <div style="font-size: 13px; color: var(--text-sub);"><?= htmlspecialchars($song['artist_name']) ?></div>
                </div>
                <div style="color: var(--text-sub); font-size: 13px; margin: 0 20px;">
                    <i class="fa-solid fa-chart-line" style="margin-right: 8px;"></i> Trending
                </div>
                <button onclick="window.location.href='home.php?song_id=<?= $song['song_id'] ?>'" 
                        style="background:none; border:none; color: #fff; cursor:pointer; font-size: 18px;">
                    <i class="fa-solid fa-play"></i>
                </button>
            </div>
        <?php endforeach; ?>
    </div>

    <h2 class="section-title" style="margin-top: 48px;">Nghệ sĩ phổ biến</h2>
    <div class="artist-grid">
        <?php foreach ($popularArtists as $artist): ?>
            <div class="artist-card">
                <div class="artist-img" style="background: #282828; display: flex; align-items: center; justify-content: center;">
                    <i class="fa-solid fa-user" style="font-size: 48px; color: #535353;"></i>
                </div>
                <div style="font-weight: 700; margin-bottom: 4px;"><?= htmlspecialchars($artist['username']) ?></div>
                <div style="font-size: 13px; color: var(--text-sub);">Nghệ sĩ</div>
            </div>
        <?php endforeach; ?>
    </div>

</main>

<?php require_once '../partials/player.php'; ?>

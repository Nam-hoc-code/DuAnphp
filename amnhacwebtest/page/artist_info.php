<?php
require_once '../auth/check_login.php';
require_once '../config/database.php';
require_once '../partials/header.php';
require_once '../partials/sidebar.php';

if (!isset($_GET['id'])) {
    die('Thiếu artist id');
}

$artistId = (int)$_GET['id'];
$conn = (new Database())->connect();

/* ===== LẤY ARTIST ===== */
$sql = "SELECT user_id, username, avatar FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $artistId);
$stmt->execute();
$artist = $stmt->get_result()->fetch_assoc();

if (!$artist) {
    die('Artist không tồn tại');
}

/* ===== LẤY BÀI HÁT (ĐÚNG THEO DB) ===== */
$sql = "
    SELECT song_id, title, cover_image, cloud_url
    FROM songs
    WHERE artist_id = ?
      AND status = 'APPROVED'
      AND is_deleted = 0
    ORDER BY created_at DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $artistId);
$stmt->execute();
$songs_result = $stmt->get_result();
$songs = [];
while ($row = $songs_result->fetch_assoc()) {
    // Kiểm tra xem bài hát có trong danh sách yêu thích không
    $userId = $_SESSION['user']['user_id'] ?? 0;
    $favSql = "SELECT favorite_id FROM favorites WHERE user_id = ? AND song_id = ?";
    $favStmt = $conn->prepare($favSql);
    $favStmt->bind_param("ii", $userId, $row['song_id']);
    $favStmt->execute();
    $row['is_favorite'] = $favStmt->get_result()->num_rows > 0;
    $songs[] = $row;
}

$defaultCover = '../assets/images/default-cover.png';
$artistAvatar = (!empty($artist['avatar'])) ? '../' . $artist['avatar'] : '../assets/images/default-artist.png';
?>

<style>
    .artist-page {
        margin-left: 260px;
        min-height: 100vh;
        background: #121212;
        padding-bottom: 100px;
    }

    /* Hero Header */
    .artist-hero {
        height: 45vh;
        min-height: 340px;
        max-height: 500px;
        position: relative;
        display: flex;
        align-items: flex-end;
        padding: 0 32px 24px 32px;
        background-size: cover;
        background-position: center 20%;
        color: #fff;
    }

    .artist-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(transparent 0, rgba(0,0,0,0.2) 50%, rgba(18,18,18,1) 100%);
        z-index: 1;
    }

    .hero-content {
        position: relative;
        z-index: 2;
        display: flex;
        flex-direction: column;
        gap: 0;
    }

    .verified-badge {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 14px;
        font-weight: 500;
        margin-bottom: 8px;
    }

    .verified-badge i {
        color: #3d91ff;
        font-size: 20px;
    }

    .artist-name {
        font-size: clamp(48px, 8vw, 96px);
        font-weight: 900;
        margin: 0;
        letter-spacing: -2px;
        line-height: 1;
    }

    .listener-count {
        margin-top: 12px;
        font-size: 16px;
        font-weight: 500;
        text-shadow: 0 2px 4px rgba(0,0,0,0.5);
    }

    /* Action Bar */
    .action-bar {
        padding: 24px 32px;
        display: flex;
        align-items: center;
        gap: 24px;
        position: relative;
    }

    .btn-play-big {
        width: 56px;
        height: 56px;
        background: var(--spotify-green);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: #000;
        border: none;
        cursor: pointer;
        transition: transform 0.1s ease, background-color 0.2s ease;
        box-shadow: 0 8px 16px rgba(0,0,0,0.3);
    }

    .btn-play-big:hover {
        transform: scale(1.05);
        background: #1ed760;
    }

    .btn-follow {
        background: transparent;
        border: 1px solid rgba(255,255,255,0.3);
        color: #fff;
        padding: 7px 15px;
        border-radius: 4px;
        font-weight: 700;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 1px;
        cursor: pointer;
        margin-left: 8px;
    }

    .btn-follow:hover {
        border-color: #fff;
    }

    .icon-btn {
        background: transparent;
        border: none;
        color: var(--text-sub);
        font-size: 22px;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .icon-btn:hover {
        color: #fff;
        transform: scale(1.05);
    }

    .icon-btn i.fa-solid.fa-heart {
        color: var(--spotify-green);
    }

    /* Popular Section */
    .popular-section {
        padding: 0 32px;
    }

    .section-title {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 20px;
        color: #fff;
    }

    .song-table {
        width: 100%;
        border-collapse: collapse;
    }

    .song-row {
        height: 56px;
        transition: background 0.2s;
        border-radius: 4px;
        cursor: pointer;
    }

    .song-row:hover {
        background: rgba(255,255,255,0.1);
    }

    .song-row td {
        padding: 0 16px;
    }

    .rank-col {
        width: 40px;
        color: var(--text-sub);
        text-align: right;
        font-size: 16px;
        font-weight: 400;
    }

    .title-col {
        display: flex;
        align-items: center;
        gap: 16px;
        height: 56px;
    }

    .song-thumb {
        width: 40px;
        height: 40px;
        border-radius: 0; /* Square thumbnails like image */
        object-fit: cover;
        box-shadow: 0 4px 8px rgba(0,0,0,0.3);
    }

    .song-name {
        font-weight: 500;
        font-size: 16px;
        color: #fff;
    }

    .play-count-col {
        color: var(--text-sub);
        font-size: 14px;
        text-align: left;
        padding-left: 20px !important;
    }

    .duration-col {
        color: var(--text-sub);
        font-size: 14px;
        text-align: right;
        width: 80px;
    }
</style>

<div class="artist-page">
    <!-- Hero Header -->
    <div class="artist-hero" style="background-image: url('<?= htmlspecialchars($artistAvatar) ?>');">
        <div class="hero-content">
            <div class="verified-badge">
                <i class="fa-solid fa-circle-check"></i>
                Verified Artist
            </div>
            <h1 class="artist-name"><?= htmlspecialchars($artist['username']) ?></h1>
            <div class="listener-count">
                <?= number_format(rand(600000, 700000)) ?> monthly listeners
            </div>
        </div>
    </div>

    <!-- Action Bar -->
    <div class="action-bar">
        <button class="btn-play-big" onclick="playFirstSong()">
            <i class="fa-solid fa-play"></i>
        </button>
        
        <button class="icon-btn heart-btn" onclick="toggleArtistFavorite(this)">
            <i class="fa-regular fa-heart"></i>
        </button>

        <button class="icon-btn">
            <i class="fa-solid fa-shuffle"></i>
        </button>
        
        <button class="btn-follow">Follow</button>
        
        <button class="icon-btn">
            <i class="fa-solid fa-ellipsis"></i>
        </button>
    </div>

    <!-- Popular Section -->
    <div class="popular-section">
        <h2 class="section-title">Popular</h2>
        
        <?php if (empty($songs)): ?>
            <p style="color: var(--text-sub);">Chưa có bài hát nào được đăng tải.</p>
        <?php else: ?>
            <table class="song-table">
                <tbody>
                    <?php foreach ($songs as $index => $song): ?>
                        <tr class="song-row" onclick="window.location.href='../user/home.php?song_id=<?= $song['song_id'] ?>'">
                            <td class="rank-col"><?= $index + 1 ?></td>
                            <td>
                                <div class="title-col">
                                    <img src="<?= htmlspecialchars($song['cover_image'] ?: $defaultCover) ?>" class="song-thumb">
                                    <span class="song-name"><?= htmlspecialchars($song['title']) ?></span>
                                </div>
                            </td>
                            <td class="play-count-col"><?= number_format(rand(1000000, 2000000)) ?></td>
                            <td class="duration-col"><?= rand(3, 4) ?>:<?= str_pad(rand(0, 59), 2, '0', STR_PAD_LEFT) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<script>
    function playFirstSong() {
        <?php if (!empty($songs)): ?>
            window.location.href = '../user/home.php?song_id=<?= $songs[0]['song_id'] ?>';
        <?php endif; ?>
    }

    function toggleArtistFavorite(btn) {
        const icon = btn.querySelector('i');
        if (icon.classList.contains('fa-regular')) {
            icon.classList.remove('fa-regular');
            icon.classList.add('fa-solid');
            icon.classList.add('fa-heart');
        } else {
            icon.classList.remove('fa-solid');
            icon.classList.remove('fa-heart');
            icon.classList.add('fa-regular');
            icon.classList.add('fa-heart');
        }
    }
</script>

<?php require_once '../partials/player.php'; ?>

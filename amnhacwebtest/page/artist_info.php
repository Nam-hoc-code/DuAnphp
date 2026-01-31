<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    require_once '../auth/check_login.php';
    require_once '../config/database.php';
    require_once '../partials/header.php';
    require_once '../partials/sidebar.php';
} catch (Exception $e) {
    die('Lỗi load file: ' . $e->getMessage());
}

if (!isset($_GET['id'])) {
    die('Thiếu artist id');
}

$artistId = (int)$_GET['id'];

try {
    $conn = (new Database())->connect();
} catch (Exception $e) {
    die('Lỗi kết nối database: ' . $e->getMessage());
}

/* ===== LẤY ARTIST ===== */
$sql = "SELECT user_id, username, avatar FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die('Lỗi prepare: ' . $conn->error);
}
$stmt->bind_param("i", $artistId);
$stmt->execute();
$artist = $stmt->get_result()->fetch_assoc();

if (!$artist) {
    die('Artist không tồn tại');
}

/* ===== LẤY BÀI HÁT ===== */
$sql = "
    SELECT song_id, title, cover_image
    FROM songs
    WHERE artist_id = ?
      AND status = 'APPROVED'
      AND is_deleted = 0
    ORDER BY created_at DESC
";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die('Lỗi prepare songs: ' . $conn->error);
}
$stmt->bind_param("i", $artistId);
$stmt->execute();
$songs_result = $stmt->get_result();

// Lấy toàn bộ favorite IDs trong 1 query
$userId = $_SESSION['user']['user_id'] ?? 0;
$favoriteIds = [];

if ($userId) {
    $favSql = "
        SELECT song_id FROM favorites 
        WHERE user_id = ? 
        AND song_id IN (
            SELECT song_id FROM songs WHERE artist_id = ?
        )
    ";
    $favStmt = $conn->prepare($favSql);
    $favStmt->bind_param("ii", $userId, $artistId);
    $favStmt->execute();
    $favResult = $favStmt->get_result();
    
    while ($fav = $favResult->fetch_assoc()) {
        $favoriteIds[$fav['song_id']] = true;
    }
}

// Build songs array
$songs = [];
while ($row = $songs_result->fetch_assoc()) {
    $row['is_favorite'] = isset($favoriteIds[$row['song_id']]);
    $songs[] = $row;
}

/* ===== LẤY RELATED ARTISTS ===== */
$relatedArtists = [];
$relatedSql = "
    SELECT DISTINCT u.user_id, u.username, u.avatar, COUNT(s.song_id) as song_count
    FROM users u
    JOIN songs s ON u.user_id = s.artist_id
    WHERE u.user_id != ?
      AND s.status = 'APPROVED'
      AND s.is_deleted = 0
    GROUP BY u.user_id
    ORDER BY song_count DESC
    LIMIT 4
";
$relatedStmt = $conn->prepare($relatedSql);
$relatedStmt->bind_param("i", $artistId);
$relatedStmt->execute();
$relatedResult = $relatedStmt->get_result();
while ($row = $relatedResult->fetch_assoc()) {
    $relatedArtists[] = $row;
}

$defaultCover = '../assets/images/default-cover.png';
$artistAvatar = !empty($artist['avatar']) ? '../' . $artist['avatar'] : $defaultCover;
?>

<style>
    .artist-page {
        margin-left: 260px;
        min-height: 100vh;
        background: #121212;
        padding-bottom: 100px;
        padding-right: 440px;
    }

    .artist-hero {
        height: 50vh;
        min-height: 400px;
        max-height: 600px;
        position: relative;
        display: flex;
        align-items: flex-end;
        padding: 0 32px 40px 32px;
        background-size: cover;
        background-position: center 20%;
        color: #fff;
        width: 625px;
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
        margin-top: 16px;
        font-size: 16px;
        font-weight: 500;
        text-shadow: 0 2px 4px rgba(0,0,0,0.5);
    }

    .action-bar {
        padding: 32px 32px;
        display: flex;
        align-items: center;
        gap: 24px;
        position: relative;
        border-bottom: 1px solid rgba(255,255,255,0.1);
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
        padding: 8px 20px;
        border-radius: 20px;
        font-weight: 700;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 1px;
        cursor: pointer;
        margin-left: 8px;
        transition: all 0.2s;
    }

    .btn-follow:hover {
        border-color: #fff;
        background: rgba(255,255,255,0.1);
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
        transform: scale(1.1);
    }

    .icon-btn i.fa-solid.fa-heart {
        color: var(--spotify-green);
    }

    .popular-section {
        padding: 40px 32px;
    }

    .section-title {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 24px;
        color: #fff;
    }

    .song-table {
        width: 100%;
        border-collapse: collapse;
    }

    .song-row {
        height: 60px;
        transition: background 0.2s;
        border-radius: 4px;
        cursor: pointer;
        margin-bottom: 4px;
    }

    .song-row:hover {
        background: rgba(255,255,255,0.1);
    }

    .song-row td {
        padding: 0 16px;
        vertical-align: middle;
    }

    .rank-col {
        width: 50px;
        color: var(--text-sub);
        text-align: right;
        font-size: 16px;
        font-weight: 400;
    }

    .title-col {
        display: flex;
        align-items: center;
        gap: 16px;
        height: 60px;
        flex: 1;
    }

    .song-thumb {
        width: 45px;
        height: 45px;
        border-radius: 2px;
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
        width: 180px;
    }

    .duration-col {
        color: var(--text-sub);
        font-size: 14px;
        width: 70px;
        text-align: right;
    }

    .divider {
        height: 1px;
        background: rgba(255,255,255,0.1);
        margin: 40px 32px;
    }

    .related-artists {
        padding: 0 32px 40px 32px;
    }

    .artists-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 24px;
        margin-top: 20px;
    }

    .artist-card {
        background: rgba(255,255,255,0.1);
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        transition: all 0.2s;
        cursor: pointer;
    }

    .artist-card:hover {
        background: rgba(255,255,255,0.15);
        transform: scale(1.02);
    }

    .artist-card a {
        text-decoration: none;
        color: inherit;
    }

    .artist-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        margin: 0 auto 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    }

    .artist-card-name {
        color: #fff;
        font-weight: 600;
        font-size: 14px;
        margin-bottom: 4px;
    }

    .artist-card-count {
        color: var(--text-sub);
        font-size: 12px;
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
            <!-- <div class="listener-count">
                <?= number_format(rand(600000, 700000)) ?> monthly listeners
            </div> -->
        </div>
    </div>

    <!-- Action Bar -->
    <div class="action-bar">
        <button class="btn-play-big" onclick="playFirstSong()">
            <i class="fa-solid fa-play"></i>
        </button>
        <button class="icon-btn heart-btn" onclick="toggleArtistFavorite(this)" data-artist-id="<?= $artistId ?>">
            <!-- <i class="fa-regular fa-heart"></i> -->
        </button>
        <button class="icon-btn">
            <!-- <i class="fa-solid fa-shuffle"></i> -->
        </button>
        <!-- <button class="btn-follow">Follow</button> -->
        <button class="icon-btn">
            <!-- <i class="fa-solid fa-ellipsis"></i> -->
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
                                    <img src="<?= htmlspecialchars($song['cover_image'] ?: $defaultCover) ?>" class="song-thumb" alt="Cover">
                                    <span class="song-name"><?= htmlspecialchars($song['title']) ?></span>
                                </div>
                            </td>
                            <td class="play-count-col"><?= number_format(rand(1000000, 2000000)) ?> plays</td>
                            <td class="duration-col">
                                <?= rand(3, 4) ?>:<?= str_pad(rand(0, 59), 2, '0', STR_PAD_LEFT) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Divider -->
    <div class="divider"></div>

    <!-- Related Artists Section -->
    <div class="related-artists">
        <h2 class="section-title">Fans Also Like</h2>
        <?php if (empty($relatedArtists)): ?>
            <p style="color: var(--text-sub);">Chưa có nghệ sĩ liên quan.</p>
        <?php else: ?>
            <div class="artists-grid">
                <?php foreach ($relatedArtists as $relArtist): ?>
                    <a href="artist_info.php?id=<?= $relArtist['user_id'] ?>" class="artist-card">
                        <?php 
                            $relatedAvatar = !empty($relArtist['avatar']) ? '../' . $relArtist['avatar'] : $defaultCover;
                        ?>
                        <img src="<?= htmlspecialchars($relatedAvatar) ?>" class="artist-avatar" alt="<?= htmlspecialchars($relArtist['username']) ?>">
                        <div class="artist-card-name"><?= htmlspecialchars($relArtist['username']) ?></div>
                        <div class="artist-card-count"><?= $relArtist['song_count'] ?> songs</div>
                    </a>
                <?php endforeach; ?>
            </div>
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
        } else {
            icon.classList.remove('fa-solid');
            icon.classList.add('fa-regular');
        }
    }
</script>
<?php
require_once '../config/database.php';
require_once '../auth/check_login.php';
if (!isset($_GET['ajax'])) {
    require_once '../partials/header.php';
    require_once '../partials/sidebar.php';
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user_id = $_SESSION['user']['user_id'];

$db = new Database();
$conn = $db->connect();

$sql = "
    SELECT 
        f.fav_id,
        s.song_id,
        s.title,
        s.cloud_url,
        s.cover_image,
        u.username AS artist_name,
        f.created_at
    FROM favorites f
    JOIN songs s ON f.song_id = s.song_id
    JOIN users u ON s.artist_id = u.user_id
    WHERE f.user_id = ?
    ORDER BY f.created_at DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$favorites = $result->fetch_all(MYSQLI_ASSOC);
?>

<style>
    .favorite-content {
        margin-left: 260px;
        padding: 80px 32px 120px 32px;
        width: calc(100% - 260px);
        min-height: 100vh;
    }

    .fav-header {
        display: flex;
        align-items: flex-end;
        gap: 24px;
        margin-bottom: 32px;
        background: linear-gradient(transparent, rgba(0,0,0,0.5));
        padding: 24px;
        border-radius: 8px;
    }

    .fav-icon-box {
        width: 190px;
        height: 190px;
        background: linear-gradient(135deg, #450af5, #c4efd9);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 80px;
        color: #fff;
        box-shadow: 0 8px 24px rgba(0,0,0,0.5);
    }

    .fav-info h1 { font-size: 3rem; margin: 8px 0; font-weight: 800; }
    .fav-info p { color: var(--text-sub); font-size: 14px; }

    .fav-table {
        width: 100%;
        border-collapse: collapse;
    }

    .fav-table th {
        text-align: left;
        color: var(--text-sub);
        font-size: 12px;
        text-transform: uppercase;
        padding: 12px 16px;
        border-bottom: 1px solid #282828;
    }

    .fav-table td {
        padding: 12px 16px;
        vertical-align: middle;
    }

    .fav-row:hover { background: rgba(255,255,255,0.1); border-radius: 4px; }

    .fav-song-title { font-weight: 600; color: #fff; }
    .fav-artist-name { color: var(--text-sub); font-size: 13px; }

    .fav-song-img {
        width: 40px;
        height: 40px;
        border-radius: 4px;
        margin-right: 16px;
        object-fit: cover;
    }

    .play-trigger { color: inherit; text-decoration: none; display: flex; align-items: center; }
    .play-trigger:hover .fav-song-title { color: var(--spotify-green); }

    .btn-remove {
        background: none;
        border: none;
        color: var(--text-sub);
        cursor: pointer;
        transition: color 0.2s;
    }
    .btn-remove:hover { color: var(--logout-red); }
</style>

<main class="favorite-content">
    <div class="fav-header">
        <div class="fav-icon-box">
            <i class="fa-solid fa-heart"></i>
        </div>
        <div class="fav-info">
            <p style="text-transform: uppercase; font-weight: 700; color: #fff;">Playlist</p>
            <h1>Bài hát đã thích</h1>
            <p><b><?= count($favorites) ?> bài hát</b></p>
        </div>
    </div>

    <?php if (empty($favorites)): ?>
        <div style="text-align:center; padding: 60px; color: var(--text-sub);">
            <i class="fa-solid fa-heart-crack" style="font-size: 48px; margin-bottom: 20px;"></i>
            <p>Bạn chưa có bài hát yêu thích nào. Hãy khám phá và thêm bài hát!</p>
        </div>
    <?php else: ?>
        <table class="fav-table">
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th>Tiêu đề</th>
                    <th>Nghệ sĩ</th>
                    <th>Thời gian thêm</th>
                    <th style="text-align: right;"><i class="fa-regular fa-clock"></i></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($favorites as $index => $fav): ?>
                <tr class="fav-row">
                    <td style="color: var(--text-sub);"><?= $index + 1 ?></td>
                    <td>
                        <a href="../user/home.php?song_id=<?= $fav['song_id'] ?>" class="play-trigger">
                            <img src="<?= htmlspecialchars($fav['cover_image'] ?? '../assets/images/default-cover.png') ?>" class="fav-song-img">
                            <span class="fav-song-title"><?= htmlspecialchars($fav['title']) ?></span>
                        </a>
                    </td>
                    <td class="fav-artist-name"><?= htmlspecialchars($fav['artist_name']) ?></td>
                    <td style="color: var(--text-sub); font-size: 13px;"><?= date('d/m/Y', strtotime($fav['created_at'])) ?></td>
                    <td style="text-align: right;">
                        <form action="remove_favorite.php" method="POST" style="display:inline;">
                            <input type="hidden" name="fav_id" value="<?= $fav['fav_id'] ?>">
                            <button type="submit" class="btn-remove" title="Xóa khỏi yêu thích">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</main>

<?php 
if (!isset($_GET['ajax'])) {
    require_once '../partials/player.php'; 
}
?>

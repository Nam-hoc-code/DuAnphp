<?php
require_once '../config/database.php';
require_once '../auth/check_login.php';
require_once '../partials/header.php';
require_once '../partials/sidebar.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra disc_id
if (!isset($_GET['disc_id']) || empty($_GET['disc_id'])) {
    die("❌ Đĩa không tồn tại");
}

$disc_id = (int)$_GET['disc_id'];

$db = new Database();
$conn = $db->connect();

// ✅ Lấy thông tin đĩa
$sql_disc = "
    SELECT 
        d.disc_id,
        d.disc_title,
        d.disc_image,
        d.price,
        d.description,
        d.created_at,
        u.user_id,
        u.username AS artist_name
    FROM discs d
    JOIN users u ON d.artist_id = u.user_id
    WHERE d.disc_id = ? AND d.is_deleted = 0
";

$stmt_disc = $conn->prepare($sql_disc);
$stmt_disc->bind_param("i", $disc_id);
$stmt_disc->execute();
$result_disc = $stmt_disc->get_result();

if ($result_disc->num_rows === 0) {
    die("❌ Đĩa không tồn tại");
}

$disc = $result_disc->fetch_assoc();

// ✅ Lấy danh sách bài hát trong đĩa (BỎ s.duration)
$sql_songs = "
    SELECT 
        s.song_id,
        s.title,
        dd.track_number
    FROM disc_details dd
    JOIN songs s ON dd.song_id = s.song_id
    WHERE dd.disc_id = ?
    ORDER BY dd.track_number ASC
";

$stmt_songs = $conn->prepare($sql_songs);
$stmt_songs->bind_param("i", $disc_id);
$stmt_songs->execute();
$result_songs = $stmt_songs->get_result();
$songs = $result_songs->fetch_all(MYSQLI_ASSOC);

// ✅ Đếm số lượng bài hát
$song_count = count($songs);
?>

<style>
    :root {
        --bg-black: #000000;
        --card-bg: #181818;
        --text-main: #ffffff;
        --text-sub: #b3b3b3;
        --spotify-green: #1DB954;
        --table-border: #282828;
    }

    .disc-info-content {
        margin-left: 260px;
        padding: 80px 40px 120px 40px;
        width: calc(100% - 260px);
        min-height: 100vh;
        background-color: var(--bg-black);
        color: var(--text-main);
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--spotify-green);
        text-decoration: none;
        font-weight: 600;
        margin-bottom: 32px;
        transition: gap 0.2s;
    }
    .back-link:hover { gap: 12px; }

    .disc-header {
        display: grid;
        grid-template-columns: 300px 1fr;
        gap: 40px;
        margin-bottom: 60px;
    }

    .disc-cover {
        position: relative;
        padding-bottom: 100%;
        box-shadow: 0 8px 32px rgba(0,0,0,0.8);
        border-radius: 8px;
        overflow: hidden;
    }

    .disc-cover img {
        position: absolute;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .disc-details h1 {
        font-size: 2.5rem;
        font-weight: 900;
        margin: 0 0 12px 0;
        line-height: 1.2;
    }

    .disc-meta {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-bottom: 24px;
        padding-bottom: 24px;
        border-bottom: 1px solid var(--table-border);
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 12px;
        color: var(--text-sub);
        font-size: 15px;
    }

    .meta-item i {
        color: var(--spotify-green);
        font-size: 18px;
        width: 24px;
        text-align: center;
    }

    .artist-link {
        color: var(--spotify-green);
        text-decoration: none;
        font-weight: 600;
        transition: color 0.2s;
    }
    .artist-link:hover { color: #1ed760; }

    .disc-price {
        font-size: 2rem;
        font-weight: 900;
        color: var(--spotify-green);
        margin-bottom: 24px;
    }

    .disc-description {
        color: var(--text-sub);
        line-height: 1.6;
        margin-bottom: 32px;
    }

    .action-buttons {
        display: flex;
        gap: 12px;
    }

    .btn {
        padding: 12px 32px;
        border: none;
        border-radius: 50px;
        font-weight: 700;
        font-size: 15px;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-primary {
        background: var(--spotify-green);
        color: #000;
    }
    .btn-primary:hover { background: #1ed760; transform: scale(1.05); }

    .btn-secondary {
        background: transparent;
        color: var(--text-main);
        border: 1px solid var(--table-border);
    }
    .btn-secondary:hover { border-color: var(--spotify-green); color: var(--spotify-green); }

    /* Danh sách bài hát */
    .songs-section {
        margin-top: 60px;
    }

    .songs-section h2 {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .songs-table {
        background: var(--card-bg);
        border-radius: 8px;
        border: 1px solid var(--table-border);
        overflow: hidden;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th {
        background: rgba(255, 255, 255, 0.05);
        padding: 16px;
        text-align: left;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--text-sub);
        border-bottom: 1px solid var(--table-border);
        letter-spacing: 1px;
    }

    td {
        padding: 16px;
        border-bottom: 1px solid var(--table-border);
        font-size: 14px;
    }

    tr:hover td {
        background-color: rgba(255, 255, 255, 0.03);
    }

    .track-number {
        color: var(--spotify-green);
        font-weight: 700;
        width: 40px;
    }

    .song-title {
        font-weight: 600;
    }

    .song-duration {
        color: var(--text-sub);
        text-align: right;
    }

    .empty-state {
        text-align: center;
        padding: 40px;
        color: var(--text-sub);
        background: var(--card-bg);
        border-radius: 8px;
        border: 1px solid var(--table-border);
    }

    .empty-state i {
        font-size: 48px;
        margin-bottom: 16px;
        display: block;
        opacity: 0.5;
    }

    @media (max-width: 768px) {
        .disc-header {
            grid-template-columns: 1fr;
        }

        .disc-cover {
            max-width: 300px;
            margin: 0 auto;
        }

        .disc-details h1 {
            font-size: 1.8rem;
        }
    }
</style>

<main class="disc-info-content">
    <a href="../disc/disclist.php" class="back-link">
        <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách đĩa
    </a>

    <div class="disc-header">
        <!-- Hình ảnh đĩa -->
        <div class="disc-cover">
            <img src="<?= htmlspecialchars($disc['disc_image'] ? '../uploads/disc_images/' . $disc['disc_image'] : '../assets/images/default-cover.png') ?>" 
                 alt="<?= htmlspecialchars($disc['disc_title']) ?>"
                 onerror="this.src='../assets/images/default-cover.png'">
        </div>

        <!-- Thông tin đĩa -->
        <div class="disc-details">
            <h1><?= htmlspecialchars($disc['disc_title']) ?></h1>

            <div class="disc-meta">
                <div class="meta-item">
                    <i class="fa-solid fa-user"></i>
                    <span>
                        Nghệ sĩ: 
                        <a href="../page/artist_profile.php?artist_id=<?= $disc['user_id'] ?>" class="artist-link">
                            <?= htmlspecialchars($disc['artist_name']) ?>
                        </a>
                    </span>
                </div>
                <div class="meta-item">
                    <i class="fa-solid fa-music"></i>
                    <span><?= $song_count ?> bài hát</span>
                </div>
                <div class="meta-item">
                    <i class="fa-solid fa-calendar"></i>
                    <span>Ngày phát hành: <?= date('d/m/Y', strtotime($disc['created_at'])) ?></span>
                </div>
            </div>

            <div class="disc-price"><?= number_format($disc['price']) ?> VNĐ</div>

            <?php if (!empty($disc['description'])): ?>
                <div class="disc-description">
                    <?= htmlspecialchars($disc['description']) ?>
                </div>
            <?php endif; ?>

            <div class="action-buttons">
                <form action="../disc/add_to_cart.php" method="POST">
                    <input type="hidden" name="disc_id" value="<?= $disc['disc_id'] ?>">
                    <input type="hidden" name="title" value="<?= htmlspecialchars($disc['disc_title']) ?>">
                    <input type="hidden" name="price" value="<?= $disc['price'] ?>">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-cart-plus"></i> Thêm vào giỏ hàng
                    </button>
                </form>
                <a href="../disc/cart.php" class="btn btn-secondary">
                    <i class="fa-solid fa-shopping-cart"></i> Xem giỏ hàng
                </a>
            </div>
        </div>
    </div>

    <!-- Danh sách bài hát -->
    <div class="songs-section">
        <h2>
            <i class="fa-solid fa-list-ol"></i>
            Danh sách bài hát (<?= $song_count ?> bài)
        </h2>

        <?php if ($song_count > 0): ?>
            <div class="songs-table">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 50px;">STT</th>
                            <th>Tên bài hát</th>
                            <th style="width: 100px; text-align: right;">Thời lượng</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($songs as $song): ?>
                        <tr>
                            <td class="track-number"><?= $song['track_number'] ?></td>
                            <td class="song-title"><?= htmlspecialchars($song['title']) ?></td>
                            <td class="song-duration">
                                <i class="fa-solid fa-music"></i>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fa-solid fa-circle-info"></i>
                <p>Đĩa này chưa có bài hát nào.</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php require_once '../partials/player.php'; ?>
<?php
require_once '../auth/check_login.php';
require_once '../config/database.php';
require_once '../partials/header.php';
require_once '../partials/sidebar.php';

$userId = $_SESSION['user']['user_id'] ?? null;
if (!$userId) {
    die('Chưa đăng nhập');
}

$conn = (new Database())->connect();

$sql = "SELECT user_id, username FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    die('User không tồn tại');
}

$sql = "
    SELECT 
        s.song_id,
        s.title,
        s.cover_image
    FROM favorites f
    JOIN songs s ON f.song_id = s.song_id
    WHERE f.user_id = ?
      AND s.status = 'APPROVED'
      AND s.is_deleted = 0
    ORDER BY s.created_at DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$songs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tài khoản</title>
    <!-- Import Outfit Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #000;
            color: #fff;
            font-family: 'Outfit', sans-serif;
            overflow-x: hidden;
        }

        .main-content {
            margin-left: 260px;
            padding: 0;
            min-height: 100vh;
            background: #121212;
            position: relative;
        }

        /* Profile Header Section mimicking Image 2 */
        .profile-header {
            display: flex;
            align-items: flex-end;
            gap: 24px;
            padding: 80px 700px 24px 120px; /* Reduced padding from 450px to 32px to fix 'narrow frame' */
            background: linear-gradient(to bottom, #535353, #2b2b2b);
            height: 340px;
            box-shadow: 0 4px 60px rgba(0, 0, 0, 0.3);
        }

        .avatar {
            width: 232px;
            height: 232px;
            border-radius: 50%;
            background: #8e9e91ff; /* Grey background like Image 2 */
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 80px;
            color: #f0f7f2ff;
            box-shadow: 0 4px 60px rgba(0, 0, 0, 0.5);
            flex-shrink: 0;
        }

        .profile-info {
            display: flex;
            flex-direction: column;
            width: 100%;
        }

        .profile-label {
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 8px;
            color: #fff;
        }

        .profile-info h1 {
            font-size: 96px; /* Huge, bold typography */
            font-weight: 900;
            margin: 0 0 10px 0;
            color: #fff;
            line-height: 1;
            letter-spacing: -2px;
        }

        .profile-stats {
            font-size: 16px;
            font-weight: 500;
            color: #fff;
        }

        /* Content Body */
        .content-body {
            padding: 24px 32px;
            background: linear-gradient(to bottom, rgba(18,18,18,1) 0%, #121212 100%);
        }

        .section-title {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 20px;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .section-title i {
            color: #a020f0; /* Purple color for the music icon */
        }

        .empty-message {
            color: #b3b3b3;
            font-size: 16px;
            padding: 40px 0;
            text-align: center; /* Centered like typical modern empty states (and likely Image 2) */
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .song-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 24px;
        }

        .song-card {
            background: #181818;
            border-radius: 6px;
            padding: 16px;
            transition: background-color 0.3s ease;
            cursor: pointer;
        }

        .song-card:hover {
            background: #282828;
        }

        .song-card-image {
            width: 100%;
            aspect-ratio: 1 / 1;
            border-radius: 4px;
            margin-bottom: 16px;
            object-fit: cover;
            box-shadow: 0 8px 24px rgba(0,0,0,0.5);
        }

        .song-title {
            font-weight: 700;
            font-size: 16px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            color: #fff;
            margin-bottom: 4px;
        }

        @media (max-width: 1024px) {
            .main-content {
                margin-left: 0;
            }
             .profile-header {
                height: auto;
                /* Removed flex-direction: column and center alignment to keep it wide & left-aligned like desktop */
                flex-wrap: wrap;
                padding-left: 32px;
                padding-right: 32px;
                padding-bottom: 32px;
                
            }
             .profile-info h1 {
                font-size: 64px;
            }
            .profile-info {
                align-items: flex-start; /* Keep text left-aligned */
            }
        }

        @media (max-width: 768px) {
            .profile-header {
                flex-direction: column;
                align-items: flex-start; /* Ensure left align even when stacked */
                padding: 40px 24px;
            }
            
            .avatar {
                width: 160px;
                height: 160px;
                font-size: 60px;
            }

            .profile-info h1 {
                font-size: 48px;
            }

          


        }
    </style>
</head>

<body>

<main class="main-content">
    <div class="profile-header">
        <div class="avatar">
            <i class="fa-solid fa-user"></i>
        </div>
        <div class="profile-info">
            <span class="profile-label">Hồ sơ</span>
            <h1><?= htmlspecialchars($user['username']) ?></h1>
            <p class="profile-stats"><?= count($songs) ?> bài hát yêu thích</p>
        </div>
    </div>

    <div class="content-body">
        <h2 class="section-title"><i class="fa-solid fa-music"></i> Bài hát yêu thích</h2>

        <?php if (empty($songs)): ?>
            <div class="empty-message">
                <i class="fa-regular fa-heart" style="font-size: 64px; margin-bottom: 24px; opacity: 0.3;"></i>
                <p>Chưa có bài hát yêu thích. Hãy thêm những bài hát bạn thích!</p>
            </div>
        <?php else: ?>
            <div class="song-grid">
                <?php foreach ($songs as $song): ?>
                    <div class="song-card">
                        <img 
                            class="song-card-image"
                            src="<?= !empty($song['cover_image']) ? htmlspecialchars($song['cover_image']) : 'https://via.placeholder.com/180?text=No+Image' ?>" 
                            alt="<?= htmlspecialchars($song['title']) ?>"
                            data-fallback="https://via.placeholder.com/180?text=No+Image">
                        <div class="song-title" title="<?= htmlspecialchars($song['title']) ?>">
                            <?= htmlspecialchars($song['title']) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php require_once '../partials/footer.php'; ?>

<script>
document.querySelectorAll('.song-card-image').forEach(img => {
    img.addEventListener('error', function() {
        this.src = this.dataset.fallback;
    });
});
</script>

</body>
</html>
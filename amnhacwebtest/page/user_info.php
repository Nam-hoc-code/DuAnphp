<?php
require_once '../auth/check_login.php';
require_once '../config/database.php';
require_once '../partials/header.php';
require_once '../partials/sidebar.php';

$userId = $_SESSION['user_id'] ?? null;
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #121212;
            color: #fff;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        .main-content {
            margin-left: 260px;
            padding: 100px 40px 40px 40px;
            min-height: 100vh;
            background: linear-gradient(to bottom, #1e1e1e, #121212);
            position: relative;
            z-index: 1;
        }

        .profile-header {
            display: flex;
            align-items: center;
            gap: 30px;
            margin-bottom: 50px;
            padding-bottom: 30px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: linear-gradient(135deg, #1db954 0%, #191414 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 70px;
            color: #fff;
            flex-shrink: 0;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.5);
        }

        .profile-info h1 {
            font-size: 36px;
            font-weight: 800;
            margin-bottom: 8px;
            color: #fff;
        }

        .profile-info p {
            font-size: 16px;
            color: #b3b3b3;
        }

        .section-title {
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 24px;
            margin-top: 20px;
            color: #fff;
        }

        .empty-message {
            color: #b3b3b3;
            font-size: 16px;
            padding: 40px 0;
            text-align: center;
        }

        .song-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 24px;
            margin-bottom: 40px;
        }

        .song-card {
            background: #282828;
            border-radius: 8px;
            padding: 16px;
            transition: all 0.3s ease;
            cursor: pointer;
            overflow: hidden;
        }

        .song-card:hover {
            background: #333333;
            transform: translateY(-4px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.5);
        }

        .song-card-image {
            width: 100%;
            aspect-ratio: 1 / 1;
            border-radius: 4px;
            margin-bottom: 12px;
            object-fit: cover;
            background: #404040;
        }

        .song-title {
            font-weight: 600;
            font-size: 15px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            color: #fff;
            line-height: 1.4;
        }

        @media (max-width: 1024px) {
            .main-content {
                margin-left: 0;
                padding: 80px 24px;
            }

            .profile-header {
                flex-direction: column;
                text-align: center;
            }

            .song-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .song-grid {
                grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
                gap: 16px;
            }

            .section-title {
                font-size: 24px;
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
            <h1><?= htmlspecialchars($user['username']) ?></h1>
            <p><?= count($songs) ?> bài hát yêu thích</p>
        </div>
    </div>

    <h2 class="section-title">🎵 Bài hát yêu thích</h2>

    <?php if (empty($songs)): ?>
        <div class="empty-message">
            <i class="fa-regular fa-heart" style="font-size: 48px; margin-bottom: 16px; display: block; opacity: 0.5;"></i>
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
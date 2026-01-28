<?php
require_once '../auth/check_login.php';
require_once '../config/database.php';
require_once '../partials/header.php';
require_once '../partials/sidebar.php';

/* ========= LẤY USER ID TỪ SESSION ========= */
$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    die('Chưa đăng nhập');
}

$conn = (new Database())->connect();

/* ========= USER INFO ========= */
$sql = "SELECT user_id, username FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    die('User không tồn tại');
}

/* ========= FAVORITE SONGS ========= */
$sql = "
    SELECT 
        s.song_id,
        s.title,
        s.cover_image
    FROM favorite f
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

$defaultCover = '../assets/images/default-cover.png';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tài khoản</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        .main-content {
            margin-left: 260px;
            padding: 80px 32px;
            min-height: 100vh;
            background: #121212;
            color: #fff;
        }

        .profile-header {
            display: flex;
            align-items: center;
            gap: 24px;
            margin-bottom: 40px;
        }

        .avatar {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            background: #282828;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 60px;
            color: #555;
        }

        .song-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px,1fr));
            gap: 24px;
            margin-top: 24px;
        }

        .song-card {
            background: #181818;
            padding: 16px;
            border-radius: 8px;
            transition: background 0.2s;
        }

        .song-card:hover {
            background: #282828;
        }

        .song-card img {
            width: 100%;
            border-radius: 4px;
            margin-bottom: 12px;
            object-fit: cover;
        }

        .song-title {
            font-weight: 600;
            font-size: 15px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
</head>

<body>

<main class="main-content">

    <!-- USER INFO -->
    <div class="profile-header">
        <div class="avatar">
            <i class="fa-solid fa-user"></i>
        </div>
        <div>
            <h1><?= htmlspecialchars($user['username']) ?></h1>
            <p><?= count($songs) ?> bài hát yêu thích</p>
        </div>
    </div>

    <!-- FAVORITE SONGS -->
    <h2>Bài hát yêu thích</h2>

    <?php if (empty($songs)): ?>
        <p>Chưa có bài hát yêu thích.</p>
    <?php else: ?>
        <div class="song-grid">
            <?php foreach ($songs as $song): ?>
                <div class="song-card">
                    <img src="<?= htmlspecialchars($song['cover_image'] ?: $defaultCover) ?>" alt="cover">
                    <div class="song-title"><?= htmlspecialchars($song['title']) ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</main>

<?php require_once '../partials/footer.php'; ?>
</body>
</html>

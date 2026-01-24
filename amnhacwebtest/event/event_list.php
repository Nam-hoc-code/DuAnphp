<?php
require_once '../auth/check_login.php';
require_once 'event_process.php';

$role = strtoupper($_SESSION['user']['role'] ?? 'USER');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh Sách Sự Kiện - Spotify</title>
    <!-- Fonts & Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --spotify-black: #000000;
            --spotify-dark: #121212;
            --spotify-card: #181818;
            --spotify-grey: #282828;
            --spotify-green: #1DB954;
            --spotify-soft-green: #1ed760;
            --text-main: #ffffff;
            --text-sub: #b3b3b3;
            --danger-red: #f15555;
            --accent-cyan: #00DBFF;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Outfit', sans-serif;
        }

        body {
            background-color: var(--spotify-black);
            color: var(--text-main);
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Navigation */
        .sidebar {
            width: 260px;
            background-color: #000000;
            padding: 24px 12px;
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            border-right: 1px solid #1f1f1f;
            z-index: 1000;
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 12px 32px 12px;
            font-size: 24px;
            font-weight: 700;
            color: var(--text-main);
            text-decoration: none;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 12px 16px;
            color: var(--text-sub);
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            border-radius: 6px;
            transition: all 0.3s ease;
            margin-bottom: 4px;
        }

        .nav-link:hover, .nav-link.active {
            color: #fff;
            background-color: var(--spotify-grey);
        }

        .nav-link i { font-size: 20px; width: 24px; text-align: center; }

        .nav-link.logout { color: var(--danger-red); margin-top: auto; }

        /* Main Content */
        .main-wrapper {
            margin-left: 260px;
            flex-grow: 1;
            padding: 40px;
            background: linear-gradient(to bottom, #1a1a1a 0%, var(--spotify-black) 300px);
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }

        header h1 {
            font-size: 32px;
            font-weight: 700;
        }

        .btn-add-header {
            background-color: var(--spotify-green);
            color: #000;
            padding: 12px 24px;
            border-radius: 500px;
            text-decoration: none;
            font-weight: 700;
            font-size: 14px;
            transition: 0.2s;
        }

        .btn-add-header:hover { transform: scale(1.05); }

        /* Event Grid */
        .event-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 24px;
        }

        .event-card {
            background-color: var(--spotify-card);
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.05);
            transition: all 0.3s ease;
            position: relative;
        }

        .event-card:hover {
            background-color: var(--spotify-grey);
            transform: translateY(-5px);
            border-color: rgba(255,255,255,0.1);
        }

        .banner-box {
            width: 100%;
            height: 180px;
            overflow: hidden;
            background-color: #222;
        }

        .banner-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s;
        }

        .event-card:hover .banner-box img { transform: scale(1.1); }

        .event-info {
            padding: 24px;
        }

        .event-info h3 {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 12px;
            line-height: 1.4;
        }

        .event-meta {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 20px;
            color: var(--text-sub);
            font-size: 14px;
        }

        .meta-item { display: flex; align-items: center; gap: 8px; }
        .meta-item i { color: var(--spotify-green); width: 16px; text-align: center; }

        .price-tag {
            background: rgba(29, 185, 84, 0.1);
            color: var(--spotify-green);
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 13px;
        }

        .btn-buy {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background-color: var(--spotify-green);
            color: #000;
            text-decoration: none;
            padding: 12px 20px;
            border-radius: 500px;
            font-weight: 700;
            font-size: 14px;
            width: 100%;
            transition: 0.2s;
        }

        .btn-buy:hover { background-color: var(--spotify-soft-green); }

        .admin-controls {
            display: flex;
            gap: 12px;
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px solid rgba(255,255,255,0.05);
        }

        .btn-edit { color: var(--accent-cyan); text-decoration: none; font-size: 13px; font-weight: 600; }
        .btn-delete { color: var(--danger-red); text-decoration: none; font-size: 13px; font-weight: 600; }
        .btn-edit:hover, .btn-delete:hover { text-decoration: underline; }

        .empty-state {
            text-align: center;
            padding: 100px 0;
            color: var(--text-sub);
            grid-column: 1 / -1;
        }

        .empty-state i { font-size: 64px; margin-bottom: 24px; opacity: 0.2; }
    </style>
</head>
<body>

    <!-- Sidebar Navigation -->
    <aside class="sidebar">
        <a href="../admin/admin_view.php" class="logo-section">
            <i class="fa-brands fa-spotify" style="color: var(--spotify-green); font-size: 36px;"></i>
            <span>Spotify Admin</span>
        </a>
        <nav>
            <a href="../admin/admin_view.php" class="nav-link"><i class="fa-solid fa-house"></i> Trang chủ</a>
            <a href="../admin/song_requests.php" class="nav-link"><i class="fa-solid fa-music"></i> Duyệt bài hát</a>
            <a href="add_event.php" class="nav-link active"><i class="fa-solid fa-calendar-alt"></i> Quản lý sự kiện</a>
        </nav>
        <a href="../auth/logout.php" class="nav-link logout"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a>
    </aside>

    <!-- Main Content -->
    <div class="main-wrapper">
        <header>
            <h1>Sự kiện Âm nhạc</h1>
            <?php if ($role === 'ADMIN'): ?>
                <a href="add_event.php" class="btn-add-header"><i class="fa-solid fa-plus"></i> Thêm sự kiện mới</a>
            <?php endif; ?>
        </header>

        <div class="event-grid">
            <?php if (empty($events)): ?>
                <div class="empty-state">
                    <i class="fa-solid fa-calendar-xmark"></i>
                    <p>Hiện chưa có sự kiện nào diễn ra.</p>
                </div>
            <?php else: ?>
                <?php foreach ($events as $event): ?>
                    <div class="event-card">
                        <div class="banner-box">
                            <img src="<?= htmlspecialchars($event['banner_image']) ?>" alt="Banner sự kiện">
                        </div>
                        <div class="event-info">
                            <h3><?= htmlspecialchars($event['name']) ?></h3>
                            <div class="event-meta">
                                <div class="meta-item">
                                    <i class="fa-solid fa-calendar-days"></i>
                                    <span><?= date('d/m/Y', strtotime($event['event_date'])) ?></span>
                                </div>
                                <div class="meta-item">
                                    <i class="fa-solid fa-ticket"></i>
                                    <span class="price-tag"><?= number_format($event['price']) ?> VNĐ</span>
                                </div>
                            </div>

                            <a href="<?= htmlspecialchars($event['buy_url']) ?>" target="_blank" class="btn-buy">
                                <i class="fa-solid fa-cart-shopping"></i> Mua vé ngay
                            </a>

                            <?php if ($role === 'ADMIN'): ?>
                                <div class="admin-controls">
                                    <a href="event_edit.php?id=<?= $event['event_id'] ?>" class="btn-edit">
                                        <i class="fa-solid fa-pen-to-square"></i> Chỉnh sửa
                                    </a>
                                    <a href="event_delete.php?id=<?= $event['event_id'] ?>" 
                                       onclick="return confirm('Bạn có chắc chắn muốn xóa sự kiện này?')" 
                                       class="btn-delete">
                                        <i class="fa-solid fa-trash-can"></i> Xóa bỏ
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>

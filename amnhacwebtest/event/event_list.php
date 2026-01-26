<?php
require_once '../auth/check_login.php';
require_once 'event_process.php';
require_once '../partials/header.php';
require_once '../partials/sidebar.php';

$role = strtoupper($_SESSION['user']['role'] ?? 'USER');
?>

<style>
    .event-content {
        margin-left: 260px;
        padding: 80px 32px 120px 32px;
        width: calc(100% - 260px);
        min-height: 100vh;
    }

    .event-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 40px;
    }

    .event-header h1 { font-size: 2.5rem; font-weight: 800; }

    .btn-add-event {
        background-color: var(--spotify-green);
        color: #000;
        padding: 12px 24px;
        border-radius: 500px;
        text-decoration: none;
        font-weight: 700;
        font-size: 14px;
        transition: transform 0.2s;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .btn-add-event:hover { transform: scale(1.05); }

    .event-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 32px;
    }

    .event-card {
        background-color: var(--card-bg);
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #282828;
        transition: all 0.3s ease;
    }

    .event-card:hover {
        background-color: #282828;
        transform: translateY(-8px);
        border-color: #333;
    }

    .banner-wrapper {
        width: 100%;
        height: 200px;
        overflow: hidden;
    }

    .banner-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .event-card:hover .banner-wrapper img { transform: scale(1.1); }

    .event-details { padding: 24px; }
    .event-details h3 { font-size: 1.4rem; font-weight: 700; margin-bottom: 12px; height: 3.4rem; overflow: hidden; }

    .event-info-row {
        display: flex;
        align-items: center;
        gap: 12px;
        color: var(--text-sub);
        font-size: 14px;
        margin-bottom: 12px;
    }
    .event-info-row i { color: var(--spotify-green); width: 20px; text-align: center; }

    .price-badge {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--spotify-green);
        margin-bottom: 24px;
        display: block;
    }

    .btn-ticket {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        background-color: var(--spotify-green);
        color: #000;
        padding: 12px;
        border-radius: 4px;
        font-weight: 700;
        width: 100%;
        transition: 0.2s;
    }
    .btn-ticket:hover { background-color: #1ed760; }

    .admin-actions {
        display: flex;
        gap: 16px;
        margin-top: 20px;
        padding-top: 16px;
        border-top: 1px solid #333;
    }
    .admin-actions a { font-size: 13px; font-weight: 600; }
    .btn-edit { color: #00dbff; }
    .btn-delete { color: var(--logout-red); }

    .empty-events {
        text-align: center;
        grid-column: 1 / -1;
        padding: 100px 0;
        color: var(--text-sub);
    }
</style>

<main class="event-content">
    <div class="event-header">
        <h1>Sự kiện Âm nhạc</h1>
        <?php if ($role === 'ADMIN'): ?>
            <a href="add_event.php" class="btn-add-event">
                <i class="fa-solid fa-plus"></i> Thêm sự kiện mới
            </a>
        <?php endif; ?>
    </div>

    <div class="event-grid">
        <?php if (empty($events)): ?>
            <div class="empty-events">
                <i class="fa-solid fa-calendar-xmark" style="font-size: 64px; margin-bottom: 24px; opacity: 0.3;"></i>
                <p>Hiện chưa có sự kiện âm nhạc nào sắp tới.</p>
            </div>
        <?php else: ?>
            <?php foreach ($events as $event): ?>
                <div class="event-card">
                    <div class="banner-wrapper">
                        <img src="<?= htmlspecialchars($event['banner_image']) ?>" alt="Banner">
                    </div>
                    <div class="event-details">
                        <h3><?= htmlspecialchars($event['name']) ?></h3>
                        
                        <div class="event-info-row">
                            <i class="fa-solid fa-calendar-days"></i>
                            <span><?= date('d/m/Y', strtotime($event['event_date'])) ?></span>
                        </div>
                        
                        <div class="event-info-row" style="margin-bottom: 20px;">
                            <i class="fa-solid fa-location-dot"></i>
                            <span>Địa điểm đang cập nhật...</span>
                        </div>

                        <span class="price-badge"><?= number_format($event['price']) ?> VNĐ</span>

                        <a href="<?= htmlspecialchars($event['buy_url']) ?>" target="_blank" class="btn-ticket">
                            <i class="fa-solid fa-ticket"></i> Đặt vé ngay
                        </a>

                        <?php if ($role === 'ADMIN'): ?>
                            <div class="admin-actions">
                                <a href="event_edit.php?id=<?= $event['event_id'] ?>" class="btn-edit">
                                    <i class="fa-solid fa-pen-to-square"></i> Chỉnh sửa
                                </a>
                                <a href="event_delete.php?id=<?= $event['event_id'] ?>" 
                                   onclick="return confirm('Xóa sự kiện này?')" class="btn-delete">
                                    <i class="fa-solid fa-trash-can"></i> Xóa
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>

<?php require_once '../partials/player.php'; ?><!-- 
<h2 class="event-title">🎵 Sự kiện âm nhạc</h2>

<?php foreach ($events as $event): ?>
    <div class="event-card">

        <?php if (!empty($event['banner_image'])): ?>
            <div class="event-banner">
                <img 
                    src="<?= htmlspecialchars($event['banner_image']) ?>" 
                    alt="Banner sự kiện"
                >
            </div>
        <?php endif; ?>

        <div class="event-content">
            <h3 class="event-name">
                <?= htmlspecialchars($event['name']) ?>
            </h3>

            <p class="event-date">
                📅 <?= date('d/m/Y', strtotime($event['event_date'])) ?>
            </p>

            <p class="event-price">
                💰 <?= number_format($event['price']) ?> VNĐ
            </p>

            <a class="event-buy"
               href="<?= htmlspecialchars($event['buy_url']) ?>"
               target="_blank">
                🎟️ Mua vé
            </a>

            <?php if ($role === 'ADMIN'): ?>
                <div class="event-admin">
                    <a href="event_edit.php?id=<?= $event['event_id'] ?>">
                        ✏️ Sửa
                    </a>
                    |
                    <a href="event_delete.php?id=<?= $event['event_id'] ?>"
                       onclick="return confirm('Xóa sự kiện này?')">
                        🗑️ Xóa
                    </a>
                </div>
            <?php endif; ?>
        </div>

    </div>
<?php endforeach; ?> -->

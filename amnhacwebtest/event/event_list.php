<?php
require_once '../auth/check_login.php';
require_once 'event_process.php';

$role = strtoupper($_SESSION['user']['role'] ?? 'USER');
?>

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
<?php endforeach; ?>

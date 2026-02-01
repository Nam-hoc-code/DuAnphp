<?php
require_once '../config/database.php';
require_once '../auth/check_login.php';
require_once '../partials/header.php';
require_once '../partials/sidebar.php';

$user_id = $_SESSION['user']['user_id'];
$db = new Database();
$conn = $db->connect();

// Fetch orders with disc details
// Note: Assuming 'is_received' column exists as requested
$sql = "
    SELECT 
        do.order_id,
        do.created_at,
        do.status,
        do.is_received,
        do.receiver_name,
        do.phone,
        do.address,
        d.disc_title,
        d.disc_image,
        d.price
    FROM disc_orders do
    JOIN discs d ON do.disc_id = d.disc_id
    WHERE do.user_id = ?
    ORDER BY do.created_at DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$orders = $result->fetch_all(MYSQLI_ASSOC);
?>

<style>
    .my-orders-container {
        margin-left: 260px;
        padding: 100px 32px;
        min-height: 100vh;
        color: #fff;
    }

    .page-title {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 32px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .orders-table-wrapper {
        background: #181818;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid #282828;
    }

    .orders-table {
        width: 100%;
        border-collapse: collapse;
        color: #ddd;
        font-size: 14px;
    }

    .orders-table th, .orders-table td {
        padding: 16px;
        text-align: left;
        border-bottom: 1px solid #282828;
    }

    .orders-table th {
        background: #202020;
        font-weight: 600;
        color: #b3b3b3;
    }

    .disc-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .disc-thumb {
        width: 48px;
        height: 48px;
        border-radius: 4px;
        object-fit: cover;
    }

    .order-status {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 500px;
        font-size: 12px;
        font-weight: 600;
        text-transform: capitalize;
    }

    .status-pending { background: rgba(255, 193, 7, 0.1); color: #ffc107; }
    .status-shipping { background: rgba(33, 150, 243, 0.1); color: #2196f3; }
    .status-completed { background: rgba(29, 185, 84, 0.1); color: #1db954; }

    .btn-confirm {
        background: transparent;
        border: 1px solid var(--spotify-green);
        color: var(--spotify-green);
        padding: 6px 16px;
        border-radius: 500px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.2s;
    }

    .btn-confirm:hover {
        background: var(--spotify-green);
        color: #000;
    }

    .received-badge {
        color: var(--spotify-green);
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    
    .empty-orders {
        text-align: center;
        padding: 60px;
        color: #b3b3b3;
    }
</style>

<div class="my-orders-container">
    <h1 class="page-title">
        <i class="fa-solid fa-box-open"></i> Đơn hàng của tôi
    </h1>

    <?php if (empty($orders)): ?>
        <div class="orders-table-wrapper empty-orders">
            <i class="fa-solid fa-clipboard-list" style="font-size: 48px; margin-bottom: 16px; opacity: 0.5;"></i>
            <p>Bạn chưa có đơn hàng nào.</p>
            <a href="../disc/disclist.php" style="color: var(--spotify-green); margin-top: 12px; display: inline-block;">Mua sắm ngay</a>
        </div>
    <?php else: ?>
        <div class="orders-table-wrapper">
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Sản phẩm</th>
                        <th>Tổng tiền</th>
                        <th>Ngày đặt</th>
                        <th>Trạng thái</th>
                        <th>Nhận hàng</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>#<?= $order['order_id'] ?></td>
                            <td>
                                <div class="disc-info">
                                    <img src="<?= htmlspecialchars($order['disc_image'] ? '../uploads/disc_images/' . $order['disc_image'] : '../assets/images/default-cover.png') ?>" 
                                         class="disc-thumb" alt="Cover">
                                    <div>
                                        <div style="font-weight: 600; color: #fff;"><?= htmlspecialchars($order['disc_title']) ?></div>
                                        <div style="font-size: 12px; color: #999;">Gửi đến: <?= htmlspecialchars($order['receiver_name']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td style="color: var(--spotify-green); font-weight: 600;">
                                <?= number_format($order['price']) ?> VNĐ
                            </td>
                            <td><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                            <td>
                                <span class="order-status status-<?= strtolower($order['status'] ?? 'pending') ?>">
                                    <?= ucfirst($order['status'] ?? 'Pending') ?>
                                </span>
                            </td>
                            <td>
                                <?php if (!empty($order['is_received'])): ?>
                                    <div class="received-badge">
                                        <i class="fa-solid fa-circle-check"></i> Đã nhận
                                    </div>
                                <?php else: ?>
                                    <button class="btn-confirm" onclick="confirmReceived(<?= $order['order_id'] ?>, this)">
                                        Đã nhận hàng
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script>
function confirmReceived(orderId, btnElement) {
    if (!confirm('Xác nhận bạn đã nhận được đơn hàng này?')) return;

    btnElement.disabled = true;
    btnElement.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang xử lý...';

    fetch('update_received_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ order_id: orderId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const parent = btnElement.parentElement;
            parent.innerHTML = `
                <div class="received-badge" style="animation: fadeIn 0.3s;">
                    <i class="fa-solid fa-circle-check"></i> Đã nhận
                </div>
            `;
        } else {
            alert('Có lỗi xảy ra. Vui lòng thử lại.');
            btnElement.disabled = false;
            btnElement.textContent = 'Đã nhận hàng';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Lỗi kết nối.');
        btnElement.disabled = false;
        btnElement.textContent = 'Đã nhận hàng';
    });
}
</script>

<?php require_once '../partials/player.php'; ?>

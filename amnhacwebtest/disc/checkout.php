<?php

require_once "../config/database.php";
require_once "../auth/check_login.php";

if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

$db = new Database();
$conn = $db->connect();

$total = 0;
$cart_items = [];

foreach ($_SESSION['cart'] as $item) {
    if (!isset($item['disc_id'])) continue;
    
    $disc_id = $item['disc_id'];
    $price   = $item['price'];
    $qty     = $item['qty'] ?? 1;

  // Lấy thông tin đĩa từ bảng discs
    $sql = "SELECT disc_title, disc_image FROM discs WHERE disc_id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("i", $disc_id);
    $stmt->execute();
    $disc = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$disc) {
        continue;
    }

    $item_total = $price * $qty;
    $total += $item_total;

    $cart_items[] = [
        'disc_id' => $disc_id,
        'name'    => $disc['disc_title'],
        'image'   => $disc['disc_image'],
        'price'   => $price,
        'qty'     => $qty,
        'subtotal' => $item_total
    ];
}

include "../partials/header.php";
include "../partials/sidebar.php";
?>

<style>
    .checkout-content {
        margin-left: 260px;
        padding: 100px 40px 40px;
        min-height: 100vh;
        background: linear-gradient(to bottom, #1e1e1e, #121212);
    }

    .checkout-container {
        max-width: 1000px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 1.5fr 1fr;
        gap: 40px;
    }

    .checkout-header {
        grid-column: 1 / -1;
        margin-bottom: 20px;
    }

    .checkout-header h1 {
        font-size: 32px;
        font-weight: 800;
        margin-bottom: 10px;
    }

    .checkout-card {
        background: rgba(255, 255, 255, 0.03);
        border-radius: 12px;
        padding: 30px;
        border: 1px solid rgba(255, 255, 255, 0.05);
    }

    .section-title {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .section-title i { color: var(--spotify-green); }

    /* Form Styles */
    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: var(--text-sub);
        margin-bottom: 8px;
    }

    .form-input {
        width: 100%;
        background: #2a2a2a;
        border: 1px solid transparent;
        border-radius: 6px;
        padding: 12px 16px;
        color: #fff;
        font-family: inherit;
        font-size: 14px;
        transition: 0.3s;
    }

    .form-input:focus {
        outline: none;
        background: #333;
        border-color: var(--spotify-green);
    }

    textarea.form-input {
        min-height: 100px;
        resize: vertical;
    }

    /* Order Summary Styles */
    .order-items {
        list-style: none;
        padding: 0;
        margin-bottom: 24px;
    }

    .order-item {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 12px 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    .order-item:last-child { border-bottom: none; }

    .order-item img {
        width: 48px;
        height: 48px;
        border-radius: 4px;
        object-fit: cover;
    }

    .item-info { flex: 1; }
    .item-name { font-weight: 600; font-size: 14px; display: block; }
    .item-meta { color: var(--text-sub); font-size: 12px; }
    .item-price { color: var(--spotify-green); font-weight: 600; font-size: 13px; margin-top: 4px; }

    .summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        font-size: 14px;
        color: var(--text-sub);
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    .summary-total {
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .total-label { font-size: 16px; font-weight: 600; }
    .total-amount { font-size: 24px; font-weight: 800; color: var(--spotify-green); }

    .btn-confirm {
        width: 100%;
        background: var(--spotify-green);
        color: #000;
        border: none;
        border-radius: 500px;
        padding: 16px;
        font-size: 16px;
        font-weight: 700;
        cursor: pointer;
        margin-top: 24px;
        transition: 0.2s;
    }

    .btn-confirm:hover {
        transform: scale(1.02);
        background: #1ed760;
    }

    @media (max-width: 992px) {
        .checkout-container { grid-template-columns: 1fr; }
    }
</style>

<div class="checkout-content">
    <div class="checkout-container">
        <div class="checkout-header">
            <h1>Xác nhận thanh toán</h1>
            <p style="color: var(--text-sub);">Vui lòng kiểm tra lại thông tin và đơn hàng trước khi xác nhận.</p>
        </div>

        <div class="checkout-main">
            <div class="checkout-card">
                <h2 class="section-title"><i class="fa-solid fa-truck"></i> Thông tin giao hàng</h2>
                <form action="discorderprocess.php" id="checkoutForm" method="POST">
                    <div class="form-group">
                        <label>Họ và tên người nhận</label>
                        <input type="text" name="receiver_name" class="form-input" placeholder="Nhập tên đầy đủ" required>
                    </div>

                    <div class="form-group">
                        <label>Số điện thoại</label>
                        <input type="text" name="receiver_phone" class="form-input" placeholder="Nhập số điện thoại" required>
                    </div>

                    <div class="form-group">
                        <label>Địa chỉ nhận hàng</label>
                        <textarea name="receiver_address" class="form-input" placeholder="Số nhà, tên đường, quận/huyện..." required></textarea>
                    </div>

                    <div class="form-group">
                        <label>Ghi chú (tùy chọn)</label>
                        <textarea name="note" class="form-input" placeholder="Ví dụ: Giao vào giờ hành chính..."></textarea>
                    </div>
                </form>
            </div>
        </div>

        <div class="checkout-sidebar">
            <div class="checkout-card">
                <h2 class="section-title"><i class="fa-solid fa-receipt"></i> Tóm tắt đơn hàng</h2>
                <ul class="order-items">
                    <?php foreach ($cart_items as $item): ?>
                        <?php
                            // Xây dựng đường dẫn hình ảnh từ thư mục uploads hoặc assets
                            $image_file = $item['image'];
                            // Kiểm tra xem file có tồn tại không
                            $possible_paths = [
                                '../uploads/discs/' . $image_file,
                                '../assets/images/discs/' . $image_file,
                                'uploads/discs/' . $image_file,
                                'assets/images/discs/' . $image_file
                            ];
                            
                            $image_path = null;
                            foreach ($possible_paths as $path) {
                                if (file_exists($path)) {
                                    $image_path = $path;
                                    break;
                                }
                            }
                            
                            // Nếu không tìm thấy, dùng placeholder đơn giản
                            if (!$image_path) {
                                $image_path = 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2248%22 height=%2248%22%3E%3Crect fill=%22%23444%22 width=%2248%22 height=%2248%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22 fill=%22%23999%22 font-size=%2212%22%3ENo Image%3C/text%3E%3C/svg%3E';
                            }
                        ?>
                        <li class="order-item">
                            <img src="<?= htmlspecialchars($image_path) ?>" 
                                 alt="<?= htmlspecialchars($item['name']) ?>"
                                 style="width:48px; height:48px; border-radius:4px; object-fit:cover; background:#333;">
                            <div class="item-info">
                                <span class="item-name"><?= htmlspecialchars($item['name']) ?></span>
                                <span class="item-meta">SL: <?= $item['qty'] ?></span>
                                <span class="item-price"><?= number_format($item['subtotal']) ?>đ</span>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>                <div class="summary-row">
                    <span>Tạm tính:</span>
                    <span><?= number_format($total) ?>đ</span>
                </div>

                <div class="summary-row">
                    <span>Phí vận chuyển:</span>
                    <span style="color: var(--spotify-green);">Miễn phí</span>
                </div>

                <div class="summary-total">
                    <span class="total-label">Tổng cộng:</span>
                    <span class="total-amount"><?= number_format($total) ?>đ</span>
                </div>

                <button type="submit" form="checkoutForm" class="btn-confirm">Xác nhận đặt đĩa</button>
                
                <p style="font-size: 11px; color: var(--text-sub); text-align: center; margin-top: 15px;">
                    Bằng cách đặt hàng, bạn đồng ý với các Điều khoản dịch vụ của Music Platform.
                </p>
            </div>
        </div>
    </div>
</div>

<?php include "../partials/player.php"; ?>
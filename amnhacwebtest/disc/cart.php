<?php
// Kiểm tra đăng nhập trước khi xem giỏ hàng
require_once '../auth/check_login.php';
// Nhúng thanh đầu trang và thanh menu điều hướng
require_once '../partials/header.php';
require_once '../partials/sidebar.php';

// Khởi tạo session nếu chưa có
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Lấy danh sách sản phẩm từ session 'cart', nếu không có thì mặc định là mảng rỗng
$cart = $_SESSION['cart'] ?? [];
$total = 0; // Biến tính tổng tiền
?>

<style>
    /* Bao quanh toàn bộ nội dung giỏ hàng */
    .cart-wrapper {
        margin-left: 260px; /* Chừa chỗ cho sidebar */
        padding: 100px 40px 120px 40px;
        width: calc(100% - 260px);
        min-height: 100vh;
    }

    .cart-header {
        margin-bottom: 40px;
    }

    .cart-header h1 {
        font-size: 3rem;
        font-weight: 800;
        letter-spacing: -2px; /* Thu hẹp khoảng cách chữ cho tiêu đề lớn */
        margin-bottom: 8px;
    }

    /* Chia bố cục thành 2 cột: Danh sách và Tóm tắt */
    .cart-container {
        display: grid;
        grid-template-columns: 1fr 350px;
        gap: 32px;
        align-items: start;
    }

    /* Container cho bảng sản phẩm với hiệu ứng kính mờ nhẹ */
    .cart-table-container {
        background: rgba(255, 255, 255, 0.03);
        border-radius: 12px;
        padding: 24px;
        border: 1px solid rgba(255, 255, 255, 0.05);
    }

    .cart-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    .cart-table th {
        color: var(--text-sub);
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 1px;
        padding-bottom: 16px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .cart-table td {
        padding: 16px 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        vertical-align: middle;
    }

    .item-info {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .item-icon {
        width: 48px;
        height: 48px;
        background: var(--nav-hover);
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: var(--spotify-green);
    }

    .item-details b {
        display: block;
        font-size: 16px;
        margin-bottom: 2px;
    }

    .item-details span {
        font-size: 14px;
        color: var(--text-sub);
    }

    .price-cell {
        font-weight: 600;
        font-size: 15px;
    }

    /* Nút xóa sản phẩm */
    .remove-btn {
        color: var(--text-sub);
        transition: 0.2s;
        padding: 8px;
    }

    .remove-btn:hover {
        color: var(--logout-red); /* Đổi màu đỏ khi di chuột qua */
        transform: scale(1.1); /* Phóng to nhẹ */
    }

    /* Thẻ tóm tắt đơn hàng bên phải */
    .summary-card {
        background: var(--card-bg);
        border-radius: 12px;
        padding: 24px;
        position: sticky; /* Giữ bảng luôn hiển thị khi cuộn trang */
        top: 100px;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .summary-title {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 16px;
        font-size: 14px;
        color: var(--text-sub);
    }

    .summary-total {
        display: flex;
        justify-content: space-between;
        margin-top: 24px;
        padding-top: 24px;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        font-size: 20px;
        font-weight: 800;
        color: var(--text-main);
    }

    .btn-checkout {
        display: block;
        width: 100%;
        background-color: var(--spotify-green);
        color: #000;
        text-align: center;
        padding: 16px;
        border-radius: 500px; /* Bo tròn hoàn toàn */
        font-weight: 700;
        margin-top: 32px;
        font-size: 15px;
        border: none;
        cursor: pointer;
        transition: transform 0.2s, background-color 0.2s;
    }

    .btn-checkout:hover {
        transform: scale(1.02);
        background-color: #1ed760; /* Màu xanh nhạt hơn khi hover */
    }

    /* Empty State */
    .empty-cart {
        text-align: center;
        padding: 100px 0;
    }

    .empty-cart i {
        font-size: 80px;
        color: var(--nav-hover);
        margin-bottom: 24px;
    }

    .empty-cart h2 {
        font-size: 24px;
        margin-bottom: 16px;
    }

    .btn-shop {
        display: inline-block;
        background: #fff;
        color: #000;
        padding: 12px 32px;
        border-radius: 500px;
        font-weight: 700;
        margin-top: 24px;
        transition: transform 0.2s;
    }

    .btn-shop:hover {
        transform: scale(1.05);
    }

    @media (max-width: 1024px) {
        .cart-container {
            grid-template-columns: 1fr;
        }
    }
</style>

<main class="cart-wrapper">
    <!-- Tiêu đề trang giỏ hàng -->
    <div class="cart-header">
        <h1>Giỏ hàng của bạn</h1>
        <p style="color: var(--text-sub);">Các bản ghi âm nhạc cao cấp đang chờ bạn.</p>
    </div>

    <?php if (empty($cart)): ?>
        <!-- Hiển thị khi giỏ hàng chưa có sản phẩm -->
        <div class="empty-cart">
            <i class="fa-solid fa-shopping-basket"></i>
            <h2>Giỏ hàng của bạn đang trống</h2>
            <p style="color: var(--text-sub);">Hãy khám phá thư viện âm nhạc của chúng tôi.</p>
            <a href="disclist.php" class="btn-shop">Tiếp tục mua sắm</a>
        </div>
    <?php else: ?>
        <!-- Hiển thị nội dung giỏ hàng -->
        <div class="cart-container">
            <!-- Phần danh sách sản phẩm (Bên trái) -->
            <div class="cart-table-container">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Giá niêm yết</th>
                            <th style="width: 50px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart as $item): ?>
                            <?php $total += $item['price']; // Cộng dồn tổng tiền ?>
                            <tr>
                                <td>
                                    <div class="item-info">
                                        <div class="item-icon">
                                            <i class="fa-solid fa-compact-disc"></i>
                                        </div>
                                        <div class="item-details">
                                            <b><?= htmlspecialchars($item['title']) ?></b>
                                            <span>Premium Disc</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="price-cell"><?= number_format($item['price']) ?> VNĐ</td>
                                <td>
                                    <!-- Nút xóa sản phẩm khỏi giỏ dựa trên ID -->
                                    <a href="remove_from_cart.php?disc_id=<?= $item['disc_id'] ?>" class="remove-btn" title="Xóa khỏi giỏ hàng">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Phần tóm tắt đơn hàng và thanh toán (Bên phải) -->
            <div class="summary-card">
                <h3 class="summary-title">Tóm tắt đơn hàng</h3>
                <div class="summary-row">
                    <span>Số lượng sản phẩm:</span>
                    <span><?= count($cart) ?></span>
                </div>
                <div class="summary-row">
                    <span>Phí vận chuyển:</span>
                    <span>Miễn phí</span>
                </div>
                <!-- Tổng số tiền cần thanh toán -->
                <div class="summary-total">
                    <span>Tổng cộng:</span>
                    <span><?= number_format($total) ?> VNĐ</span>
                </div>

                <!-- Form gửi yêu cầu thanh toán -->
                <form action="checkout.php" method="POST">
                    <button type="submit" class="btn-checkout">
                        Tiến hành thanh toán
                    </button>
                </form>

                <p style="font-size: 11px; color: var(--text-sub); text-align: center; margin-top: 20px;">
                    Bằng cách nhấn thanh toán, bạn đồng ý với các điều khoản dịch vụ của chúng tôi.
                </p>
            </div>
        </div>
    <?php endif; ?>
</main>

<?php require_once '../partials/player.php'; ?>

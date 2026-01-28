<?php
require_once '../config/database.php';
require_once '../auth/check_login.php';
require_once '../partials/header.php';
require_once '../partials/sidebar.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$db = new Database();
$conn = $db->connect();

$sql = "
    SELECT 
        d.disc_id,
        d.price,
        s.title,
        s.cover_image,
        u.username AS artist_name
    FROM discs d
    JOIN songs s ON d.song_id = s.song_id
    JOIN users u ON s.artist_id = u.user_id
    ORDER BY d.disc_id DESC
";

$result = $conn->query($sql);
$discList = $result->fetch_all(MYSQLI_ASSOC);
?>

<style>
    /* Container nội dung chính của cửa hàng đĩa */
    .disc-content {
        margin-left: 260px; /* Nhường chỗ cho sidebar bên trái */
        padding: 80px 32px 120px 32px;
        width: calc(100% - 260px);
        min-height: 100vh;
    }

    .shop-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 32px;
    }

    .shop-title h1 { font-size: 2rem; font-weight: 800; margin: 0; }
    .shop-title p { color: var(--text-sub); margin-top: 4px; }

    /* Nút giỏ hàng màu trắng nổi bật */
    .cart-link {
        background: #fff;
        color: #000;
        padding: 12px 24px;
        border-radius: 500px;
        font-weight: 700;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: transform 0.2s;
    }
    .cart-link:hover { transform: scale(1.05); } /* Hiệu ứng phóng to nhẹ khi di chuột */

    .disc-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 24px;
    }

    /* Thẻ hiển thị thông tin đĩa nhạc */
    .disc-card {
        background: #181818;
        padding: 16px;
        border-radius: 8px;
        transition: background 0.3s;
        border: 1px solid #282828;
    }
    .disc-card:hover { background: #282828; } /* Đổi màu nền sáng hơn khi hover */

    /* Hiệu ứng đổ bóng cho ảnh đĩa */
    .disc-img-wrapper {
        position: relative;
        padding-bottom: 100%; /* Giữ tỉ lệ khung hình vuông 1:1 */
        margin-bottom: 16px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.5);
    }

    .disc-img {
        position: absolute;
        width: 100%;
        height: 100%;
        object-fit: cover; /* Cắt ảnh vừa khít khung hình vuông */
        border-radius: 4px;
    }

    .disc-title {
        font-weight: 700;
        font-size: 16px;
        margin-bottom: 4px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .disc-artist { color: var(--text-sub); font-size: 14px; margin-bottom: 12px; }

    .disc-price {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--spotify-green); /* Giá tiền màu xanh lá đặc trưng */
        margin-bottom: 16px;
        display: block;
    }

    .btn-buy {
        width: 100%;
        background: var(--spotify-green);
        color: #000;
        border: none;
        padding: 10px;
        border-radius: 4px;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    .btn-buy:hover { background: #1ed760; }
</style>

<main class="disc-content">
    <div class="shop-header">
        <div class="shop-title">
            <h1>💿 Cửa hàng Đĩa nhạc</h1>
            <p>Sở hữu những bản nhạc chất lượng cao nhất</p>
        </div>
        <a href="cart.php" class="cart-link">
            <i class="fa-solid fa-cart-shopping"></i> Giỏ hàng của tôi
        </a>
    </div>

    <?php if (empty($discList)): ?>
        <p style="text-align:center; color: var(--text-sub); padding: 40px;">Hiện chưa có đĩa nào được bán.</p>
    <?php else: ?>
        <div class="disc-grid">
            <?php foreach ($discList as $disc): ?>
            <div class="disc-card">
                <div class="disc-img-wrapper">
                    <img src="<?= htmlspecialchars($disc['cover_image'] ?? '../assets/images/default-cover.png') ?>" class="disc-img">
                </div>
                <div class="disc-title"><?= htmlspecialchars($disc['title']) ?></div>
                <div class="disc-artist"><?= htmlspecialchars($disc['artist_name']) ?></div>
                <span class="disc-price"><?= number_format($disc['price']) ?> VNĐ</span>
                <form action="add_to_cart.php" method="POST">
                    <input type="hidden" name="disc_id" value="<?= $disc['disc_id'] ?>">
                    <input type="hidden" name="title" value="<?= htmlspecialchars($disc['title']) ?>">
                    <input type="hidden" name="price" value="<?= $disc['price'] ?>">
                    <button type="submit" class="btn-buy">
                        <i class="fa-solid fa-cart-plus"></i> Thêm vào giỏ
                    </button>
                </form>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<?php require_once '../partials/player.php'; ?>

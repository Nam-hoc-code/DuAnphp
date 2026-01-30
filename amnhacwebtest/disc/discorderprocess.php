<?php
require_once "../auth/check_login.php";
require_once "../config/database.php";

if (empty($_SESSION['cart'])) {
    die("Giỏ hàng trống");
}

$user_id = $_SESSION['user']['user_id'];

// ✅ ĐÚNG TÊN FIELD TỪ FORM
$receiver_name    = $_POST['receiver_name'];
$receiver_phone   = $_POST['receiver_phone'];
$receiver_address = $_POST['receiver_address'];

$db = new Database();
$conn = $db->connect();

$sql = "
    INSERT INTO disc_orders
    (disc_id, user_id, receiver_name, phone, address, status, created_at)
    VALUES (?, ?, ?, ?, ?, 'pending', NOW())
";

$stmt = $conn->prepare($sql);

/* ✅ DUYỆT ĐÚNG CẤU TRÚC CART */
foreach ($_SESSION['cart'] as $item) {

    $disc_id = (int) $item['disc_id'];

    $stmt->bind_param(
        "iisss",
        $disc_id,
        $user_id,
        $receiver_name,
        $receiver_phone,
        $receiver_address
    );

    $stmt->execute();
}

/* 🧹 XÓA GIỎ HÀNG */
unset($_SESSION['cart']);

include "../partials/header.php";
include "../partials/sidebar.php";
?>

<style>
    .order-processed-content {
        margin-left: 260px;
        padding: 100px 20px;
        min-height: 100vh;
        background: linear-gradient(to bottom, #1e1e1e, #121212);
        display: flex;
        justify-content: center;
        align-items: flex-start;
    }

    .success-card {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 20px;
        padding: 60px 40px;
        max-width: 500px;
        width: 100%;
        text-align: center;
        box-shadow: 0 20px 40px rgba(0,0,0,0.4);
        animation: fadeInScale 0.5s ease-out;
    }

    .success-icon {
        width: 80px;
        height: 80px;
        background: var(--spotify-green);
        color: #000;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 40px;
        margin: 0 auto 30px;
        box-shadow: 0 10px 20px rgba(29, 185, 84, 0.3);
    }

    .success-card h2 {
        font-size: 28px;
        font-weight: 850;
        margin-bottom: 16px;
        color: #fff;
    }

    .success-card p {
        color: var(--text-sub);
        font-size: 16px;
        line-height: 1.6;
        margin-bottom: 32px;
    }

    .btn-groups {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .btn-action {
        padding: 14px 32px;
        border-radius: 500px;
        font-weight: 700;
        font-size: 14px;
        text-decoration: none;
        transition: 0.2s;
        display: inline-block;
    }

    .btn-home {
        background: var(--spotify-green);
        color: #000;
    }

    .btn-home:hover {
        transform: scale(1.05);
        background: #1ed760;
    }

    .btn-secondary {
        background: transparent;
        color: #fff;
        border: 1px solid var(--text-sub);
    }

    .btn-secondary:hover {
        border-color: #fff;
        background: rgba(255,255,255,0.05);
    }

    @keyframes fadeInScale {
        from { opacity: 0; transform: scale(0.9); }
        to { opacity: 1; transform: scale(1); }
    }
</style>

<div class="order-processed-content">
    <div class="success-card">
        <div class="success-icon">
            <i class="fa-solid fa-check"></i>
        </div>
        <h2>Đặt hàng thành công!</h2>
        <p>Cảm ơn bạn đã ủng hộ nghệ sĩ. Đơn hàng của bạn đã được tiếp nhận và đang chờ xác nhận.</p>
        
        <div class="btn-groups">
            <a href="../user/home.php" class="btn-action btn-home">Quay về trang chủ</a>
            <a href="../disc/disclist.php" class="btn-action btn-secondary">Tiếp tục mua sắm</a>
        </div>
    </div>
</div>

<?php include "../partials/player.php"; ?>

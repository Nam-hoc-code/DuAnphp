<?php
require_once "../auth/check_login.php";
require_once "../config/database.php";

if (empty($_SESSION['cart'])) {
    die("Giỏ hàng trống");
}

$user_id = $_SESSION['user']['id'];

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

echo "<h2>✅ Đơn hàng đã được xử lý</h2>";
echo "<p>Vui lòng chờ nghệ sĩ xác nhận & đóng gói.</p>";
echo '<a href="../user/home.php">⬅ Về trang chủ</a>';

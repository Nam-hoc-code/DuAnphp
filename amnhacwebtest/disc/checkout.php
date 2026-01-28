<?php
require_once "../config/database.php";
// require_once "../auth/check_login.php";

if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

$db = new Database();
$conn = $db->connect();

$total = 0;
$cart_items = [];

foreach ($_SESSION['cart'] as $item) {

    $disc_id = $item['disc_id'];
    $price   = $item['price'];

    // Lấy tên đĩa từ DB
    $sql = "SELECT s.title 
            FROM discs d 
            JOIN songs s ON d.song_id = s.song_id
            WHERE d.disc_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $disc_id);
    $stmt->execute();
    $disc = $stmt->get_result()->fetch_assoc();

    $total += $price;

    $cart_items[] = [
        'name'  => $disc['title'],
        'price' => $price
    ];
}
?>

<h2>Thanh toán đơn hàng</h2>

<h3>Thông tin người nhận</h3>
<form action="discorderprocess.php" method="POST">

    <label>Họ tên:</label><br>
    <input type="text" name="receiver_name" required><br><br>

    <label>Số điện thoại:</label><br>
    <input type="text" name="receiver_phone" required><br><br>

    <label>Địa chỉ:</label><br>
    <textarea name="receiver_address" required></textarea><br><br>

    <label>Ghi chú:</label><br>
    <textarea name="note"></textarea><br><br>

    <h3>Đơn hàng</h3>
    <ul>
        <?php foreach ($cart_items as $item): ?>
            <li>
                <?= htmlspecialchars($item['name']) ?> –
                <?= number_format($item['price']) ?>đ × 1
            </li>
        <?php endforeach; ?>
    </ul>

    <h3>Tổng tiền: <?= number_format($total) ?>đ</h3>

    <button type="submit">Xác nhận đặt đĩa</button>
</form>

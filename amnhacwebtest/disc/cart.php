<?php
require_once '../auth/check_login.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$cart = $_SESSION['cart'] ?? [];
$total = 0;
?>

<h2>🛒 Giỏ hàng</h2>

<?php if (empty($cart)): ?>
    <p>Giỏ hàng trống</p>
<?php else: ?>
<table border="1" cellpadding="10">
<tr>
    <th>Bài hát</th>
    <th>Giá</th>
    <th></th>
</tr>

<?php foreach ($cart as $item): ?>
<?php $total += $item['price']; ?>
<tr>
    <td><?= htmlspecialchars($item['title']) ?></td>
    <td><?= number_format($item['price']) ?> VNĐ</td>
    <td>
        <a href="remove_from_cart.php?disc_id=<?= $item['disc_id'] ?>">❌</a>
    </td>
</tr>
<?php endforeach; ?>

<tr>
    <td><b>Tổng</b></td>
    <td colspan="2"><b><?= number_format($total) ?> VNĐ</b></td>
</tr>
</table>

<form action="checkout.php" method="POST">
    <button type="submit">✅ Thanh toán</button>
</form>
<?php endif; ?>

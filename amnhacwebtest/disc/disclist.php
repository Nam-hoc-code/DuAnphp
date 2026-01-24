<?php
require_once '../config/database.php';
require_once '../auth/check_login.php';

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
        u.username AS artist_name
    FROM discs d
    JOIN songs s ON d.song_id = s.song_id
    JOIN users u ON s.artist_id = u.user_id
    ORDER BY d.disc_id DESC
";

$result = $conn->query($sql);
$discList = $result->fetch_all(MYSQLI_ASSOC);
?>

<h2>💿 Danh sách đĩa nhạc</h2>

<a href="cart.php">🛒 Xem giỏ hàng</a>

<table border="1" cellpadding="10">
<tr>
    <th>Bài hát</th>
    <th>Nghệ sĩ</th>
    <th>Giá</th>
    <th></th>
</tr>

<?php foreach ($discList as $disc): ?>
<tr>
    <td><?= htmlspecialchars($disc['title']) ?></td>
    <td><?= htmlspecialchars($disc['artist_name']) ?></td>
    <td><?= number_format($disc['price']) ?> VNĐ</td>
    <td>
        <form action="add_to_cart.php" method="POST">
            <input type="hidden" name="disc_id" value="<?= $disc['disc_id'] ?>">
            <input type="hidden" name="title" value="<?= htmlspecialchars($disc['title']) ?>">
            <input type="hidden" name="price" value="<?= $disc['price'] ?>">
            <button type="submit">➕ Thêm vào giỏ</button>
        </form>
    </td>
</tr>
<?php endforeach; ?>
</table>

<?php
require_once '../config/db.php';
require_once '../check_login.php';

if (!isset($_GET['event_id'])) {
    die('Thiếu event_id');
}

$event_id = (int)$_GET['event_id'];

$sql = "SELECT * FROM event WHERE event_id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$event_id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    die('Sự kiện không tồn tại');
}
?>

<h2>Chi tiết sự kiện</h2>

<p><b>Tên sự kiện:</b> <?= htmlspecialchars($event['name']) ?></p>
<p><b>Ngày diễn ra:</b> <?= date('d/m/Y', strtotime($event['event_date'])) ?></p>
<p><b>Giá vé:</b> <?= number_format($event['price']) ?> VNĐ</p>

<form action="ticketprocess.php" method="POST">
    <input type="hidden" name="event_id" value="<?= $event['event_id'] ?>">
    <button type="submit">Mua vé</button>
</form>

<a href="eventlist.php">⬅ Quay lại</a>

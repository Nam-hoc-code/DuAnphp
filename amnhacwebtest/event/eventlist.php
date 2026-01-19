<?php
require_once '../config/db.php';
require_once '../check_login.php';

$sql = "SELECT * FROM event ORDER BY event_date ASC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Danh sách sự kiện</h2>

<?php if (isset($_GET['success'])): ?>
    <p style="color:green;">Mua vé thành công!</p>
<?php endif; ?>

<table border="1" cellpadding="10">
    <tr>
        <th>Tên sự kiện</th>
        <th>Ngày diễn ra</th>
        <th>Giá vé</th>
        <th></th>
    </tr>

    <?php foreach ($events as $event): ?>
    <tr>
        <td><?= htmlspecialchars($event['name']) ?></td>
        <td><?= date('d/m/Y', strtotime($event['event_date'])) ?></td>
        <td><?= number_format($event['price']) ?> VNĐ</td>
        <td>
            <a href="eventdetail.php?event_id=<?= $event['event_id'] ?>">
                Xem chi tiết
            </a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

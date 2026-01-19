<?php
require_once '../config/db.php';
require_once '../auth_check.php';

$sql = "
    SELECT 
        d.disc_id,
        d.price,
        s.title,
        s.artist
    FROM disc d
    JOIN songs s ON d.song_id = s.song_id
    ORDER BY d.disc_id DESC
";
$stmt = $conn->prepare($sql);
$stmt->execute();
$discList = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Danh sách đĩa nhạc</h2>

<?php if (isset($_GET['success'])): ?>
    <p style="color:green;">Mua đĩa thành công!</p>
<?php endif; ?>

<table border="1" cellpadding="10">
    <tr>
        <th>Bài hát</th>
        <th>Nghệ sĩ</th>
        <th>Giá</th>
        <th>Hành động</th>
    </tr>

    <?php foreach ($discList as $disc): ?>
    <tr>
        <td><?= htmlspecialchars($disc['title']) ?></td>
        <td><?= htmlspecialchars($disc['artist']) ?></td>
        <td><?= number_format($disc['price']) ?> VNĐ</td>
        <td>
            <a href="discdetail.php?disc_id=<?= $disc['disc_id'] ?>">
                Xem chi tiết
            </a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

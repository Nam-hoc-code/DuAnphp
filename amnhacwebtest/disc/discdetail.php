<?php
require_once '../config/db.php';
require_once '../auth_check.php';

if (!isset($_GET['disc_id'])) {
    die('Thiếu disc_id');
}

$disc_id = (int)$_GET['disc_id'];

$sql = "
    SELECT 
        d.disc_id,
        d.price,
        s.title,
        s.artist
    FROM disc d
    JOIN songs s ON d.song_id = s.song_id
    WHERE d.disc_id = ?
";
$stmt = $conn->prepare($sql);
$stmt->execute([$disc_id]);
$disc = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$disc) {
    die('Đĩa không tồn tại');
}
?>

<h2>Chi tiết đĩa nhạc</h2>

<p><b>Bài hát:</b> <?= htmlspecialchars($disc['title']) ?></p>
<p><b>Nghệ sĩ:</b> <?= htmlspecialchars($disc['artist']) ?></p>
<p><b>Giá:</b> <?= number_format($disc['price']) ?> VNĐ</p>

<form action="discorderprocess.php" method="POST">
    <input type="hidden" name="disc_id" value="<?= $disc['disc_id'] ?>">
    <button type="submit">Mua đĩa</button>
</form>

<a href="disclist.php">⬅ Quay lại danh sách</a>

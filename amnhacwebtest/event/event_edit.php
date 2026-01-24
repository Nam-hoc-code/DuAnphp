<?php
require_once '../auth/check_login.php';
require_once '../config/database.php';

if (($_SESSION['user']['role'] ?? '') !== 'ADMIN') {
    die('Bạn không có quyền sửa sự kiện');
}

$event_id = $_GET['id'] ?? null;
if (!$event_id) {
    die('Thiếu ID');
}

$db = new Database();
$conn = $db->connect();

$sql = "SELECT * FROM events WHERE event_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die('Sự kiện không tồn tại');
}

$event = $result->fetch_assoc();

?>

<h2>✏️ Sửa sự kiện</h2>

<form action="event_update_process.php" method="post">
    <input type="hidden" name="event_id" value="<?= $event['event_id'] ?>">

    <label>Tên sự kiện</label><br>
    <input type="text" name="name" value="<?= htmlspecialchars($event['name']) ?>" required><br><br>

    <label>Ngày tổ chức</label><br>
    <input type="date" name="event_date" value="<?= $event['event_date'] ?>" required><br><br>

    <label>Giá vé</label><br>
    <input type="number" name="price" value="<?= $event['price'] ?>" required><br><br>

    <label>Link mua vé</label><br>
    <input type="text" name="buy_url" value="<?= htmlspecialchars($event['buy_url']) ?>"><br><br>

    <button type="submit">💾 Lưu thay đổi</button>
</form>

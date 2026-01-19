<?php
require_once '../config/db.php';
require_once '../check_login.php';

if (!isset($_POST['event_id'])) {
    die('Dữ liệu không hợp lệ');
}

$event_id = (int)$_POST['event_id'];
$user_id  = $_SESSION['user_id'];

/* Kiểm tra event tồn tại */
$check = $conn->prepare("SELECT event_id FROM event WHERE event_id = ?");
$check->execute([$event_id]);

if ($check->rowCount() === 0) {
    die('Sự kiện không tồn tại');
}

/* Lưu vé */
$sql = "
    INSERT INTO event_tickets (event_id, user_id, created_at)
    VALUES (?, ?, NOW())
";
$stmt = $conn->prepare($sql);
$stmt->execute([$event_id, $user_id]);

header("Location: eventlist.php?success=1");
exit;

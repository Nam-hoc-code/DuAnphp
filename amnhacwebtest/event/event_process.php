<?php
require_once '../config/database.php';

$db = new Database();
$conn = $db->connect();

/* =========================
   LẤY DANH SÁCH SỰ KIỆN
========================= */
$sql = "
    SELECT 
        event_id,
        name,
        event_date,
        price,
        buy_url,
        banner_image
    FROM events
    ORDER BY event_date ASC
";

$result = $conn->query($sql);

$events = [];
if ($result) {
    $events = $result->fetch_all(MYSQLI_ASSOC);
}

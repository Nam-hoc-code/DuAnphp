<?php
require_once "check_artist.php";
require_once "../config/database.php";

if (!isset($_SESSION['user']['id'])) {
    die("Chưa đăng nhập");
}

$artist_id = (int) $_SESSION['user']['id'];

$db = new Database();
$conn = $db->connect();

/*
 Lấy các đơn hàng mà bài hát thuộc về nghệ sĩ này
*/
$sql = "
SELECT 
    o.order_id,
    u.username AS buyer,
    s.title AS song_title,
    d.price,
    o.created_at
FROM disc_orders o
JOIN discs d ON o.disc_id = d.disc_id
JOIN songs s ON d.song_id = s.song_id
JOIN users u ON o.user_id = u.user_id
WHERE s.artist_id = ?
ORDER BY o.created_at DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $artist_id);
$stmt->execute();
$result = $stmt->get_result();

echo "<h2>Đơn hàng đĩa của tôi</h2>";

while ($row = $result->fetch_assoc()) {
    echo "
    <p>
        🧾 Đơn #{$row['order_id']} |
        🎵 {$row['song_title']} |
        👤 {$row['buyer']} |
        💰 {$row['price']} |
        🕒 {$row['created_at']}
    </p>
    ";
}

<?php
require_once "check_artist.php";
require_once "../config/database.php";

if (!isset($_POST['disc_id'])) {
    die("Thiếu disc_id");
}

$disc_id   = (int) $_POST['disc_id'];
$artist_id = $_SESSION['user']['id'];

$db = new Database();
$conn = $db->connect();

/* 🔒 Không cho xóa nếu đã có đơn */
$check = $conn->prepare("
    SELECT COUNT(*) 
    FROM disc_orders 
    WHERE disc_id = ?
");
$check->bind_param("i", $disc_id);
$check->execute();
$check->bind_result($count);
$check->fetch();
$check->close();

if ($count > 0) {
    die("❌ Đĩa đã có đơn hàng, không thể xóa");
}

/* ✅ Xóa đĩa (chỉ của artist đó) */
$sql = "
    DELETE d FROM discs d
    JOIN songs s ON d.song_id = s.song_id
    WHERE d.disc_id = ? AND s.artist_id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $disc_id, $artist_id);
$stmt->execute();

header("Location: oders.php");
exit;

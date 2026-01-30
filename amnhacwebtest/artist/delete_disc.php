<?php

require_once "check_artist.php";
require_once "../config/database.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request");
}

if (!isset($_POST['disc_id'])) {
    die("Thiếu disc_id");
}

$disc_id   = (int) $_POST['disc_id'];
$artist_id = (int) $_SESSION['user']['user_id'];  

$db = new Database();
$conn = $db->connect();

// ✅ Kiểm tra disc có phải của artist này không
$check_owner = $conn->prepare("
    SELECT disc_id 
    FROM discs 
    WHERE disc_id = ? AND artist_id = ?
");
$check_owner->bind_param("ii", $disc_id, $artist_id);
$check_owner->execute();
$check_owner->store_result();

if ($check_owner->num_rows === 0) {
    die("❌ Đĩa này không tồn tại hoặc không phải của bạn");
}
$check_owner->close();

// 🔒 Không cho xóa nếu đã có đơn
$check = $conn->prepare("
    SELECT COUNT(*) AS order_count
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

// ✅ Xóa chi tiết đĩa trước (disc_details)
$sql_details = "
    DELETE FROM disc_details 
    WHERE disc_id = ?
";
$stmt_details = $conn->prepare($sql_details);
$stmt_details->bind_param("i", $disc_id);
if (!$stmt_details->execute()) {
    die("❌ Lỗi khi xóa chi tiết đĩa: " . $stmt_details->error);
}
$stmt_details->close();

// ✅ Xóa đĩa (soft delete - đặt is_deleted = 1)
$sql = "
    UPDATE discs 
    SET is_deleted = 1 
    WHERE disc_id = ? AND artist_id = ?
";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("❌ Lỗi prepare: " . $conn->error);
}

$stmt->bind_param("ii", $disc_id, $artist_id);
if (!$stmt->execute()) {
    die("❌ Lỗi khi xóa đĩa: " . $stmt->error);
}
$stmt->close();

// ✅ Xóa file hình ảnh nếu có
$get_image = $conn->prepare("
    SELECT disc_image 
    FROM discs 
    WHERE disc_id = ?
");
$get_image->bind_param("i", $disc_id);
$get_image->execute();
$get_image->bind_result($disc_image);
$get_image->fetch();
$get_image->close();

if (!empty($disc_image)) {
    $image_path = "../uploads/disc_images/" . $disc_image;
    if (file_exists($image_path)) {
        unlink($image_path);
    }
}

header("Location: oders.php?success=delete");
exit;
?>
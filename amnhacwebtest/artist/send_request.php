<?php
require_once "check_artist.php";
require_once "../config/database.php";

if (!isset($_SESSION['user']['user_id'])) {
    die("Chưa đăng nhập");
}

if (!isset($_GET['id'])) {
    die("Thiếu ID bài hát");
}

$song_id   = (int) $_GET['id'];
$artist_id = (int) $_SESSION['user']['user_id'];
$admin_id  = 1; // admin duy nhất

$db = new Database();
$conn = $db->connect();

/* Kiểm tra bài hát có tồn tại & thuộc về nghệ sĩ */
$sql = "SELECT status FROM songs 
        WHERE song_id = ? 
        AND artist_id = ? 
        AND is_deleted = 0";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $song_id, $artist_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die("Không có quyền gửi yêu cầu");
}

$song = $result->fetch_assoc();

/* Chỉ cho gửi khi đang PENDING */
if ($song['status'] !== 'PENDING') {
    die("Bài hát không ở trạng thái chờ duyệt");
}

/* Tạo notification cho admin */
$sql = "INSERT INTO notifications (user_id, content, is_read, created_at)
        VALUES (?, ?, 0, NOW())";

$content = "Nghệ sĩ #$artist_id gửi yêu cầu duyệt bài hát ID #$song_id";

$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $admin_id, $content);
$stmt->execute();

/* Quay về danh sách */
header("Location: my_songs.php");
exit;

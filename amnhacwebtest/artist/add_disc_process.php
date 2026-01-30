<?php

require_once "check_artist.php";
require_once "../config/database.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request");
}

$artist_id = (int)$_SESSION['user']['user_id'];
$disc_title = trim($_POST['disc_title'] ?? '');
$price = (float)($_POST['price'] ?? 0);
$description = trim($_POST['description'] ?? '');
$songs = $_POST['songs'] ?? [];

// ✅ Kiểm tra dữ liệu
if (empty($disc_title)) {
    die("❌ Tên đĩa không được để trống");
}
if ($price < 10000) {
    die("❌ Giá tiền phải từ 10.000 VNĐ trở lên");
}
if (empty($songs)) {
    die("❌ Vui lòng chọn ít nhất 1 bài hát");
}

$db = new Database();
$conn = $db->connect();

// ✅ XỬ LÝ UPLOAD HÌNH ẢNH
$disc_image = '';
if (!empty($_FILES['disc_image']['name'])) {
    $file = $_FILES['disc_image'];
    $allowed = ['image/jpeg', 'image/png', 'image/gif'];
    
    if (!in_array($file['type'], $allowed)) {
        die("❌ Định dạng hình ảnh không hợp lệ. Chỉ chấp nhận JPG, PNG, GIF");
    }
    
    if ($file['size'] > 5 * 1024 * 1024) {
        die("❌ Kích thước hình ảnh vượt quá 5MB");
    }
    
    // Tạo tên file unique
    $upload_dir = "../uploads/disc_images/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('disc_') . '.' . $ext;
    $filepath = $upload_dir . $filename;
    
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        die("❌ Lỗi khi upload hình ảnh");
    }
    
    $disc_image = $filename;
}

// ✅ KIỂM TRA TẤT CẢ BÀI HÁT CÓ PHẢI CỦA ARTIST KHÔNG
foreach ($songs as $song_id) {
    $song_id = (int)$song_id;
    $check = $conn->prepare("
        SELECT song_id 
        FROM songs 
        WHERE song_id = ? AND artist_id = ? AND is_deleted = 0
    ");
    $check->bind_param("ii", $song_id, $artist_id);
    $check->execute();
    $check->store_result();
    
    if ($check->num_rows === 0) {
        die("❌ Một trong các bài hát không thuộc về bạn hoặc đã bị xóa");
    }
}

// ✅ THÊM ĐĨA VÀO BẢNG discs
$sql_disc = "
    INSERT INTO discs (artist_id, disc_title, disc_image, price, description, is_deleted, created_at)
    VALUES (?, ?, ?, ?, ?, 0, NOW())
";
$stmt_disc = $conn->prepare($sql_disc);
if (!$stmt_disc) {
    die("❌ Lỗi prepare: " . $conn->error);
}

$stmt_disc->bind_param("issds", $artist_id, $disc_title, $disc_image, $price, $description);
if (!$stmt_disc->execute()) {
    die("❌ Lỗi thêm đĩa: " . $stmt_disc->error);
}

$disc_id = $stmt_disc->insert_id;

// ✅ THÊM CÁC BÀI HÁT VÀO disc_details
$track_number = 1;
foreach ($songs as $song_id) {
    $song_id = (int)$song_id;
    
    $sql_detail = "
        INSERT INTO disc_details (disc_id, song_id, track_number)
        VALUES (?, ?, ?)
    ";
    $stmt_detail = $conn->prepare($sql_detail);
    if (!$stmt_detail) {
        die("❌ Lỗi prepare chi tiết: " . $conn->error);
    }
    
    $stmt_detail->bind_param("iii", $disc_id, $song_id, $track_number);
    if (!$stmt_detail->execute()) {
        die("❌ Lỗi thêm bài hát: " . $stmt_detail->error);
    }
    
    $track_number++;
}

// ✅ THÀNH CÔNG
header("Location: oders.php?success=1");
exit;
?>
<?php
require_once '../auth/check_login.php';
require_once '../config/database.php';
require_once '../config/cloudinary.php';

use Cloudinary\Api\Upload\UploadApi;

/* 🔐 CHỈ ADMIN */
if (($_SESSION['user']['role'] ?? '') !== 'ADMIN') {
    die('Bạn không có quyền thêm sự kiện');
}

/* ===== LẤY DỮ LIỆU ===== */
$name       = trim($_POST['name'] ?? '');
$event_date = $_POST['event_date'] ?? null;
$price      = $_POST['price'] ?? 0;
$buy_url    = trim($_POST['buy_url'] ?? '');

if ($name === '' || !$event_date || $buy_url === '') {
    die('Thiếu dữ liệu');
}

/* ===== UPLOAD BANNER ===== */
$bannerImage = null;

if (!empty($_FILES['banner']['tmp_name'])) {
    try {
        $upload = (new UploadApi())->upload(
            $_FILES['banner']['tmp_name'],
            [
                'folder' => 'events'
            ]
        );
        $bannerImage = $upload['secure_url'];
    } catch (Exception $e) {
        die('Upload banner thất bại');
    }
}

/* ===== INSERT DB ===== */
$db = new Database();
$conn = $db->connect();

$sql = "
    INSERT INTO events (name, event_date, price, buy_url, banner_image)
    VALUES (?, ?, ?, ?, ?)
";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "ssiss",
    $name,
    $event_date,
    $price,
    $buy_url,
    $bannerImage
);

$stmt->execute();

/* ===== REDIRECT ===== */
header("Location: ../event/event_list.php?msg=added");
exit;

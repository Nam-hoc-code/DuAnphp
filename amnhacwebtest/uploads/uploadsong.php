<?php
$cloudinary = require_once "../config/cloudinary.php";
use Cloudinary\Api\Upload\UploadApi;

if (!isset($_FILES['song']) || $_FILES['song']['error'] !== 0) {
    die("Không có file hoặc upload lỗi");
}

$tmpPath = $_FILES['song']['tmp_name'];

$result = $cloudinary->uploadApi()->upload(
    $tmpPath,
    [
        'resource_type' => 'video', // bắt buộc cho mp3
        'folder' => 'songs/audio'
    ]
);

echo "Upload thành công: " . $result['secure_url'];

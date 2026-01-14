<?php
require_once "check_artist.php";
require_once "../config/database.php";

/* Cloudinary */
$cloudinary = require "../config/cloudinary.php";
use Cloudinary\Api\Upload\UploadApi;

$title = $_POST['title'];
$artist_id = $_SESSION['user_id'];

if (!isset($_FILES['audio']) || !isset($_FILES['cover'])) {
    die("Thiếu file upload");
}

$db = new Database();
$conn = $db->connect();

/* 1️⃣ Upload AUDIO (mp3) */
$audio = (new UploadApi())->upload(
    $_FILES['audio']['tmp_name'],
    [
        'resource_type' => 'video',
        'folder' => 'music/audio'
    ]
);

/* 2️⃣ Upload COVER */
$cover = (new UploadApi())->upload(
    $_FILES['cover']['tmp_name'],
    [
        'folder' => 'music/cover'
    ]
);

$cloud_url       = $audio['secure_url'];
$cloud_public_id = $audio['public_id'];
$cover_image     = $cover['secure_url'];

/* 3️⃣ Lưu DB */
$sql = "INSERT INTO songs
        (title, artist_id, cover_image, cloud_url, cloud_public_id, status, is_deleted, created_at)
        VALUES (?, ?, ?, ?, ?, 'PENDING', 0, NOW())";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "sisss",
    $title,
    $artist_id,
    $cover_image,
    $cloud_url,
    $cloud_public_id
);
$stmt->execute();

header("Location: my_songs.php");
exit;

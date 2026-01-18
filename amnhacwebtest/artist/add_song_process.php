<?php
require_once "check_artist.php";
require_once "../config/database.php";
$cloudinary = require "../config/cloudinary.php";
use Cloudinary\Api\Upload\UploadApi;

$title = $_POST['title'];
$artist_id = $_SESSION['user_id'];

if (!isset($_FILES['audio']) || !isset($_FILES['cover'])) { die("Thiếu file"); }

$db = new Database();
$conn = $db->connect();

// Upload Audio
$audio = (new UploadApi())->upload($_FILES['audio']['tmp_name'], ['resource_type' => 'video', 'folder' => 'music/audio']);
// Upload Cover
$cover = (new UploadApi())->upload($_FILES['cover']['tmp_name'], ['folder' => 'music/cover']);

$sql = "INSERT INTO songs (title, artist_id, cover_image, cloud_url, cloud_public_id, status, is_deleted, created_at)
        VALUES (?, ?, ?, ?, ?, 'PENDING', 0, NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sisss", $title, $artist_id, $cover['secure_url'], $audio['secure_url'], $audio['public_id']);
$stmt->execute();

header("Location: my_songs.php");
exit;
?>

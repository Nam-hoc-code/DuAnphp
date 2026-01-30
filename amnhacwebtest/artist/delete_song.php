<?php
require_once "check_artist.php";
require_once "../config/database.php";

$cloudinary = require "../config/cloudinary.php";
use Cloudinary\Api\Admin\AdminApi;
use Cloudinary\Api\Upload\UploadApi;

$song_id = $_GET['id'];
$artist_id = $_SESSION['user']['user_id'];

$db = new Database();
$conn = $db->connect();

/* Lấy public_id */
$sql = "SELECT cloud_public_id FROM songs 
        WHERE song_id = ? AND artist_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $song_id, $artist_id);
$stmt->execute();
$song = $stmt->get_result()->fetch_assoc();

if (!$song) {
    die("Không tìm thấy bài hát");
}

/* Xóa Cloudinary */
(new UploadApi())->destroy($song['cloud_public_id'], [
    'resource_type' => 'video'
]);

/* Soft delete */
$sql = "UPDATE songs SET is_deleted = 1 WHERE song_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $song_id);
$stmt->execute();

header("Location: my_songs.php");
exit;

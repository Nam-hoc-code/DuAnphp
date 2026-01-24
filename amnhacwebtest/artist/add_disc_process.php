<?php
require_once "check_artist.php";
require_once "../config/database.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request");
}

$artist_id = $_SESSION['user']['id'];
$song_id   = (int)$_POST['song_id'];
$price     = (float)$_POST['price'];

$db = new Database();
$conn = $db->connect();

/* ✅ KIỂM TRA BÀI HÁT CÓ PHẢI CỦA ARTIST KHÔNG */
$check = $conn->prepare("
    SELECT song_id 
    FROM songs 
    WHERE song_id = ? AND artist_id = ?
");
$check->bind_param("ii", $song_id, $artist_id);
$check->execute();
$check->store_result();

if ($check->num_rows === 0) {
    die("❌ Bài hát không thuộc về bạn");
}

/* ✅ THÊM ĐĨA (KHÔNG CÓ artist_id) */
$sql = "
    INSERT INTO discs (song_id, price)
    VALUES (?, ?)
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("id", $song_id, $price);
$stmt->execute();

header("Location: oders.php");
exit;

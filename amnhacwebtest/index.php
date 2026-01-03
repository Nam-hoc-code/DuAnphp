<?php
require_once "config/database.php";

$db = new Database();
$conn = $db->connect();

$sql = "SELECT title, artist, file_path 
        FROM songs 
        WHERE is_deleted = 0";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Nghe nhạc đơn giản</title>
</head>
<body>

<h2>Danh sách bài hát</h2>

<?php while ($song = $result->fetch_assoc()) { ?>
    <p>
        <strong><?= $song['title'] ?></strong> - <?= $song['artist'] ?>
    </p>

    <audio controls>
        <source src="<?= $song['file_path'] ?>" type="audio/mpeg">
        Trình duyệt không hỗ trợ audio
    </audio>

    <hr>
<?php } ?>

</body>
</html>

<?php
require_once '../config/database.php';
require_once '../check_login.php';

if (!isset($_POST['fav_id'])) {
    die('Thiếu fav_id');
}

$fav_id  = (int)$_POST['fav_id'];
$user_id = $_SESSION['user_id'];

/* Chỉ cho xóa favorite của chính mình */
$sql = "
    DELETE FROM favorites
    WHERE fav_id = ? AND user_id = ?
";
$stmt = $conn->prepare($sql);
$stmt->execute([$fav_id, $user_id]);

header("Location: favorite_list.php");
exit;

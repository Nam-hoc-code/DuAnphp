<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

require_once '../config/database.php';

/* KHÔNG require check_login.php trong API */

if (!isset($_SESSION['user'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Please login first'
    ]);
    exit;
}

if (!isset($_POST['song_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Missing song_id'
    ]);
    exit;
}

$user_id = $_SESSION['user']['user_id'];   // sửa lại key
$song_id = (int)$_POST['song_id'];

$db = new Database();
$conn = $db->connect();

/* Check đã tồn tại chưa */
$checkSql = "SELECT fav_id FROM favorites WHERE user_id = ? AND song_id = ?";
$check = $conn->prepare($checkSql);
$check->bind_param("ii", $user_id, $song_id);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {

    $deleteSql = "DELETE FROM favorites WHERE user_id = ? AND song_id = ?";
    $stmt = $conn->prepare($deleteSql);
    $stmt->bind_param("ii", $user_id, $song_id);
    $stmt->execute();

    echo json_encode([
        'status' => 'success',
        'action' => 'removed'
    ]);
    exit;

} else {

    $insertSql = "INSERT INTO favorites (user_id, song_id, created_at) VALUES (?, ?, NOW())";
    $stmt = $conn->prepare($insertSql);
    $stmt->bind_param("ii", $user_id, $song_id);
    $stmt->execute();

    echo json_encode([
        'status' => 'success',
        'action' => 'added'
    ]);
    exit;
}

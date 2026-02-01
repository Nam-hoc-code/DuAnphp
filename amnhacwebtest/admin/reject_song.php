<?php
require_once "check_admin.php";
require_once "../config/database.php";

$song_id = $_GET['id'];
$admin_id = $_SESSION['user']['user_id'];

$db = new Database();
$conn = $db->connect();

/* Update */
$update = $conn->prepare(
    "UPDATE songs SET status = 'REJECTED' WHERE song_id = ?"
);
$update->bind_param("i", $song_id);
$update->execute();

/* Log */
$log = $conn->prepare(
    "INSERT INTO songs_log (song_id, action, admin_id, action_time)
     VALUES (?, 'REJECT', ?, NOW())"
);
$log->bind_param("ii", $song_id, $admin_id);
$log->execute();

header("Location: song_requests.php");
exit;

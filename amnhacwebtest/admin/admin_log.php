<?php
require_once "../config/database.php";

$db = new Database();
$conn = $db->connect();

$sql = "
    SELECT
        sl.log_id,
        s.title AS song_title,
        sl.action,
        u.username AS admin_name,
        sl.action_time
    FROM songs_log sl 
    JOIN songs s ON sl.song_id = s.song_id
    JOIN users u ON sl.admin_id = u.user_id
    ORDER BY sl.action_time DESC
";

$result = $conn->query($sql);
$logs = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Logs</title>
</head>
<body>

<table border="1" cellpadding="8">
    <tr>
        <th>Log ID</th>
        <th>Tên bài hát</th>
        <th>Action</th>
        <th>Admin</th>
        <th>Time</th>
    </tr>

    <?php foreach ($logs as $log): ?>
        <tr>
            <td><?= $log['log_id'] ?></td>
            <td><?= htmlspecialchars($log['song_title']) ?></td>
            <td><?= $log['action'] ?></td>
            <td><?= htmlspecialchars($log['admin_name']) ?></td>
            <td><?= $log['action_time'] ?></td>
        </tr>
    <?php endforeach; ?>
</table>

</body>
</html>

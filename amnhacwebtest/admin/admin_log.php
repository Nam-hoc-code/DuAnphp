<?php
require_once "check_admin.php";
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
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nhật ký duyệt - Spotify Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg-body: #000000;
            --bg-sidebar: #121212;
            --bg-card: #181818;
            --bg-card-hover: #282828;
            --accent-green: #1DB954;
            --text-main: #ffffff;
            --text-muted: #b3b3b3;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Outfit', sans-serif;
        }

        body {
            background-color: var(--bg-body);
            color: var(--text-main);
            display: flex;
            min-height: 100vh;
        }

        .main-content {
            margin-left: 240px;
            flex-grow: 1;
            padding: 32px;
            background: linear-gradient(to bottom, #222 0%, #000 300px);
        }

        .header {
            margin-bottom: 32px;
        }

        .header h1 {
            font-size: 32px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .log-container {
            background-color: rgba(24, 24, 24, 0.5);
            border-radius: 8px;
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        thead th {
            color: var(--text-muted);
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 1px;
            padding-bottom: 12px;
            border-bottom: 1px solid #333;
        }

        tbody tr {
            transition: background-color 0.2s;
        }

        tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        td {
            padding: 12px 0;
            vertical-align: middle;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            color: var(--text-muted);
            font-size: 14px;
        }

        .status-badge {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            display: inline-block;
        }

        .status-approve { background-color: rgba(29, 185, 84, 0.2); color: var(--accent-green); border: 1px solid rgba(29, 185, 84, 0.3); }
        .status-reject { background-color: rgba(233, 20, 41, 0.2); color: #e91429; border: 1px solid rgba(233, 20, 41, 0.3); }

        @media (max-width: 768px) {
            .main-content { margin-left: 80px; }
        }
    </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main-content">
    <div class="header">
        <h1>Nhật ký duyệt bài</h1>
    </div>

    <div class="log-container">
        <?php if (count($logs) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>BÀI HÁT</th>
                        <th>HÀNH ĐỘNG</th>
                        <th>NGƯỜI DUYỆT</th>
                        <th>THỜI GIAN</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td>#<?= $log['log_id'] ?></td>
                            <td style="color: white; font-weight: 500;"><?= htmlspecialchars($log['song_title']) ?></td>
                            <td>
                                <?php if ($log['action'] == 'APPROVE'): ?>
                                    <span class="status-badge status-approve">Đã duyệt</span>
                                <?php else: ?>
                                    <span class="status-badge status-reject">Từ chối</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($log['admin_name']) ?></td>
                            <td><?= date('H:i d/m/Y', strtotime($log['action_time'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div style="text-align: center; padding: 40px; color: var(--text-muted);">
                <p>Chưa có nhật ký nào.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>

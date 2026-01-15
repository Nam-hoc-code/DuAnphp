<?php
require_once __DIR__ . "/check_admin.php";
require_once __DIR__ . "/../config/database.php";

$db = new Database();
$conn = $db->connect();

$sql = "
SELECT s.song_id, s.title, u.username AS artist, s.created_at, s.cover_image
FROM songs s
JOIN users u ON s.artist_id = u.user_id
WHERE s.status = 'PENDING'
ORDER BY s.created_at DESC
";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Duyệt bài hát - Spotify Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg-body: #000000;
            --bg-sidebar: #121212;
            --bg-card: #181818;
            --bg-card-hover: #282828;
            --accent-green: #1DB954;
            --accent-cyan: #00DBFF;
            --text-main: #ffffff;
            --text-muted: #b3b3b3;
            --danger: #e91429;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Roboto', sans-serif;
        }

        body {
            background-color: var(--bg-body);
            color: var(--text-main);
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styling */
        .sidebar {
            width: 240px;
            background-color: var(--bg-sidebar);
            padding: 24px;
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
        }

        .logo {
            margin-bottom: 32px;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            color: white;
            font-size: 24px;
            font-weight: bold;
        }

        .logo svg {
            width: 40px;
            height: 40px;
            fill: var(--accent-green);
        }

        .nav-menu {
            list-style: none;
            flex-grow: 1;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 12px;
            text-decoration: none;
            color: var(--text-muted);
            font-weight: 500;
            border-radius: 4px;
            transition: all 0.2s;
            margin-bottom: 8px;
        }

        .nav-link:hover, .nav-link.active {
            color: white;
            background-color: var(--bg-card-hover);
        }

        .logout-btn {
            margin-top: auto;
            color: #ff5555;
        }

        /* Main Content */
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
            font-family: 'Times New Roman', Times, serif;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Song Table Styling */
        .song-list-container {
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
        }

        .song-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .song-img {
            width: 40px;
            height: 40px;
            border-radius: 4px;
            background-color: #333;
            object-fit: cover;
        }

        .song-details b {
            display: block;
            font-size: 16px;
        }

        .song-details span {
            font-size: 14px;
            color: var(--text-muted);
        }

        .actions {
            display: flex;
            gap: 10px;
        }

        .btn-action {
            padding: 6px 16px;
            border-radius: 20px;
            text-decoration: none;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            transition: transform 0.1s;
        }

        .btn-approve {
            background-color: var(--accent-green);
            color: black;
        }

        .btn-reject {
            border: 1px solid var(--text-muted);
            color: white;
        }

        .btn-action:hover {
            transform: scale(1.05);
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: var(--text-muted);
        }

        @media (max-width: 768px) {
            .sidebar { width: 80px; }
            .sidebar span { display: none; }
            .main-content { margin-left: 80px; }
        }
    </style>
</head>
<body>

<div class="sidebar">
    <a href="admin_view.php" class="logo">
        <svg viewBox="0 0 167.5 167.5">
            <path d="M83.7,0C37.5,0,0,37.5,0,83.7s37.5,83.7,83.7,83.7s83.7-37.5,83.7-83.7S130,0,83.7,0z M122.1,120.8 c-1.5,2.4-4.5,3.2-6.9,1.7c-19.1-11.7-43.2-14.3-71.5-7.8c-2.7,0.6-5.4-1-6.1-3.7c-0.6-2.7,1-5.4,3.7-6.1 c30.9-7,57.7-4.1,79.1,9C122.7,115.4,123.5,118.4,122.1,120.8z M132.3,98c-1.9,3-5.8,4-8.8,2.1c-21.9-13.5-55.3-17.4-81.2-9.5 c-3.3,1-6.8-0.8-7.9-4.1c-1-3.3,0.8-6.8,4.1-7.9c30-9.1,67-4.7,92,10.6C133.7,91.1,134.6,95,132.3,98z M133.3,74.5 c-26.2-15.6-69.5-17-94.7-9.4c-4,1.2-8.2-1.1-9.4-5.1c-1.2-4,1.1-8.2,5.1-9.4c30.1-9.1,78.1-7.4,109,10.9 c3.6,2.1,4.8,6.8,2.7,10.4C134,75.1,129.3,76.3,133.3,74.5z"/>
        </svg>
        <span>Spotify</span>
    </a>

    <ul class="nav-menu">
        <li class="nav-item">
            <a href="admin_view.php" class="nav-link">
                <i class="fas fa-home"></i>
                <span>Trang chủ</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="song_requests.php" class="nav-link active">
                <i class="fas fa-music"></i>
                <span>Duyệt bài hát</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="../auth/logout.php" class="nav-link logout-btn">
                <i class="fas fa-sign-out-alt"></i>
                <span>Đăng xuất</span>
            </a>
        </li>
    </ul>
</div>

<div class="main-content">
    <div class="header">
        <h1>Danh sách chờ duyệt</h1>
    </div>

    <div class="song-list-container">
        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th># TIÊU ĐỀ</th>
                        <th>NGÀY GỬI</th>
                        <th>HÀNH ĐỘNG</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <div class="song-info">
                                    <img src="<?= !empty($row['cover_image']) ? $row['cover_image'] : 'https://via.placeholder.com/40' ?>" class="song-img" alt="">
                                    <div class="song-details">
                                        <b><?= htmlspecialchars($row['title']) ?></b>
                                        <span><?= htmlspecialchars($row['artist']) ?></span>
                                    </div>
                                </div>
                            </td>
                            <td><?= date('d/m/Y', strtotime($row['created_at'])) ?></td>
                            <td>
                                <div class="actions">
                                    <a href="approve_song.php?id=<?= $row['song_id'] ?>" class="btn-action btn-approve">Duyệt</a>
                                    <a href="reject_song.php?id=<?= $row['song_id'] ?>" class="btn-action btn-reject">Từ chối</a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-check-circle" style="font-size: 48px; margin-bottom: 16px; color: var(--accent-green);"></i>
                <p>Không có bài hát nào đang chờ duyệt!</p>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>

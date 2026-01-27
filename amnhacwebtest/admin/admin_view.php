<?php 
require_once __DIR__ . "/check_admin.php";
require_once __DIR__ . "/../config/database.php";
require_once "dash_board.php";
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Spotify</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <!-- Font Awesome for icons -->
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

        /* Main Content Styling */
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
            margin-bottom: 8px;
            font-family: 'Times New Roman', Times, serif;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
            margin-bottom: 48px;
        }

        .stat-card {
            background-color: var(--bg-card);
            padding: 24px;
            border-radius: 8px;
            transition: background-color 0.3s;
            cursor: default;
            display: flex;
            align-items: center;
            gap: 20px;
            border: 1px solid #222;
        }

        .stat-card:hover {
            background-color: var(--bg-card-hover);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .icon-users { background-color: rgba(0, 219, 255, 0.1); color: var(--accent-cyan); }
        .icon-songs { background-color: rgba(29, 185, 84, 0.1); color: var(--accent-green); }
        .icon-pending { background-color: rgba(255, 165, 0, 0.1); color: #ffa500; }

        .stat-info h3 {
            font-size: 14px;
            color: var(--text-muted);
            text-transform: uppercase;
            margin-bottom: 4px;
            letter-spacing: 1px;
        }

        .stat-value {
            font-size: 28px;
            font-weight: bold;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 80px;
            }
        }
    </style>
</head>
<body>


<?php include 'sidebar.php'; ?>

<div class="main-content">
    <div class="header">
        <h1>Admin Dashboard</h1>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon icon-users">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <h3>Tổng người dùng</h3>
                <div class="stat-value"><?= $totalUsers ?></div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon icon-songs">
                <i class="fas fa-music"></i>
            </div>
            <div class="stat-info">
                <h3>Tổng bài hát</h3>
                <div class="stat-value"><?= $totalSongs ?></div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon icon-pending">
                <i class="fas fa-hourglass-half"></i>
            </div>
            <div class="stat-info">
                <h3>Bài chờ duyệt</h3>
                <div class="stat-value"><?= $pendingSongs ?></div>
            </div>
        </div>
    </div>
</div>

</body>
</html>

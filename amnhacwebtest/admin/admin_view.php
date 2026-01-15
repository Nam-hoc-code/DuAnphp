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

        .nav-item {
            margin-bottom: 8px;
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
        }

        .nav-link:hover, .nav-link.active {
            color: white;
            background-color: var(--bg-card-hover);
        }

        .nav-link i {
            font-size: 20px;
            width: 24px;
            text-align: center;
        }

        .logout-btn {
            margin-top: auto;
            color: #ff5555;
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

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 80px;
                padding: 20px 10px;
            }
            .logo span, .nav-link span {
                display: none;
            }
            .main-content {
                margin-left: 80px;
            }
        }
    </style>
</head>
<body>


<div class="sidebar">
    <a href="#" class="logo">
        <svg viewBox="0 0 167.5 167.5">
            <path d="M83.7,0C37.5,0,0,37.5,0,83.7s37.5,83.7,83.7,83.7s83.7-37.5,83.7-83.7S130,0,83.7,0z M122.1,120.8 c-1.5,2.4-4.5,3.2-6.9,1.7c-19.1-11.7-43.2-14.3-71.5-7.8c-2.7,0.6-5.4-1-6.1-3.7c-0.6-2.7,1-5.4,3.7-6.1 c30.9-7,57.7-4.1,79.1,9C122.7,115.4,123.5,118.4,122.1,120.8z M132.3,98c-1.9,3-5.8,4-8.8,2.1c-21.9-13.5-55.3-17.4-81.2-9.5 c-3.3,1-6.8-0.8-7.9-4.1c-1-3.3,0.8-6.8,4.1-7.9c30-9.1,67-4.7,92,10.6C133.7,91.1,134.6,95,132.3,98z M133.3,74.5 c-26.2-15.6-69.5-17-94.7-9.4c-4,1.2-8.2-1.1-9.4-5.1c-1.2-4,1.1-8.2,5.1-9.4c30.1-9.1,78.1-7.4,109,10.9 c3.6,2.1,4.8,6.8,2.7,10.4C134,75.1,129.3,76.3,133.3,74.5z"/>
        </svg>
        <span>Spotify</span>
    </a>

    <ul class="nav-menu">
        <li class="nav-item">
            <a href="admin_view.php" class="nav-link active">
                <i class="fas fa-home"></i>
                <span>Trang chủ</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="song_requests.php" class="nav-link">
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


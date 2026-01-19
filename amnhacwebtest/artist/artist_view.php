<?php
require_once __DIR__ . "/check_artist.php";
require_once __DIR__ . "/../config/database.php"; 
require_once "dash_board.php"; 
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <meta charset="UTF-8">
    <title>Artist Dashboard - Spotify Style</title>
    <style>
        :root { 
            --bg-black: #000000; 
            --sidebar-bg: #121212; 
            --card-bg: #181818; 
            --text-main: #ffffff; 
            --text-sub: #b3b3b3; 
            --spotify-green: #1DB954; 
            --nav-hover: #282828;
            --logout-red: #f15555;
        }

        body { 
            font-family: 'Segoe UI', Roboto, sans-serif; 
            margin: 0; 
            display: flex; 
            background-color: var(--bg-black); 
            color: var(--text-main); 
        }

        /* Sidebar Giao diện giống ảnh */
        .sidebar { 
            width: 260px; 
            height: 100vh; 
            background: var(--sidebar-bg); 
            position: fixed; 
            padding: 24px 12px; 
            display: flex;
            flex-direction: column;
            box-sizing: border-box;
        }

        .logo-container {
            display: flex;
            align-items: center;
            padding: 0 12px 30px 12px;
            gap: 8px;
        }
        
        .logo-container span { font-size: 1.5rem; font-weight: bold; letter-spacing: -1px; }

        .nav-group { flex-grow: 1; }

        .nav-link { 
            display: flex; 
            align-items: center; 
            color: var(--text-sub); 
            text-decoration: none; 
            padding: 12px 16px; 
            border-radius: 4px; 
            font-weight: bold; 
            font-size: 14px;
            transition: 0.2s;
            margin-bottom: 4px;
        }

        .nav-link:hover { color: #fff; background-color: var(--nav-hover); }
        .nav-link.active { background-color: var(--nav-hover); color: #fff; }
        
        .nav-link i { margin-right: 16px; font-size: 20px; font-style: normal; }

        .nav-link.logout { color: var(--logout-red); margin-top: auto; }
        .nav-link.logout:hover { background-color: rgba(241, 85, 85, 0.1); }

        /* Nội dung chính */
        .main { margin-left: 260px; padding: 32px; width: 100%; box-sizing: border-box; }
        
        h1 { font-size: 2rem; margin-bottom: 30px; }

        .stats-container { display: flex; gap: 24px; margin-bottom: 40px; }
        .card { 
            background: var(--card-bg); 
            padding: 24px; 
            border-radius: 8px; 
            flex: 1; 
            transition: background 0.3s;
            cursor: pointer;
        }
        .card:hover { background: #282828; }
        .card h3 { margin: 0; color: var(--text-sub); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; }
        .card p { font-size: 2.5rem; font-weight: bold; margin: 12px 0 0; color: var(--spotify-green); }

        .action-banner {
            background: linear-gradient(to bottom, #282828, #181818);
            padding: 40px;
            border-radius: 8px;
            border: 1px solid #333;
        }

        .btn-add { 
            background: var(--spotify-green); 
            color: black; 
            padding: 14px 32px; 
            border-radius: 500px; 
            text-decoration: none; 
            font-weight: bold; 
            display: inline-block; 
            margin-top: 20px;
            text-transform: uppercase;
            font-size: 14px;
            transition: transform 0.2s;
        }
        .btn-add:hover { transform: scale(1.04); background-color: #1ed760; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="logo-container">
        <svg width="40" height="40" viewBox="0 0 167.5 167.5" fill="#1DB954">
            <path d="M83.7 0C37.5 0 0 37.5 0 83.7c0 46.3 37.5 83.7 83.7 83.7 46.3 0 83.7-37.5 83.7-83.7C167.5 37.5 130 0 83.7 0zm38.4 120.7c-1.5 2.5-4.8 3.3-7.3 1.8-19.1-11.7-43.2-14.3-71.5-7.8-2.9.7-5.7-1.1-6.4-4-.7-2.9 1.1-5.7 4-6.4 31.1-7.1 57.8-4.1 79.4 9.1 2.5 1.5 3.3 4.8 1.8 7.3zm10.2-22.8c-1.9 3.1-5.9 4.1-9 2.2-21.9-13.5-55.2-17.4-81.1-9.5-3.5 1.1-7.1-1-8.2-4.5-1.1-3.5 1-7.1 4.5-8.2 29.5-8.9 66.3-4.6 91.5 10.8 3.2 2 4.1 6.1 2.3 9.2zm.9-23.9C105.3 57.5 61.2 56 35.8 63.7c-4.3 1.3-8.8-1.2-10.1-5.5-1.3-4.3 1.2-8.8 5.5-10.1 30.1-9.1 79-7.4 109.2 10.5 3.9 2.3 5.2 7.3 2.9 11.2s-7.2 5.2-11.1 2.9z"/>
        </svg>
        <span>Spotify</span>
    </div>

    <div class="nav-group">
        <a href="artist_view.php" class="nav-link active"><i class="fa-solid fa-house"></i> Trang chủ</a>
        <a href="my_songs.php" class="nav-link"><i class="fa-solid fa-music"></i> Duyệt bài hát</a>
        <a href="add_song.php" class="nav-link"><i class="fa-solid fa-circle-plus"></i> Thêm bài mới</a>
        <a href="oders.php" class="nav-link">Đơn hàng</a>
        <a href="../auth/logout.php" class="nav-link logout"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a>
    </div>
</div>

<div class="main">
    <h1>Chào mừng nghệ sĩ quay trở lại</h1>
    
    <div class="stats-container">
        <div class="card">
            <h3>Tổng bài hát</h3>
            <p><?= $totalSongs ?? 0 ?></p>
        </div>
        <div class="card">
            <h3>Đang chờ duyệt</h3>
            <p><?= $pendingSongs ?? 0 ?></p>
        </div>
    </div>

    <div class="action-banner">
        <h2>Bạn có bản nhạc mới?</h2>
        <p style="color: var(--text-sub);">Chia sẻ tác phẩm của bạn với thế giới ngay hôm nay.</p>
        <a href="add_song.php" class="btn-add">Upload bài hát ngay</a>
    </div>
</div>

</body>
</html>

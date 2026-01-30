<?php require_once "check_artist.php"; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <meta charset="UTF-8">
    <title>Thêm bài hát - Artist Dashboard</title>
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
            --input-bg: #282828;
        }

        body { 
            font-family: 'Segoe UI', Roboto, sans-serif; 
            margin: 0; 
            display: flex; 
            background-color: var(--bg-black); 
            color: var(--text-main); 
        }

        /* Sidebar */
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
        
        .nav-link i { margin-right: 16px; font-size: 20px; font-style: normal; width: 20px; text-align: center; }

        .nav-link.logout { color: var(--logout-red); margin-top: auto; }
        .nav-link.logout:hover { background-color: rgba(241, 85, 85, 0.1); }

        /* Content Area */
        .main { margin-left: 260px; padding: 32px; width: calc(100% - 260px); box-sizing: border-box; display: flex; flex-direction: column; align-items: center; }
        
        .form-container {
            background: var(--card-bg);
            padding: 40px;
            border-radius: 8px;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.5);
        }

        h1 { font-size: 2rem; margin-bottom: 30px; align-self: flex-start; }
        h2 { margin-top: 0; margin-bottom: 24px; text-align: center; font-size: 1.5rem; }

        label { display: block; margin-bottom: 8px; color: var(--text-sub); font-size: 0.75rem; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }

        input[type="text"], input[type="file"] { 
            width: 100%; 
            padding: 14px; 
            margin-bottom: 24px; 
            background: var(--input-bg); 
            border: 1px solid transparent; 
            border-radius: 4px; 
            color: white; 
            box-sizing: border-box;
            transition: 0.3s;
        }

        input[type="text"]:focus { border-color: var(--spotify-green); outline: none; background: #333; }

        input[type="file"] {
            padding: 10px;
            font-size: 0.85rem;
        }

        .btn-submit { 
            width: 100%; 
            background: var(--spotify-green); 
            color: black; 
            border: none; 
            padding: 16px; 
            border-radius: 500px; 
            font-weight: bold; 
            font-size: 1rem;
            cursor: pointer; 
            transition: transform 0.2s, background 0.2s;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-submit:hover { transform: scale(1.02); background: #1ed760; }
        
        .back-link {
            margin-top: 24px;
            text-align: center;
        }
        .back-link a {
            color: var(--text-sub);
            text-decoration: none;
            font-size: 0.85rem;
            transition: 0.2s;
        }
        .back-link a:hover { color: #fff; }
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
        <a href="artist_view.php" class="nav-link"><i class="fa-solid fa-house"></i> Trang chủ</a>
        <a href="my_songs.php" class="nav-link"><i class="fa-solid fa-music"></i> Bài hát</a>
        <a href="add_song.php" class="nav-link active"><i class="fa-solid fa-circle-plus"></i> Thêm bài mới</a>
        <a href="oders.php" class="nav-link"><i class="fa-solid fa-cart-shopping"></i> Đơn hàng</a>
        <a href="../auth/logout.php" class="nav-link logout"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a>
    </div>
</div>

<div class="main">
    <h1>Chia sẻ âm nhạc của bạn</h1>
    
    <div class="form-container">
        <h2>Thêm bài hát mới</h2>
        <form action="add_song_process.php" method="POST" enctype="multipart/form-data">
            <label>Tên bài hát</label>
            <input type="text" name="title" required placeholder="Ví dụ: Lạc Trôi">
            
            <label>Tập tin nhạc (MP3)</label>
            <input type="file" name="audio" accept=".mp3" required>
            
            <label>Ảnh bìa (Cover)</label>
            <input type="file" name="cover" accept="image/*" required>
            
            <button type="submit" class="btn-submit">Bắt đầu Upload</button>
        </form>
        <div class="back-link">
            <a href="artist_view.php"><i class="fa-solid fa-arrow-left"></i> Quay lại Dashboard</a>
        </div>
    </div>
</div>

</body>
</html>

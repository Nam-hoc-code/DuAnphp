<?php
require_once "check_artist.php";
require_once "../config/database.php";

$db = new Database();
$conn = $db->connect();
$artist_id = $_SESSION['user']['id'];

$sql = "SELECT * FROM songs WHERE artist_id = ? AND is_deleted = 0 ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $artist_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <meta charset="UTF-8">
    <title>Bài hát của tôi - Artist Dashboard</title>
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
            --table-border: #282828;
        }

        body { 
            font-family: 'Outfit', sans-serif; 
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
            gap: 12px;
        }
        
        .logo-container span { font-size: 1.5rem; font-weight: 700; letter-spacing: -1px; }

        .nav-group { flex-grow: 1; }

        .nav-link { 
            display: flex; 
            align-items: center; 
            color: var(--text-sub); 
            text-decoration: none; 
            padding: 12px 16px; 
            border-radius: 6px; 
            font-weight: 600; 
            font-size: 14px;
            transition: 0.2s;
            margin-bottom: 4px;
        }

        .nav-link:hover { color: #fff; background-color: var(--nav-hover); }
        .nav-link.active { background-color: var(--nav-hover); color: #fff; }
        
        .nav-link i { margin-right: 16px; font-size: 20px; width: 20px; text-align: center; }

        .nav-link.logout { color: var(--logout-red); margin-top: auto; }
        .nav-link.logout:hover { background-color: rgba(241, 85, 85, 0.1); }

        /* Content Area */
        .main { margin-left: 260px; padding: 40px; width: calc(100% - 260px); box-sizing: border-box; }
        
        h1 { font-size: 2.2rem; font-weight: 700; margin-bottom: 32px; letter-spacing: -1px; }

        .table-container {
            background: var(--card-bg);
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.5);
            border: 1px solid #222;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            padding: 12px 16px;
            color: var(--text-sub);
            font-size: 0.8rem;
            text-transform: uppercase;
            border-bottom: 1px solid var(--table-border);
            letter-spacing: 1.5px;
            font-weight: 700;
        }

        td {
            padding: 16px;
            border-bottom: 1px solid var(--table-border);
            vertical-align: middle;
            font-size: 14px;
        }

        tr:hover td { background-color: rgba(255, 255, 255, 0.03); }

        .cover-img { width: 48px; height: 48px; border-radius: 4px; object-fit: cover; box-shadow: 0 4px 8px rgba(0,0,0,0.3); }
        
        .status-badge { 
            padding: 6px 14px; 
            border-radius: 50px; 
            font-size: 11px; 
            font-weight: 700; 
            background: #333; 
            text-transform: uppercase;
        }
        .status-pending { color: #f1c40f; background: rgba(241, 196, 15, 0.1); }
        .status-approved { color: var(--spotify-green); background: rgba(29, 185, 84, 0.1); }

        .btn-del { color: var(--logout-red); text-decoration: none; font-size: 0.9rem; font-weight: 600; }
        .btn-del:hover { text-decoration: underline; }
        
        .btn-send { color: var(--spotify-green); text-decoration: none; font-size: 0.9rem; font-weight: 600; }
        .btn-send:hover { text-decoration: underline; }

        audio { height: 32px; filter: invert(100%) hue-rotate(180deg) brightness(1.5); opacity: 0.8; width: 220px; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="logo-container">
        <svg width="32" height="32" viewBox="0 0 167.5 167.5" fill="#1DB954">
            <path d="M83.7 0C37.5 0 0 37.5 0 83.7c0 46.3 37.5 83.7 83.7 83.7 46.3 0 83.7-37.5 83.7-83.7C167.5 37.5 130 0 83.7 0zm38.4 120.7c-1.5 2.5-4.8 3.3-7.3 1.8-19.1-11.7-43.2-14.3-71.5-7.8-2.9.7-5.7-1.1-6.4-4-.7-2.9 1.1-5.7 4-6.4 31.1-7.1 57.8-4.1 79.4 9.1 2.5 1.5 3.3 4.8 1.8 7.3zm10.2-22.8c-1.9 3.1-5.9 4.1-9 2.2-21.9-13.5-55.2-17.4-81.1-9.5-3.5 1.1-7.1-1-8.2-4.5-1.1-3.5 1-7.1 4.5-8.2 29.5-8.9 66.3-4.6 91.5 10.8 3.2 2 4.1 6.1 2.3 9.2zm.9-23.9C105.3 57.5 61.2 56 35.8 63.7c-4.3 1.3-8.8-1.2-10.1-5.5-1.3-4.3 1.2-8.8 5.5-10.1 30.1-9.1 79-7.4 109.2 10.5 3.9 2.3 5.2 7.3 2.9 11.2s-7.2 5.2-11.1 2.9z"/>
        </svg>
        <span>Spotify</span>
    </div>

    <div class="nav-group">
        <a href="artist_view.php" class="nav-link"><i class="fa-solid fa-house"></i> Trang chủ</a>
        <a href="my_songs.php" class="nav-link active"><i class="fa-solid fa-music"></i> Duyệt bài hát</a>
        <a href="add_song.php" class="nav-link"><i class="fa-solid fa-circle-plus"></i> Thêm bài mới</a>
        <a href="oders.php" class="nav-link"><i class="fa-solid fa-cart-shopping"></i> Đơn hàng</a>
        <a href="../auth/logout.php" class="nav-link logout"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a>
    </div>
</div>

<div class="main">
    <h1>Quản lý bài hát của bạn</h1>
    
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Bìa</th>
                    <th>Tiêu đề</th>
                    <th>Nghe thử</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><img src="<?= $row['cover_image'] ?>" class="cover-img"></td>
                    <td style="font-weight: 700; font-size: 15px;"><?= htmlspecialchars($row['title']) ?></td>
                    <td><audio controls src="<?= $row['cloud_url'] ?>"></audio></td>
                    <td>
                        <span class="status-badge <?= $row['status'] === 'PENDING' ? 'status-pending' : 'status-approved' ?>">
                            <?= $row['status'] ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($row['status'] === 'PENDING'): ?>
                            <a href="send_request.php?id=<?= $row['song_id'] ?>" class="btn-send"><i class="fa-solid fa-paper-plane"></i> Gửi duyệt</a>
                            <span style="color: #333; margin: 0 8px;">|</span>
                        <?php endif; ?>
                        <a href="delete_song.php?id=<?= $row['song_id'] ?>" class="btn-del" onclick="return confirm('Xóa bài hát?')"><i class="fa-solid fa-trash"></i> Xóa</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>

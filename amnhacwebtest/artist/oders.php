<?php
require_once "check_artist.php";
require_once "../config/database.php";

if (!isset($_SESSION['user']['id'])) {
    die("Chưa đăng nhập");
}

$artist_id = (int) $_SESSION['user']['id'];

$db = new Database();
$conn = $db->connect();

/*
 Lấy các đơn hàng mà bài hát thuộc về nghệ sĩ này
*/
$sql = "
SELECT 
    o.order_id,
    u.username AS buyer,
    s.title AS song_title,
    d.price,
    o.created_at
FROM disc_orders o
JOIN discs d ON o.disc_id = d.disc_id
JOIN songs s ON d.song_id = s.song_id
JOIN users u ON o.user_id = u.user_id
WHERE s.artist_id = ?
ORDER BY o.created_at DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $artist_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <meta charset="UTF-8">
    <title>Đơn hàng - Artist Dashboard</title>
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
        .main { margin-left: 260px; padding: 32px; width: calc(100% - 260px); box-sizing: border-box; }
        
        h1 { font-size: 2rem; margin-bottom: 30px; }

        .order-container {
            background: var(--card-bg);
            border-radius: 8px;
            padding: 24px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.5);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th {
            text-align: left;
            padding: 12px 16px;
            color: var(--text-sub);
            font-size: 0.85rem;
            text-transform: uppercase;
            border-bottom: 1px solid var(--table-border);
            letter-spacing: 1px;
        }

        td {
            padding: 16px;
            border-bottom: 1px solid var(--table-border);
            color: var(--text-main);
            font-size: 0.95rem;
        }

        tr:hover td {
            background-color: var(--nav-hover);
        }

        .order-id { color: var(--spotify-green); font-weight: bold; }
        .price { font-weight: bold; }
        .date { color: var(--text-sub); font-size: 0.85rem; }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: var(--text-sub);
        }
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
        <a href="my_songs.php" class="nav-link"><i class="fa-solid fa-music"></i> Duyệt bài hát</a>
        <a href="add_song.php" class="nav-link"><i class="fa-solid fa-circle-plus"></i> Thêm bài mới</a>
        <a href="oders.php" class="nav-link active"><i class="fa-solid fa-cart-shopping"></i> Đơn hàng</a>
        <a href="../auth/logout.php" class="nav-link logout"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a>
    </div>
</div>

<div class="main">
    <h1>Lịch sử đơn hàng đĩa</h1>
    
    <div class="order-container">
        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Sản phẩm (Bài hát)</th>
                        <th>Người mua</th>
                        <th>Giá tiền</th>
                        <th>Ngày tạo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="order-id">#<?= $row['order_id'] ?></td>
                            <td><?= htmlspecialchars($row['song_title'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($row['buyer'] ?? 'N/A') ?></td>
                            <td class="price"><?= number_format($row['price'], 0, ',', '.') ?> VNĐ</td>
                            <td class="date"><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <i class="fa-solid fa-box-open" style="font-size: 3rem; margin-bottom: 20px;"></i>
                <p>Bạn chưa có đơn hàng nào.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>

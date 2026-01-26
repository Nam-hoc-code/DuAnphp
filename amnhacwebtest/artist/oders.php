<?php
require_once "check_artist.php";
require_once "../config/database.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user']['id'])) {
    die("Chưa đăng nhập");
}

$artist_id = (int) $_SESSION['user']['id'];

$db = new Database();
$conn = $db->connect();

/* =========================
   1️⃣ ĐƠN HÀNG ĐĨA
========================= */
$sql_orders = "
    SELECT 
        o.order_id,
        u.username AS buyer,
        s.title AS disc_name,
        d.price,
        o.status,
        o.created_at
    FROM disc_orders o
    JOIN discs d ON o.disc_id = d.disc_id
    JOIN songs s ON d.song_id = s.song_id
    JOIN users u ON o.user_id = u.user_id
    WHERE s.artist_id = ?
    ORDER BY o.created_at DESC
";
$stmt = $conn->prepare($sql_orders);
$stmt->bind_param("i", $artist_id);
$stmt->execute();
$result_orders = $stmt->get_result();

/* =========================
   2️⃣ SONGS (FORM THÊM ĐĨA)
========================= */
$sql_songs = "
    SELECT song_id, title
    FROM songs
    WHERE artist_id = ? AND is_deleted = 0
";
$stmt_songs = $conn->prepare($sql_songs);
$stmt_songs->bind_param("i", $artist_id);
$stmt_songs->execute();
$result_songs = $stmt_songs->get_result();

/* =========================
   3️⃣ DISCS HIỆN CÓ
========================= */
$sql_discs = "
    SELECT 
        d.disc_id,
        s.title AS song_title,
        d.price,
        (
            SELECT COUNT(*) 
            FROM disc_orders o 
            WHERE o.disc_id = d.disc_id
        ) AS order_count
    FROM discs d
    JOIN songs s ON d.song_id = s.song_id
    WHERE s.artist_id = ?
    ORDER BY d.disc_id DESC
";
$stmt_discs = $conn->prepare($sql_discs);
$stmt_discs->bind_param("i", $artist_id);
$stmt_discs->execute();
$result_discs = $stmt_discs->get_result();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý bán đĩa - Artist Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
            --glass: rgba(255, 255, 255, 0.05);
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
            z-index: 1000;
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
            transition: all 0.3s ease;
            margin-bottom: 4px;
        }

        .nav-link:hover { color: #fff; background-color: var(--nav-hover); }
        .nav-link.active { background-color: var(--nav-hover); color: var(--spotify-green); }
        
        .nav-link i { margin-right: 16px; font-size: 18px; width: 20px; text-align: center; }

        .nav-link.logout { color: var(--logout-red); margin-top: auto; }
        .nav-link.logout:hover { background-color: rgba(241, 85, 85, 0.1); }

        /* Main Content */
        .main { 
            margin-left: 260px; 
            padding: 40px; 
            width: calc(100% - 260px); 
            box-sizing: border-box; 
            min-height: 100vh;
        }
        
        h1 { font-size: 2.2rem; font-weight: 700; margin-bottom: 30px; letter-spacing: -1px; }
        h2 { font-size: 1.4rem; font-weight: 600; margin: 40px 0 20px; color: var(--text-main); display: flex; align-items: center; gap: 10px; }

        .content-card {
            background: var(--card-bg);
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.5);
            margin-bottom: 30px;
            border: 1px solid #222;
        }

        /* Table Styling */
        table { width: 100%; border-collapse: collapse; }
        th { 
            text-align: left; 
            padding: 12px 16px; 
            color: var(--text-sub); 
            font-size: 12px; 
            text-transform: uppercase; 
            border-bottom: 1px solid var(--table-border);
            letter-spacing: 1px;
        }
        td { 
            padding: 16px; 
            border-bottom: 1px solid var(--table-border); 
            font-size: 14px;
            vertical-align: middle;
        }
        tr:hover td { background-color: rgba(255, 255, 255, 0.03); }

        /* Badge Styling */
        .status-badge {
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            display: inline-block;
        }
        .status-pending { background: rgba(255, 193, 7, 0.1); color: #ffc107; }
        .status-confirmed { background: rgba(0, 170, 255, 0.1); color: #00aaff; }
        .status-shipping { background: rgba(255, 87, 34, 0.1); color: #ff5722; }
        .status-done { background: rgba(29, 185, 84, 0.1); color: var(--spotify-green); }

        /* Buttons */
        .btn { 
            background: var(--spotify-green); 
            color: #000; 
            border: none; 
            padding: 8px 18px; 
            border-radius: 50px; 
            font-weight: 700; 
            font-size: 13px;
            cursor: pointer; 
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn:hover { transform: scale(1.05); background: #1ed760; }
        .btn-danger { background: rgba(241, 85, 85, 0.1); color: var(--logout-red); border: 1px solid rgba(241, 85, 85, 0.2); }
        .btn-danger:hover { background: var(--logout-red); color: #fff; }

        /* Form Styling */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            align-items: flex-end;
        }
        .form-group { display: flex; flex-direction: column; gap: 8px; }
        .form-group label { font-size: 13px; font-weight: 600; color: var(--text-sub); }
        
        select, input {
            background: #282828;
            border: 1px solid transparent;
            color: #fff;
            padding: 12px 16px;
            border-radius: 6px;
            font-family: 'Outfit', sans-serif;
            font-size: 14px;
            transition: border 0.3s;
        }
        select:focus, input:focus { outline: none; border-color: var(--spotify-green); background: #333; }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: var(--text-sub);
            font-style: italic;
        }

        hr { border: 0; border-top: 1px solid #222; margin: 40px 0; }
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
        <a href="my_songs.php" class="nav-link"><i class="fa-solid fa-music"></i> Duyệt bài hát</a>
        <a href="add_song.php" class="nav-link"><i class="fa-solid fa-circle-plus"></i> Thêm bài mới</a>
        <a href="oders.php" class="nav-link active"><i class="fa-solid fa-cart-shopping"></i> Đơn hàng</a>
        <a href="../auth/logout.php" class="nav-link logout"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a>
    </div>
</div>

<div class="main">
    <h1>🎧 Dashboard Quản Lý Bán Đĩa</h1>

    <!-- =======================
         ĐƠN HÀNG
    ======================= -->
    <h2><i class="fa-solid fa-receipt"></i> Đơn hàng đĩa của tôi</h2>
    
    <div class="content-card">
        <?php if ($result_orders->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Mã đơn</th>
                    <th>Tên đĩa</th>
                    <th>Người mua</th>
                    <th>Giá</th>
                    <th>Trạng thái</th>
                    <th>Thời gian</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $result_orders->fetch_assoc()): ?>
                <tr>
                    <td style="font-weight: 600; color: var(--spotify-green);">#<?= $row['order_id'] ?></td>
                    <td style="font-weight: 600;"><?= htmlspecialchars($row['disc_name']) ?></td>
                    <td><?= htmlspecialchars($row['buyer']) ?></td>
                    <td style="font-weight: 600;"><?= number_format($row['price']) ?> VNĐ</td>
                    <td>
                        <span class="status-badge status-<?= $row['status'] ?>">
                            <i class="fa-solid fa-circle" style="font-size: 8px; margin-right: 5px;"></i>
                            <?= $row['status'] ?>
                        </span>
                    </td>
                    <td style="color: var(--text-sub); font-size: 13px;"><?= date('H:i d/m/Y', strtotime($row['created_at'])) ?></td>
                    <td>
                        <?php if ($row['status'] === 'pending'): ?>
                            <form method="POST" action="update_order_status.php">
                                <input type="hidden" name="order_id" value="<?= $row['order_id'] ?>">
                                <input type="hidden" name="status" value="confirmed">
                                <button class="btn"><i class="fa-solid fa-check"></i> Xác nhận</button>
                            </form>
                        <?php elseif ($row['status'] === 'confirmed'): ?>
                            <form method="POST" action="update_order_status.php">
                                <input type="hidden" name="order_id" value="<?= $row['order_id'] ?>">
                                <input type="hidden" name="status" value="shipping">
                                <button class="btn" style="background: #00aaff; color: #fff;"><i class="fa-solid fa-truck"></i> Giao hàng</button>
                            </form>
                        <?php elseif ($row['status'] === 'shipping'): ?>
                            <form method="POST" action="update_order_status.php">
                                <input type="hidden" name="order_id" value="<?= $row['order_id'] ?>">
                                <input type="hidden" name="status" value="done">
                                <button class="btn"><i class="fa-solid fa-circle-check"></i> Hoàn tất</button>
                            </form>
                        <?php else: ?>
                            <span style="color: var(--text-sub);"><i class="fa-solid fa-lock"></i> Đã hoàn thành</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="empty-state">
            <i class="fa-solid fa-folder-open" style="font-size: 40px; margin-bottom: 20px; display: block; opacity: 0.5;"></i>
            Chưa có đơn hàng nào được đặt.
        </div>
        <?php endif; ?>
    </div>

    <!-- =======================
         THÊM ĐĨA
    ======================= -->
    <h2><i class="fa-solid fa-circle-plus"></i> Thêm đĩa mới</h2>
    
    <div class="content-card">
        <form action="add_disc_process.php" method="POST" class="form-grid">
            <div class="form-group">
                <label>Chọn bài hát:</label>
                <select name="song_id" required>
                    <option value="" disabled selected>-- Chọn bài hát --</option>
                    <?php while ($song = $result_songs->fetch_assoc()): ?>
                        <option value="<?= $song['song_id'] ?>">
                            <?= htmlspecialchars($song['title']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Giá bán (VNĐ):</label>
                <input type="number" name="price" min="1000" placeholder="VD: 50000" required>
            </div>

            <div class="form-group">
                <button class="btn" style="height: 45px; padding: 0 30px; justify-content: center;">
                    <i class="fa-solid fa-plus"></i> Tạo đĩa ngay
                </button>
            </div>
        </form>
    </div>

    <!-- =======================
         ĐĨA HIỆN CÓ
    ======================= -->
    <h2><i class="fa-solid fa-compact-disc"></i> Đĩa hiện có</h2>
    
    <div class="content-card">
        <?php if ($result_discs->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Bài hát</th>
                    <th>Giá niêm yết</th>
                    <th>Số lượt đặt</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $result_discs->fetch_assoc()): ?>
                <tr>
                    <td style="font-weight: 600; font-size: 15px;">
                        <i class="fa-solid fa-music" style="margin-right: 10px; color: var(--spotify-green);"></i>
                        <?= htmlspecialchars($row['song_title']) ?>
                    </td>
                    <td style="font-weight: 600;"><?= number_format($row['price']) ?> VNĐ</td>
                    <td>
                        <span style="color: var(--text-sub);"><?= $row['order_count'] ?> đơn hàng</span>
                    </td>
                    <td>
                        <?php if ($row['order_count'] > 0): ?>
                            <span class="status-badge" style="background: rgba(255,255,255,0.05); color: var(--text-sub);">
                                <i class="fa-solid fa-lock"></i> Đang kinh doanh
                            </span>
                        <?php else: ?>
                            <span class="status-badge" style="background: rgba(29, 185, 84, 0.1); color: var(--spotify-green);">
                                <i class="fa-solid fa-globe"></i> Đang hiển thị
                            </span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($row['order_count'] == 0): ?>
                            <form action="delete_disc.php" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa đĩa này?')">
                                <input type="hidden" name="disc_id" value="<?= $row['disc_id'] ?>">
                                <button class="btn btn-danger"><i class="fa-solid fa-trash-can"></i> Xóa</button>
                            </form>
                        <?php else: ?>
                            <span style="color: var(--text-sub); font-size: 12px;">Không thể xóa khi có đơn hàng</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="empty-state">
            Bạn chưa tạo đĩa nào. Hãy thêm đĩa đầu tiên phía trên!
        </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>

<?php 
// Kiểm tra quyền admin trước khi truy cập trang
require_once __DIR__ . "/check_admin.php";
// Kết nối cơ sở dữ liệu
require_once __DIR__ . "/../config/database.php";
// Lấy dữ liệu thống kê cho Dashboard (tổng số user, bài hát,...)
require_once "dash_board.php";
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Spotify</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            /* Định nghĩa bảng màu cho toàn bộ trang Admin */
            --bg-body: #000000;      /* Màu nền chính */
            --bg-sidebar: #121212;   /* Màu nền thanh menu bên trái */
            --bg-card: #181818;      /* Màu nền của các thẻ thống kê */
            --bg-card-hover: #282828; /* Màu khi di chuột qua thẻ */
            --accent-green: #1DB954; /* Màu xanh Spotify */
            --accent-cyan: #00DBFF;  /* Màu xanh cyan cho icon */
            --text-main: #ffffff;    /* Màu chữ trắng chính */
            --text-muted: #b3b3b3;   /* Màu chữ xám mô tả */
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box; /* Giúp tính toán kích thước phần tử chính xác hơn */
            font-family: 'Outfit', sans-serif;
        }

        body {
            background-color: var(--bg-body);
            color: var(--text-main);
            display: flex; /* Sử dụng flexbox để căn chỉnh sidebar và nội dung */
            min-height: 100vh;
        }

        /* Nội dung chính của trang (bên phải sidebar) */
        .main-content {
            margin-left: 240px; /* Chừa khoảng trống cho sidebar cố định */
            flex-grow: 1;
            padding: 32px;
            background: linear-gradient(to bottom, #222 0%, #000 300px); /* Hiệu ứng đổ màu nền */
        }

        .header {
            margin-bottom: 32px; /* Khoảng cách dưới tiêu đề */
        }

        .header h1 {
            font-size: 32px;
            margin-bottom: 8px;
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            text-transform: uppercase; /* Viết hoa toàn bộ tiêu đề */
            letter-spacing: 1px; /* Khoảng cách giữa các chữ cái */
        }

        /* Lưới hiển thị các thẻ thống kê */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); /* Tự động điều chỉnh số cột */
            gap: 24px; /* Khoảng cách giữa các thẻ */
            margin-bottom: 48px;
        }

        .stat-card {
            background-color: var(--bg-card);
            padding: 24px;
            border-radius: 8px;
            transition: background-color 0.3s; /* Hiệu ứng chuyển màu mượt mà */
            cursor: default;
            display: flex;
            align-items: center;
            gap: 20px;
            border: 1px solid #222;
        }

        .stat-card:hover {
            background-color: var(--bg-card-hover); /* Đổi màu nền khi di chuột vào */
        }

        /* Hình khối tròn chứa icon */
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        /* Màu sắc riêng cho từng loại icon */
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

        /* Xử lý giao diện trên màn hình nhỏ (Mobile/Tablet) */
        @media (max-width: 768px) {
            .main-content {
                margin-left: 80px; /* Thu hẹp lề trái khi sidebar thu nhỏ */
            }
        }
    </style>
</head>
<body>


<!-- Nhúng thanh Sidebar dùng chung -->
<?php include 'sidebar.php'; ?>

<div class="main-content">
    <div class="header">
        <h1>Admin Dashboard</h1>
    </div>

    <!-- Lưới hiển thị các thẻ thống kê -->
    <div class="stats-grid">
        <!-- Thẻ thống kê Người dùng -->
        <div class="stat-card">
            <div class="stat-icon icon-users">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <h3>Tổng người dùng</h3>
                <div class="stat-value"><?= $totalUsers ?></div>
            </div>
        </div>

        <!-- Thẻ thống kê Bài hát -->
        <div class="stat-card">
            <div class="stat-icon icon-songs">
                <i class="fas fa-music"></i>
            </div>
            <div class="stat-info">
                <h3>Tổng bài hát</h3>
                <div class="stat-value"><?= $totalSongs ?></div>
            </div>
        </div>

        <!-- Thẻ thống kê Bài hát chờ duyệt -->
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

<?php
// Kiểm tra quyền truy cập admin
require_once __DIR__ . "/check_admin.php";
// Kết nối cơ sở dữ liệu
require_once __DIR__ . "/../config/database.php";

$db = new Database();
$conn = $db->connect();

// Truy vấn lấy danh sách các bài hát có trạng thái 'PENDING' (Đang chờ duyệt)
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
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            /* Các biến màu chủ đạo cho trang */
            --bg-body: #000000;      /* Màu nền chính tối */
            --bg-sidebar: #121212;   /* Màu nền sidebar */
            --bg-card: #181818;      /* Màu nền thẻ/khối */
            --bg-card-hover: #282828; /* Màu nền khi di chuột qua */
            --accent-green: #1DB954; /* Màu xanh Spotify truyền thống */
            --accent-cyan: #00DBFF;  /* Màu xanh cyan */
            --text-main: #ffffff;    /* Màu chữ trắng chính */
            --text-muted: #b3b3b3;   /* Màu chữ xám mô tả */
            --danger: #e91429;       /* Màu đỏ cho các cảnh báo/cấm */
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

        /* Cấu trúc nội dung chính */
        .main-content {
            margin-left: 240px; /* Lùi lề trái để nhường chỗ cho sidebar dính */
            flex-grow: 1; /* Cho phép nội dung mở rộng hết phần còn lại */
            padding: 32px;
            background: linear-gradient(to bottom, #222 0%, #000 300px); /* Nền chuyển màu từ xám sang đen */
        }

        .header {
            margin-bottom: 32px;
        }

        .header h1 {
            font-size: 32px;
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Container chứa bảng danh sách */
        .song-list-container {
            background-color: rgba(24, 24, 24, 0.5); /* Nền đen trong suốt nhẹ */
            border-radius: 8px; /* Bo góc khối */
            padding: 20px;
        }

        /* Thiết lập bảng */
        table {
            width: 100%;
            border-collapse: collapse; /* Loại bỏ khoảng cách giữa các viền ô */
            text-align: left;
        }

        thead th {
            color: var(--text-muted);
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 1px;
            padding-bottom: 12px;
            border-bottom: 1px solid #333; /* Đường gạch dưới tiêu đề cột */
        }

        tbody tr {
            transition: background-color 0.2s;
        }

        tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.1); /* Sáng lên khi di chuột qua hàng */
        }

        td {
            padding: 12px 0;
            vertical-align: middle;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05); /* Đường gạch dưới mờ cho mỗi hàng */
        }

        /* Khối thông tin bài hát (Ảnh + Tên) */
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
            object-fit: cover; /* Đảm bảo ảnh không bị méo */
        }

        .song-details b {
            display: block;
            font-size: 16px;
        }

        .song-details span {
            font-size: 14px;
            color: var(--text-muted);
        }

        /* Các nút bấm hành động */
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
            background-color: var(--accent-green); /* Nút Duyệt màu xanh */
            color: black;
        }

        .btn-reject {
            border: 1px solid var(--text-muted); /* Nút Từ chối dạng viền */
            color: white;
        }

        .btn-action:hover {
            transform: scale(1.05); /* Phóng to nhẹ khi di chuột qua nút */
        }

        /* Giao diện khi danh sách trống */
        .empty-state {
            text-align: center;
            padding: 40px;
            color: var(--text-muted);
        }

        @media (max-width: 768px) {
            .main-content { margin-left: 80px; }
        }
    </style>
</head>
<body>

<!-- Nhúng sidebar -->
<?php include 'sidebar.php'; ?>

<div class="main-content">
    <div class="header">
        <h1>Danh sách chờ duyệt</h1>
    </div>

    <!-- Container chứa bảng danh sách bài hát -->
    <div class="song-list-container">
        <?php if ($result->num_rows > 0): ?>
            <!-- Hiển thị bảng nếu có bài hát chờ duyệt -->
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
                                <!-- Thông tin bài hát: Ảnh và Tiêu đề/Nghệ sĩ -->
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
                                <!-- Các nút hành động: Duyệt hoặc Từ chối -->
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
            <!-- Hiển thị thông báo nếu không có bài hát nào -->
            <div class="empty-state">
                <i class="fas fa-check-circle" style="font-size: 48px; margin-bottom: 16px; color: var(--accent-green);"></i>
                <p>Không có bài hát nào đang chờ duyệt!</p>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>

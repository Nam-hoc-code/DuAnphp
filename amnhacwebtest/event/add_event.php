<?php
require_once '../auth/check_login.php';
require_once '../config/database.php';

/* =========================
   CHỈ CHO ADMIN
========================= */
if (($_SESSION['user']['role'] ?? '') !== 'ADMIN') {
    die('⛔ Bạn không có quyền truy cập trang này');
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Sự Kiện - Admin Dashboard</title>
    <!-- Fonts & Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --spotify-black: #000000;
            --spotify-dark: #121212;
            --spotify-card: #181818;
            --spotify-grey: #282828;
            --spotify-green: #1DB954;
            --spotify-soft-green: #1ed760;
            --text-main: #ffffff;
            --text-sub: #b3b3b3;
            --danger-red: #f15555;
            --accent-blue: #00DBFF;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Outfit', sans-serif;
        }

        body {
            background-color: var(--spotify-black);
            color: var(--text-main);
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Navigation */
        .sidebar {
            width: 260px;
            background-color: #000000;
            padding: 24px 12px;
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            border-right: 1px solid #1f1f1f;
            z-index: 1000;
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 12px 32px 12px;
            font-size: 24px;
            font-weight: 700;
            color: var(--text-main);
            text-decoration: none;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 12px 16px;
            color: var(--text-sub);
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            border-radius: 6px;
            transition: all 0.3s ease;
            margin-bottom: 4px;
        }

        .nav-link:hover, .nav-link.active {
            color: #fff;
            background-color: var(--spotify-grey);
        }

        .nav-link i { font-size: 20px; width: 24px; text-align: center; }

        .nav-link.logout { color: var(--danger-red); margin-top: auto; }
        .nav-link.logout:hover { background-color: rgba(241, 85, 85, 0.1); }

        /* Main Content */
        .main-wrapper {
            margin-left: 260px;
            flex-grow: 1;
            padding: 40px;
            max-width: 1000px;
            background: linear-gradient(to bottom, #1a1a1a 0%, var(--spotify-black) 300px);
        }

        header h1 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 40px;
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .glass-container {
            background-color: var(--spotify-card);
            border-radius: 12px;
            padding: 32px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            border: 1px solid rgba(255,255,255,0.05);
            margin-bottom: 40px;
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .section-title {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title i { color: var(--spotify-green); }

        /* Form Controls */
        .event-form {
            display: grid;
            gap: 24px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

        .input-box {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .input-box label {
            font-weight: 600;
            color: var(--text-sub);
            font-size: 14px;
        }

        input[type="text"], 
        input[type="date"], 
        input[type="number"], 
        input[type="url"],
        input[type="file"] {
            background-color: var(--spotify-grey);
            border: 1px solid transparent;
            color: #fff;
            padding: 14px 18px;
            border-radius: 8px;
            font-size: 15px;
            outline: none;
            transition: all 0.3s;
        }

        input:focus {
            border-color: var(--spotify-green);
            background-color: #333;
        }

        input[type="file"] {
            padding: 10px;
            cursor: pointer;
        }

        ::-webkit-calendar-picker-indicator {
            filter: invert(1);
            cursor: pointer;
        }

        /* Buttons */
        .btn-submit {
            background-color: var(--spotify-green);
            color: #000;
            border: none;
            padding: 16px 32px;
            border-radius: 500px;
            font-weight: 700;
            font-size: 15px;
            cursor: pointer;
            transition: transform 0.2s, background-color 0.2s;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 10px;
        }

        .btn-submit:hover {
            transform: scale(1.02);
            background-color: var(--spotify-soft-green);
        }

        .manage-box {
            background: rgba(255,255,255,0.03);
            border-radius: 12px;
            padding: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border: 1px dashed #444;
        }

        .manage-info {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .manage-info i {
            font-size: 32px;
            color: var(--accent-blue);
        }

        .btn-view-list {
            color: var(--spotify-green);
            text-decoration: none;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: 0.2s;
        }

        .btn-view-list:hover {
            text-decoration: underline;
            color: #fff;
        }

        @media (max-width: 768px) {
            .form-row { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <!-- Sidebar Navigation -->
    <aside class="sidebar">
        <a href="../admin/admin_view.php" class="logo-section">
            <i class="fa-brands fa-spotify" style="color: var(--spotify-green); font-size: 36px;"></i>
            <span>Spotify Admin</span>
        </a>
        <nav>
            <a href="../admin/admin_view.php" class="nav-link"><i class="fa-solid fa-house"></i> Trang chủ</a>
            <a href="../admin/song_requests.php" class="nav-link"><i class="fa-solid fa-music"></i> Duyệt bài hát</a>
            <a href="add_event.php" class="nav-link active"><i class="fa-solid fa-calendar-plus"></i> Thêm sự kiện</a>
        </nav>
        <a href="../auth/logout.php" class="nav-link logout"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a>
    </aside>

    <!-- Main Content -->
    <div class="main-wrapper">
        <header>
            <h1><i class="fa-solid fa-calendar-day" style="color: var(--accent-blue);"></i> Quản lý Sự kiện</h1>
        </header>

        <!-- Section: Thêm sự kiện mới -->
        <section class="glass-container">
            <h2 class="section-title"><i class="fa-solid fa-plus-circle"></i> Thêm sự kiện mới</h2>
            
            <form action="add_event_process.php" method="POST" enctype="multipart/form-data" class="event-form">
                
                <div class="input-box">
                    <label>Tên sự kiện</label>
                    <input type="text" name="name" placeholder="Tên concert, liveshow..." required>
                </div>

                <div class="form-row">
                    <div class="input-box">
                        <label>Ngày diễn ra</label>
                        <input type="date" name="event_date" required>
                    </div>
                    <div class="input-box">
                        <label>Giá vé (VNĐ)</label>
                        <input type="number" name="price" placeholder="Ví dụ: 500000" min="0">
                    </div>
                </div>

                <div class="input-box">
                    <label>Link mua vé</label>
                    <input type="url" name="buy_url" placeholder="https://ticketbox.vn/..." required>
                </div>

                <div class="input-box">
                    <label>Ảnh banner sự kiện (Poster)</label>
                    <input type="file" name="banner" accept="image/*" required>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fa-solid fa-check-circle"></i> Thêm sự kiện ngay
                </button>
            </form>
        </section>

        <!-- Section: Quản lý sự kiện -->
        <section>
            <div class="manage-box">
                <div class="manage-info">
                    <i class="fa-solid fa-clipboard-list"></i>
                    <div>
                        <h3 style="font-size: 18px; font-weight: 700;">Quản lý sự kiện</h3>
                        <p style="color: var(--text-sub); font-size: 14px;">Xem và chỉnh sửa danh sách các sự kiện đã tạo.</p>
                    </div>
                </div>
                <a href="../event/event_list.php" class="btn-view-list">
                    Xem danh sách sự kiện <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </section>
    </div>

</body>
</html>

<!-- <h2>➕ Thêm sự kiện mới</h2>

<form action="add_event_process.php" method="POST" enctype="multipart/form-data">

    <label>Tên sự kiện</label><br>
    <input type="text" name="name" required><br><br>

    <label>Ngày diễn ra</label><br>
    <input type="date" name="event_date" required><br><br>

    <label>Giá vé (VNĐ)</label><br>
    <input type="number" name="price" min="0"><br><br>

    <label>Link mua vé</label><br>
    <input type="url" name="buy_url" required><br><br>

    <label>Ảnh banner sự kiện</label><br>
    <input type="file" name="banner" accept="image/*" required><br><br>

    <button type="submit">➕ Thêm sự kiện</button>
</form>

<hr>

<h3>📋 Quản lý sự kiện</h3>
<a href="../event/event_list.php">➡️ Xem danh sách sự kiện</a> -->

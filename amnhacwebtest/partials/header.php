<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$keyword = $_GET['q'] ?? '';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { 
            /* Hệ thống biến màu dùng chung cho toàn bộ ứng dụng */
            --bg-black: #000000;      /* Màu nền đen tuyền */
            --sidebar-bg: #121212;    /* Màu xám tối cho sidebar */
            --card-bg: #181818;       /* Màu nền cho các thẻ sản phẩm/bài hát */
            --text-main: #ffffff;     /* Màu chữ trắng chính */
            --text-sub: #b3b3b3;      /* Màu chữ xám cho thông tin phụ */
            --spotify-green: #1DB954; /* Màu thương hiệu xanh lá */
            --nav-hover: #282828;     /* Màu nền khi hover vào menu */
            --logout-red: #f15555;    /* Màu đỏ cho nút đăng xuất/xóa */
            --table-border: #282828;  /* Màu đường viền bảng */
            --player-bg: #121212;     /* Màu nền trình phát nhạc */
            --glass: rgba(255, 255, 255, 0.05); /* Hiệu ứng kính mờ (glassmorphism) */
        }

        * { 
            box-sizing: border-box; 
            font-family: 'Outfit', sans-serif;
        }

        body { 
            margin: 0; 
            background-color: var(--bg-black); 
            color: var(--text-main); 
            overflow-x: hidden; /* Ngăn chặn cuộn ngang trình duyệt */
        }

        .app-container {
            display: flex;
            min-height: 100vh;
        }

        /* Reverted Search Header Styles */
        .top-nav {
            position: fixed;
            top: 0;
            left: 260px; /* Reverted to 260px */
            right: 0;
            height: 64px;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(20px);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
            z-index: 1000;
        }

        .nav-center {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: flex-start; /* Align search to left in center area */
            gap: 8px;
            max-width: 500px;
        }

        .search-container {
            position: relative;
            flex: 1;
            max-width: 400px;
        }

        .search-container i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #b3b3b3;
            font-size: 16px;
        }

        .search-input {
            width: 100%;
            height: 40px; /* Slightly smaller for cleaner look */
            background: #242424;
            border: none;
            border-radius: 20px;
            padding: 0 40px;
            color: #fff;
            font-size: 14px;
            transition: background 0.2s;
        }

        .search-input:hover { background: #f0ebebff; }
        .search-input:focus {
            outline: none;
            background: #2a2a2a;
            box-shadow: 0 0 0 1px #fff;
        }

        .search-input::placeholder { color: #b3b3b3; }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .user-container {
            position: relative;
        }

        .search-icon {
            position: absolute;
            left: 390px;
            top: 50%;
            transform: translateY(-50%);
            color: #000;
            font-size: 18px;
        }
        .search-input {
            width: 100%;
            background: #fff;
            border: none;
            padding: 12px 48px;
            border-radius: 500px;
            font-family: 'Outfit', sans-serif;
            font-size: 14px;
            color: #000;
            outline: none;
        }
        .user-menu {
            display: flex;
            align-items: center;
            gap: 12px;
            background: rgba(0,0,0,0.6);
            padding: 4px 12px 4px 4px;
            border-radius: 32px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 1px solid rgba(255,255,255,0.1);
        }

        .user-menu:hover { 
            background: #282828;
            border-color: rgba(255,255,255,0.2);
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            background: #535353;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            color: #fff;
        }

        .user-name { 
            font-size: 14px; 
            font-weight: 700; 
            color: #fff;
            margin-right: 4px;
        }

        /* Dropdown Menu Styles */
        .dropdown-menu {
            position: absolute;
            top: 50px;
            right: 0;
            width: 180px;
            background: #282828;
            border-radius: 4px;
            padding: 4px;
            box-shadow: 0 16px 24px rgba(0,0,0,0.5);
            display: none;
            flex-direction: column;
            z-index: 1001;
            animation: fadeInScale 0.2s ease-out;
        }

        .dropdown-menu.show { display: flex; }

        .dropdown-item {
            padding: 12px 16px;
            color: #eaeaea;
            font-size: 14px;
            font-weight: 500;
            display: block;
            border-radius: 2px;
            text-decoration: none;
            transition: background 0.2s;
        }

        .dropdown-item:hover { background: rgba(255,255,255,0.1); color: #fff; }

        .dropdown-divider {
            height: 1px;
            background: rgba(255,255,255,0.1);
            margin: 4px 0;
            border: none;
        }

        /* Nút đăng nhập/đăng ký */
        .btn-auth {
            color: var(--text-main);
            text-decoration: none;
            font-weight: 700;
            font-size: 14px;
            padding: 8px 24px;
            border-radius: 500px;
            transition: transform 0.2s;
        }

        .btn-login { background: #fff; color: #000; }
        .btn-login:hover { transform: scale(1.05); }

        @keyframes fadeInScale {
            from { opacity: 0; transform: scale(0.95) translateY(-10px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }

        a { text-decoration: none; color: inherit; }
    </style>
</head>
<body>

<div class="app-container">
    <header class="top-nav">

        <div>
        <!-- thanh search nha -->   
        <form method="GET" action="../services/search.php" class="search-form">
            <i class="fa-solid fa-magnifying-glass search-icon"></i>
            <input 
                type="text"
                name="q"
                class="search-input"
                placeholder="Bạn muốn nghe gì?"
                value="<?= htmlspecialchars($keyword) ?>"
                required
            >
        </form>
        </div>
        <div class="nav-right">
            <?php if (isset($_SESSION['user'])): ?>
                <div class="user-container">
                    <div class="user-menu" id="userMenuTrigger">
                        <div class="user-avatar">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <span class="user-name"><?= htmlspecialchars($_SESSION['user']['username']) ?></span>
                        <i class="fa-solid fa-caret-down" style="font-size: 12px; color: var(--text-sub);"></i>
                    </div>
                    
                    <div class="dropdown-menu" id="userMenuDropdown">
                        <a href="profile.php" class="dropdown-item">Tài khoản</a>
                        <hr class="dropdown-divider">
                        <a href="../auth/logout.php" class="dropdown-item" style="color: var(--logout-red);">Đăng xuất</a>
                    </div>
                </div>

                <script>
                    const trigger = document.getElementById('userMenuTrigger');
                    const dropdown = document.getElementById('userMenuDropdown');

                    trigger.addEventListener('click', (e) => {
                        e.stopPropagation();
                        dropdown.classList.toggle('show');
                    });

                    document.addEventListener('click', () => {
                        dropdown.classList.remove('show');
                    });

                    dropdown.addEventListener('click', (e) => {
                        e.stopPropagation();
                    });
                </script>
            <?php else: ?>
                <a href="../auth/register_form.php" class="btn-auth" style="color: var(--text-sub);">Đăng ký</a>
                <a href="../auth/login_form.php" class="btn-auth btn-login">Đăng nhập</a>
            <?php endif; ?>
        </div>
    </header>

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
            --bg-black: #000000;
            --sidebar-bg: #121212;
            --card-bg: #181818;
            --text-main: #ffffff;
            --text-sub: #b3b3b3;
            --spotify-green: #1DB954;
            --nav-hover: #282828;
            --logout-red: #f15555;
            --table-border: #282828;
            --player-bg: #121212;
            --glass: rgba(255, 255, 255, 0.05);
        }

        * { 
            box-sizing: border-box; 
            font-family: 'Outfit', sans-serif;
        }

        body { 
            margin: 0; 
            background-color: var(--bg-black); 
            color: var(--text-main); 
            overflow-x: hidden;
        }

        .app-container {
            display: flex;
            min-height: 100vh;  
        }

        .top-nav {
            position: fixed;
            top: 0;
            left: 260px;
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

        .search-form {
            flex: 1;
            max-width: 500px;
        }

        .search-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #000;
            font-size: 16px;
            pointer-events: none;
        }

        .search-input {
            width: 100%;
            height: 40px;
            background: #fff;
            border: none;
            border-radius: 500px;
            padding: 0 48px;
            font-family: 'Outfit', sans-serif;
            font-size: 14px;
            color: #000;
            outline: none;
            position: relative;
        }

        .search-input::placeholder { 
            color: #999;
        }

        .search-input:focus {
            box-shadow: 0 0 0 2px var(--spotify-green);
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .user-container {
            position: relative;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 12px;
            background: rgba(0, 0, 0, 0.6);
            padding: 4px 12px 4px 4px;
            border-radius: 32px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .user-menu:hover { 
            background: #282828;
            border-color: rgba(255, 255, 255, 0.2);
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
            flex-shrink: 0;
        }

        .user-name { 
            font-size: 14px; 
            font-weight: 700; 
            color: #fff;
            margin-right: 4px;
            max-width: 120px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .dropdown-menu {
            position: absolute;
            top: 50px;
            right: 0;
            width: 180px;
            background: #282828;
            border-radius: 4px;
            padding: 4px;
            box-shadow: 0 16px 24px rgba(0, 0, 0, 0.5);
            display: none;
            flex-direction: column;
            z-index: 1001;
            animation: fadeInScale 0.2s ease-out;
        }

        .dropdown-menu.show { 
            display: flex; 
        }

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

        .dropdown-item:hover { 
            background: rgba(255, 255, 255, 0.1); 
            color: #fff; 
        }

        .dropdown-divider {
            height: 1px;
            background: rgba(255, 255, 255, 0.1);
            margin: 4px 0;
            border: none;
        }

        .btn-auth {
            color: var(--text-main);
            text-decoration: none;
            font-weight: 700;
            font-size: 14px;
            padding: 8px 24px;
            border-radius: 500px;
            transition: transform 0.2s;
            border: none;
            cursor: pointer;
        }

        .btn-login { 
            background: #fff; 
            color: #000; 
        }

        .btn-login:hover { 
            transform: scale(1.05); 
        }

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
        <div class="search-form">
            <form method="GET" action="../services/search.php" style="position: relative;">
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
            <?php if (isset($_SESSION['user']) && !empty($_SESSION['user'])): ?>
                <div class="user-container">
                    <div class="user-menu" id="userMenuTrigger">
                        <div class="user-avatar">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <span class="user-name"><?= htmlspecialchars($_SESSION['user']['username'] ?? 'User') ?></span>
                        <i class="fa-solid fa-caret-down" style="font-size: 12px; color: var(--text-sub); flex-shrink: 0;"></i>
                    </div>
                    
                    <div class="dropdown-menu" id="userMenuDropdown">
                        <a href="../page/user_info.php" class="dropdown-item">
                            <i class="fa-solid fa-user" style="margin-right: 8px;"></i>Tài khoản
                        </a>
                        <a href="../user/my_orders.php" class="dropdown-item">
                            <i class="fa-solid fa-box-open" style="margin-right: 8px;"></i>Đơn hàng của tôi
                        </a>
                        <hr class="dropdown-divider">
                        <a href="../auth/logout.php" class="dropdown-item" style="color: var(--logout-red);">
                            <i class="fa-solid fa-sign-out-alt" style="margin-right: 8px;"></i>Đăng xuất
                        </a>
                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const trigger = document.getElementById('userMenuTrigger');
                        const dropdown = document.getElementById('userMenuDropdown');

                        if (trigger && dropdown) {
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
                        }
                    });
                </script>
            <?php else: ?>
                <a href="../auth/register_form.php" class="btn-auth" style="color: var(--text-sub);">Đăng ký</a>
                <a href="../auth/login_form.php" class="btn-auth btn-login">Đăng nhập</a>
            <?php endif; ?>
        </div>
    </header>
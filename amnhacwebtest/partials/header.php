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

        * { box-sizing: border-box; }
        body { 
            font-family: 'Outfit', sans-serif; 
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
            backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
            z-index: 100;
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
            gap: 16px;
            background: rgba(0,0,0,0.5);
            padding: 4px 12px 4px 4px;
            border-radius: 32px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .user-menu:hover { background: #282828; }

        .user-avatar {
            width: 28px;
            height: 28px;
            background: #535353;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }

        .user-name { font-size: 14px; font-weight: 700; }

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
        .btn-logout { background: transparent; color: var(--text-sub); }
        .btn-logout:hover { color: #fff; }

        a { text-decoration: none; color: inherit; }
    </style>
</head>
<body>

<div class="app-container">
    <header class="top-nav">
        <div class="nav-left">
            <!-- Search or Breadcrumbs could go here -->
        </div>
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
                <div class="user-menu" title="Tài khoản">
                    <div class="user-avatar">
                        <i class="fa-solid fa-user"></i>
                    </div>
                    <span class="user-name"><?= htmlspecialchars($_SESSION['user']['username']) ?></span>
                </div>
                <a href="../auth/logout.php" class="btn-auth btn-logout">Đăng xuất</a>
<?php else: ?>
    <a href="../auth/register_form.php" class="btn-auth" style="color: var(--text-sub);">Đăng ký</a>
    <a href="../auth/login_form.php" class="btn-auth btn-login">Đăng nhập</a>
<?php endif; ?>
        </div>
    </header>

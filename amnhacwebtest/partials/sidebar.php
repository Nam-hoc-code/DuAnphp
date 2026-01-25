<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<style>
    .sidebar { 
        width: 260px; 
        height: 100vh; 
        background: var(--bg-black); 
        position: fixed; 
        padding: 24px 8px; 
        display: flex;
        flex-direction: column;
        box-sizing: border-box;
        z-index: 1000;
    }

    .logo-container {
        display: flex;
        align-items: center;
        padding: 0 16px 24px 16px;
        gap: 8px;
        color: #fff;
    }
    .logo-container i { font-size: 28px; color: var(--spotify-green); }
    .logo-container span { font-size: 1.5rem; font-weight: 700; letter-spacing: -1px; }

    .nav-group { flex-grow: 1; }

    .nav-link { 
        display: flex; 
        align-items: center; 
        color: var(--text-sub); 
        text-decoration: none; 
        padding: 12px 16px; 
        border-radius: 4px; 
        font-weight: 700; 
        font-size: 14px;
        transition: 0.3s;
        margin-bottom: 4px;
    }

    .nav-link:hover { color: #fff; }
    .nav-link.active { background-color: var(--nav-hover); color: #fff; }
    
    .nav-link i { margin-right: 16px; font-size: 20px; width: 24px; text-align: center; }

    .sidebar-divider {
        height: 1px;
        background: #282828;
        margin: 8px 16px;
    }

    .sidebar-footer {
        padding: 16px;
        font-size: 11px;
        color: var(--text-sub);
    }
</style>

<div class="sidebar">
    <a href="../user/home.php" class="logo-container">
        <i class="fa-brands fa-spotify"></i>
        <span>Music Platform</span>
    </a>

    <div class="nav-group">
        <a href="../user/home.php" class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'home.php') !== false ? 'active' : '' ?>">
            <i class="fa-solid fa-house"></i> Trang chủ
        </a>
        <a href="../favorite/favorite_list.php" class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'favorite_list.php') !== false ? 'active' : '' ?>">
            <i class="fa-solid fa-heart"></i> Yêu thích
        </a>
        <a href="../disc/disclist.php" class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'disclist.php') !== false ? 'active' : '' ?>">
            <i class="fa-solid fa-compact-disc"></i> Mua đĩa
        </a>
        <a href="../event/event_list.php" class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'event_list.php') !== false ? 'active' : '' ?>">
            <i class="fa-solid fa-ticket"></i> Sự kiện
        </a>
        <a href="../services/search.php" class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'search.php') !== false ? 'active' : '' ?>">
            <i class="fa-solid fa-magnifying-glass"></i> Tìm kiếm
        </a>

        <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'artist'): ?>
            <div class="sidebar-divider"></div>
            <a href="../artist/artist_view.php" class="nav-link">
                <i class="fa-solid fa-microphone"></i> Artist Dashboard
            </a>
        <?php endif; ?>

        <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>
            <div class="sidebar-divider"></div>
            <a href="../admin/admin_view.php" class="nav-link">
                <i class="fa-solid fa-user-shield"></i> Admin Panel
            </a>
        <?php endif; ?>
    </div>

    <div class="sidebar-footer">
        <span>© 2026 Music Platform</span>
    </div>
</div>

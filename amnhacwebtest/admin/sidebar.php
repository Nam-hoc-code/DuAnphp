<?php
// Lấy tên file hiện tại để đánh dấu trạng thái "active" trên menu
$current_page = basename($_SERVER['PHP_SELF']);
?>
<style>
    :root {
        --bg-sidebar: #121212;
        --bg-card-hover: #282828;
        --accent-green: #1DB954;
        --text-muted: #b3b3b3;
    }

    .sidebar {
        width: 240px;
        background-color: var(--bg-sidebar);
        padding: 24px;
        display: flex;
        flex-direction: column;
        position: fixed;
        height: 100vh;
        left: 0;
        top: 0;
    }

    .logo {
        margin-bottom: 32px;
        display: flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        color: white;
        font-size: 24px;
        font-weight: bold;
    }

    .logo svg {
        width: 40px;
        height: 40px;
        fill: var(--accent-green);
    }

    .nav-menu {
        list-style: none;
        flex-grow: 1;
    }

    .nav-item {
        margin-bottom: 8px;
    }

    .nav-link {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 12px;
        text-decoration: none;
        color: var(--text-muted);
        font-weight: 500;
        border-radius: 4px;
        transition: all 0.2s;
    }

    .nav-link:hover, .nav-link.active {
        color: white;
        background-color: var(--bg-card-hover);
    }

    .nav-link i {
        font-size: 20px;
        width: 24px;
        text-align: center;
    }

    .logout-btn {
        margin-top: auto;
        color: #ff5555 !important;
    }
    
    .logout-btn:hover {
        background-color: rgba(255, 85, 85, 0.1);
    }

    @media (max-width: 768px) {
        .sidebar {
            width: 80px;
            padding: 20px 10px;
        }
        .logo span, .nav-link span {
            display: none;
        }
    }
</style>

<div class="sidebar">
    <!-- Logo và tên Thương hiệu -->
    <a href="../admin/admin_view.php" class="logo">
        <svg viewBox="0 0 167.5 167.5">
            <path d="M83.7,0C37.5,0,0,37.5,0,83.7s37.5,83.7,83.7,83.7s83.7-37.5,83.7-83.7S130,0,83.7,0z M122.1,120.8 c-1.5,2.4-4.5,3.2-6.9,1.7c-19.1-11.7-43.2-14.3-71.5-7.8c-2.7,0.6-5.4-1-6.1-3.7c-0.6-2.7,1-5.4,3.7-6.1 c30.9-7,57.7-4.1,79.1,9C122.7,115.4,123.5,118.4,122.1,120.8z M132.3,98c-1.9,3-5.8,4-8.8,2.1c-21.9-13.5-55.3-17.4-81.2-9.5 c-3.3,1-6.8-0.8-7.9-4.1c-1-3.3,0.8-6.8,4.1-7.9c30-9.1,67-4.7,92,10.6C133.7,91.1,134.6,95,132.3,98z M133.3,74.5 c-26.2-15.6-69.5-17-94.7-9.4c-4,1.2-8.2-1.1-9.4-5.1c-1.2-4,1.1-8.2,5.1-9.4c30.1-9.1,78.1-7.4,109,10.9 c3.6,2.1,4.8,6.8,2.7,10.4C134,75.1,129.3,76.3,133.3,74.5z"/>
        </svg>
        <span>Spotify</span>
    </a>

    <!-- Danh sách menu điều hướng -->
    <ul class="nav-menu">
        <li class="nav-item">
            <!-- Nếu trang hiện tại là admin_view.php thì thêm class 'active' -->
            <a href="../admin/admin_view.php" class="nav-link <?= $current_page == 'admin_view.php' ? 'active' : '' ?>">
                <i class="fas fa-home"></i>
                <span>Trang chủ</span>
            </a>
        </li>
        <li class="nav-item">
            <!-- Menu Duyệt bài hát -->
            <a href="../admin/song_requests.php" class="nav-link <?= $current_page == 'song_requests.php' ? 'active' : '' ?>">
                <i class="fas fa-music"></i>
                <span>Duyệt bài hát</span>
            </a>
        </li>        
        <li class="nav-item">
            <!-- Menu Thêm sự kiện -->
            <a href="../event/add_event.php" class="nav-link <?= $current_page == 'add_event.php' ? 'active' : '' ?>">
                <i class="fas fa-calendar-plus"></i>
                <span>Thêm sự kiện</span>
            </a>
        </li>

        <li class="nav-item">
            <!-- Menu Thêm sự kiện -->
            <a href="../admin/admin_log.php" class="nav-link" >
                <i class="fas fa-calendar-plus"></i>
                <span>Nhật ký duyệt</span>
            </a>
        </li>

        <li class="nav-item">
            <!-- Nút đăng xuất -->
            <a href="../auth/logout.php" class="nav-link logout-btn">
                <i class="fas fa-sign-out-alt"></i>
                <span>Đăng xuất</span>
            </a>
        </li>
    </ul>
</div>

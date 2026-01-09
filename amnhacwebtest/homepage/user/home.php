<?php
// Kết nối database
require_once "../../config/database.php";
session_start();
$db = new Database();
$conn = $db->connect();
// Lấy danh sách bài hát thịnh hành
$sql_songs = "SELECT * FROM songs WHERE is_deleted = 0 ORDER BY created_at DESC LIMIT 9";
$result_songs = $conn->query($sql_songs);

// Lấy danh sách nghệ sĩ phổ biến
$sql_artists = "SELECT DISTINCT artist FROM songs WHERE is_deleted = 0 LIMIT 4";
$result_artists = $conn->query($sql_artists);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Music Website - Nghe nhạc trực tuyến</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #121212;
            color: #ffffff;
            min-height: 100vh;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header styles */
        header {
            background-color: #1a1a1a;
            padding: 20px 0;
            border-bottom: 1px solid #333;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 28px;
            font-weight: 700;
            color: #1db954;
            text-decoration: none;
            display: flex;
            align-items: center;
        }

        .logo i {
            margin-right: 10px;
        }

        .search-container {
            flex-grow: 1;
            max-width: 500px;
            margin: 0 30px;
            position: relative;
        }

        .search-box {
            width: 100%;
            padding: 12px 20px;
            padding-left: 50px;
            border-radius: 30px;
            border: none;
            background-color: #333;
            color: white;
            font-size: 16px;
            outline: none;
        }

        .search-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #aaa;
        }

        .auth-buttons {
            display: flex;
            gap: 10px;
        }

        .login-btn, .register-btn {
            padding: 10px 25px;
            border-radius: 30px;
            font-weight: 600;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
            border: none;
        }

        .login-btn {
            background-color: #1db954;
            color: white;
        }

        .login-btn:hover {
            background-color: #1ed760;
        }

        .register-btn {
            background-color: transparent;
            color: white;
            border: 1px solid #555;
        }

        .register-btn:hover {
            border-color: #888;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #333;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: #1db954;
        }

        .username {
            font-weight: 600;
        }

        .logout-btn {
            background-color: transparent;
            color: #aaa;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }

        .logout-btn:hover {
            color: white;
        }

        /* Main content styles */
        main {
            padding: 40px 0;
        }

        .section-title {
            font-size: 24px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
        }

        .section-title i {
            margin-right: 10px;
            color: #1db954;
        }

        .songs-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 25px;
            margin-bottom: 50px;
        }

        .song-card {
            background-color: #181818;
            border-radius: 10px;
            padding: 20px;
            transition: background-color 0.3s;
            cursor: pointer;
        }

        .song-card:hover {
            background-color: #282828;
        }

        .song-image {
            width: 100%;
            aspect-ratio: 1/1;
            border-radius: 8px;
            margin-bottom: 15px;
            object-fit: cover;
        }

        .song-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 8px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .song-artist {
            color: #aaa;
            font-size: 15px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .song-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
        }

        .action-btn {
            background: none;
            border: none;
            color: #aaa;
            cursor: pointer;
            font-size: 18px;
            transition: color 0.3s;
        }

        .action-btn:hover {
            color: #1db954;
        }

        .artists-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .artist-card {
            display: flex;
            align-items: center;
            background-color: #181818;
            border-radius: 10px;
            padding: 15px;
            width: calc(50% - 10px);
            transition: background-color 0.3s;
            cursor: pointer;
        }

        .artist-card:hover {
            background-color: #282828;
        }

        .artist-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 15px;
            background-color: #333;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: #1db954;
        }

        .artist-name {
            font-size: 18px;
            font-weight: 600;
        }

        /* Footer styles */
        footer {
            background-color: #1a1a1a;
            padding: 30px 0;
            text-align: center;
            border-top: 1px solid #333;
            margin-top: 40px;
        }

        .footer-content {
            max-width: 800px;
            margin: 0 auto;
        }

        .footer-links {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-bottom: 20px;
        }

        .footer-link {
            color: #aaa;
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer-link:hover {
            color: #1db954;
        }

        .copyright {
            color: #666;
            font-size: 14px;
        }

        /* Player bar */
        .player-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: #181818;
            padding: 15px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-top: 1px solid #333;
            z-index: 100;
        }

        .player-info {
            display: flex;
            align-items: center;
            width: 25%;
        }

        .player-image {
            width: 50px;
            height: 50px;
            border-radius: 5px;
            margin-right: 15px;
        }

        .player-text h4 {
            font-size: 16px;
            margin-bottom: 5px;
        }

        .player-text p {
            font-size: 14px;
            color: #aaa;
        }

        .player-controls {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 50%;
        }

        .control-buttons {
            display: flex;
            align-items: center;
            gap: 25px;
            margin-bottom: 10px;
        }

        .control-btn {
            background: none;
            border: none;
            color: white;
            font-size: 20px;
            cursor: pointer;
            transition: color 0.3s;
        }

        .control-btn.play-btn {
            background-color: white;
            color: black;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .control-btn:hover {
            color: #1db954;
        }

        .progress-bar {
            width: 100%;
            height: 4px;
            background-color: #333;
            border-radius: 2px;
            position: relative;
            cursor: pointer;
        }

        .progress {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 30%;
            background-color: #1db954;
            border-radius: 2px;
        }

        .player-extra {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            width: 25%;
            gap: 15px;
        }

        .volume-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .volume-slider {
            width: 80px;
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background-color: #1a1a1a;
            padding: 40px;
            border-radius: 10px;
            width: 90%;
            max-width: 400px;
        }

        .modal-title {
            font-size: 24px;
            margin-bottom: 25px;
            text-align: center;
            color: #1db954;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #aaa;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            background-color: #333;
            border: 1px solid #444;
            border-radius: 5px;
            color: white;
            font-size: 16px;
        }

        .form-control:focus {
            outline: none;
            border-color: #1db954;
        }

        .submit-btn {
            width: 100%;
            padding: 12px;
            background-color: #1db954;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .submit-btn:hover {
            background-color: #1ed760;
        }

        .modal-switch {
            text-align: center;
            margin-top: 20px;
            color: #aaa;
        }

        .modal-switch a {
            color: #1db954;
            text-decoration: none;
            cursor: pointer;
        }

        .modal-switch a:hover {
            text-decoration: underline;
        }

        .close-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            background: none;
            border: none;
            color: #aaa;
            font-size: 24px;
            cursor: pointer;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                align-items: flex-start;
            }

            .search-container {
                max-width: 100%;
                margin: 20px 0;
            }

            .auth-buttons {
                align-self: flex-end;
                margin-top: -60px;
            }

            .songs-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            }

            .artist-card {
                width: 100%;
            }

            .player-bar {
                flex-direction: column;
                padding: 15px;
            }

            .player-info, .player-controls, .player-extra {
                width: 100%;
                margin-bottom: 15px;
            }

            .player-info {
                justify-content: center;
                text-align: center;
            }

            .player-extra {
                justify-content: center;
            }
        }

        /* Error message */
        .error-message {
            background-color: #ff3333;
            color: white;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
        }

        .success-message {
            background-color: #1db954;
            color: white;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
        }

        /* ----- New layout tweaks to match provided UI ----- */
        /* fixed narrow sidebar */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            width: 96px;
            background: #0f0f0f;
            border-right: 1px solid rgba(255,255,255,0.03);
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px 8px;
            gap: 20px;
            z-index: 999;
        }

        .side-logo { width:48px; height:48px; border-radius:50%; background: linear-gradient(45deg,#1db954,#1ed760); box-shadow:0 6px 18px rgba(0,0,0,0.6); display:flex; align-items:center; justify-content:center; }
        .side-logo i { color: #fff; font-size: 20px; }

        .side-menu-item { width:100%; background: rgba(255,255,255,0.03); color:#fff; padding:10px 6px; border-radius:8px; text-align:center; font-weight:700; font-size:13px; }

        /* create left gutter for main container */
        body { padding-left: 120px; }

        /* make header/content align with main layout */
        header, main, footer { margin-left: 0; }

        /* Trending row -> horizontal scroll */
        .songs-grid {
            display: flex;
            gap: 18px;
            overflow-x: auto;
            padding-bottom: 8px;
            -webkit-overflow-scrolling: touch;
        }

        .song-card {
            min-width: 180px;
            max-width: 200px;
            background-color: transparent;
            padding: 0;
            border-radius: 12px;
            text-align: center;
        }

        .song-image {
            width: 100%;
            border-radius: 14px;
            margin-bottom: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.6);
        }

        .song-title { font-size: 14px; margin: 6px 0 4px; color: #ffffff; }
        .song-artist { color: rgba(255,255,255,0.75); font-size: 13px; }

        /* Artists: circular avatars and centered names */
        .artists-container { display: flex; gap: 32px; align-items: flex-start; padding-top: 8px; }
        .artist-card { background: transparent; border-radius: 8px; padding: 0; flex-direction: column; align-items: center; text-align: center; }
        .artist-avatar { width: 120px; height: 120px; border-radius: 50%; overflow: hidden; background-color: #222; display:flex; align-items:center; justify-content:center; box-shadow: 0 10px 30px rgba(0,0,0,0.6); }
        .artist-name { margin-top: 12px; font-size: 15px; }

        @media (max-width: 1024px) {
            body { padding-left: 0; }
            .sidebar { display: none; }
            .songs-grid { gap: 16px; }
        }

    </style>
    </head>
    <body>
        <!-- Left sidebar -->
        <aside class="sidebar" aria-label="Main sidebar">
            <div class="side-logo"><i class="fab fa-spotify" aria-hidden="true"></i></div>
            <div class="side-menu-item">Danh sách bài hát</div>
        </aside>
    <!-- Header -->
    <header>
        <div class="container">
            <div class="header-content">
                <a href="index.php" class="logo">
                    <i class="fas fa-music"></i> Music Website
                </a>
                
                <div class="search-container">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" class="search-box" placeholder="Tìm kiếm bài hát">
                </div>
                
                <?php if(isset($_SESSION['user_id'])): ?>
                    <div class="user-menu">
                        <div class="user-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <div class="username"><?php echo htmlspecialchars($_SESSION['username']); ?></div>
                            <button class="logout-btn" onclick="location.href='logout.php'">Đăng xuất</button>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="auth-buttons">
                        <button class="login-btn" onclick="showLoginModal()">Đăng Nhập</button>
                        <button class="register-btn" onclick="showRegisterModal()">Đăng Ký</button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container">
        <!-- Popular Songs Section -->
        <section class="popular-songs">
            <h2 class="section-title">
                <i class="fas fa-fire"></i> Những bài hát thịnh hành
            </h2>
            
            <div class="songs-grid">
                <?php if($result_songs->num_rows > 0): ?>
                    <?php while($song = $result_songs->fetch_assoc()): ?>
                        <div class="song-card" onclick="playSong(<?php echo $song['song_id']; ?>, '<?php echo htmlspecialchars($song['title']); ?>', '<?php echo htmlspecialchars($song['artist']); ?>')">
                            <?php if($song['image_path']): ?>
                                <img src="<?php echo htmlspecialchars($song['image_path']); ?>" alt="<?php echo htmlspecialchars($song['title']); ?>" class="song-image">
                            <?php else: ?>
                                <img src="https://via.placeholder.com/200x200/1db954/ffffff?text=<?php echo substr($song['title'], 0, 1); ?>" alt="<?php echo htmlspecialchars($song['title']); ?>" class="song-image">
                            <?php endif; ?>
                            <h3 class="song-title"><?php echo htmlspecialchars($song['title']); ?></h3>
                            <p class="song-artist"><?php echo htmlspecialchars($song['artist']); ?></p>
                            <div class="song-actions">
                                <?php if(isset($_SESSION['user_id'])): ?>
                                    <button class="action-btn" onclick="toggleFavorite(event, <?php echo $song['song_id']; ?>)">
                                        <i class="far fa-heart"></i>
                                    </button>
                                <?php endif; ?>
                                <button class="action-btn" onclick="playSong(<?php echo $song['song_id']; ?>, '<?php echo htmlspecialchars($song['title']); ?>', '<?php echo htmlspecialchars($song['artist']); ?>')">
                                    <i class="fas fa-play"></i>
                                </button>
                                <button class="action-btn">
                                    <i class="fas fa-ellipsis-h"></i>
                                </button>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>Không có bài hát nào.</p>
                <?php endif; ?>
            </div>
        </section>

        <!-- Popular Artists Section -->
        <section class="popular-artists">
            <h2 class="section-title">
                <i class="fas fa-users"></i> Nghệ sĩ phổ biến
            </h2>
            
            <div class="artists-container">
                <?php if($result_artists->num_rows > 0): ?>
                    <?php while($artist = $result_artists->fetch_assoc()): ?>
                        <div class="artist-card">
                            <div class="artist-avatar">
                                <i class="fas fa-user"></i>
                            </div>
                            <div>
                                <h3 class="artist-name"><?php echo htmlspecialchars($artist['artist']); ?></h3>
                                <p class="song-artist">Nghệ sĩ</p>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>Không có nghệ sĩ nào.</p>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer>
        <div class="container footer-content">
            <div class="footer-links">
                <a href="#" class="footer-link">Giới thiệu</a>
                <a href="#" class="footer-link">Hỗ trợ</a>
                <a href="#" class="footer-link">Điều khoản</a>
                <a href="#" class="footer-link">Bảo mật</a>
                <a href="#" class="footer-link">Liên hệ</a>
            </div>
            <p class="copyright">© 2023 Music Website. Tất cả các quyền được bảo lưu.</p>
        </div>
    </footer>

    <!-- Player Bar -->
    <div class="player-bar" id="playerBar">
        <div class="player-info">
            <img src="https://via.placeholder.com/50x50/1db954/ffffff?text=S" alt="Song cover" class="player-image" id="playerImage">
            <div class="player-text">
                <h4 id="playerTitle">Chưa có bài hát</h4>
                <p id="playerArtist">Chọn bài hát để phát</p>
            </div>
        </div>
        
        <div class="player-controls">
            <div class="control-buttons">
                <button class="control-btn"><i class="fas fa-random"></i></button>
                <button class="control-btn"><i class="fas fa-step-backward"></i></button>
                <button class="control-btn play-btn" onclick="togglePlay()"><i class="fas fa-play"></i></button>
                <button class="control-btn"><i class="fas fa-step-forward"></i></button>
                <button class="control-btn"><i class="fas fa-redo"></i></button>
            </div>
            
            <div class="progress-bar">
                <div class="progress"></div>
            </div>
        </div>
        
        <div class="player-extra">
            <div class="volume-container">
                <button class="control-btn"><i class="fas fa-volume-up"></i></button>
                <input type="range" min="0" max="100" value="70" class="volume-slider" onchange="changeVolume(this.value)">
            </div>
            <button class="control-btn"><i class="fas fa-expand"></i></button>
        </div>
    </div>

    <!-- Login Modal -->
    <div class="modal" id="loginModal">
        <div class="modal-content">
            <button class="close-btn" onclick="hideLoginModal()">&times;</button>
            <h2 class="modal-title">Đăng Nhập</h2>
            <form action="login.php" method="POST">
                <div class="form-group">
                    <label for="loginUsername">Tên đăng nhập</label>
                    <input type="text" id="loginUsername" name="username" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="loginPassword">Mật khẩu</label>
                    <input type="password" id="loginPassword" name="password" class="form-control" required>
                </div>
                <button type="submit" class="submit-btn">Đăng Nhập</button>
            </form>
            <div class="modal-switch">
                Chưa có tài khoản? <a onclick="showRegisterModal()">Đăng ký ngay</a>
            </div>
        </div>
    </div>

    <!-- Register Modal -->
    <div class="modal" id="registerModal">
        <div class="modal-content">
            <button class="close-btn" onclick="hideRegisterModal()">&times;</button>
            <h2 class="modal-title">Đăng Ký</h2>
            <form action="register.php" method="POST">
                <div class="form-group">
                    <label for="regUsername">Tên đăng nhập</label>
                    <input type="text" id="regUsername" name="username" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="regPhone">Số điện thoại</label>
                    <input type="tel" id="regPhone" name="phone" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="regPassword">Mật khẩu</label>
                    <input type="password" id="regPassword" name="password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="regConfirmPassword">Xác nhận mật khẩu</label>
                    <input type="password" id="regConfirmPassword" name="confirm_password" class="form-control" required>
                </div>
                <button type="submit" class="submit-btn">Đăng Ký</button>
            </form>
            <div class="modal-switch">
                Đã có tài khoản? <a onclick="showLoginModal()">Đăng nhập</a>
            </div>
        </div>
    </div>

    <script>
        // Player state
        let isPlaying = false;
        let currentSong = null;
        
        // Modal functions
        function showLoginModal() {
            document.getElementById('loginModal').style.display = 'flex';
            hideRegisterModal();
        }
        
        function hideLoginModal() {
            document.getElementById('loginModal').style.display = 'none';
        }
        
        function showRegisterModal() {
            document.getElementById('registerModal').style.display = 'flex';
            hideLoginModal();
        }
        
        function hideRegisterModal() {
            document.getElementById('registerModal').style.display = 'none';
        }
        
        // Close modals when clicking outside
        window.onclick = function(event) {
            if (event.target.id === 'loginModal') {
                hideLoginModal();
            }
            if (event.target.id === 'registerModal') {
                hideRegisterModal();
            }
        }
        
        // Player functions
        function playSong(songId, title, artist) {
            currentSong = { id: songId, title: title, artist: artist };
            document.getElementById('playerTitle').textContent = title;
            document.getElementById('playerArtist').textContent = artist;
            document.getElementById('playerImage').src = `https://via.placeholder.com/50x50/1db954/ffffff?text=${title.charAt(0)}`;
            
            // Send to server to record listening history
            <?php if(isset($_SESSION['user_id'])): ?>
                fetch('record_history.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `song_id=${songId}`
                });
            <?php endif; ?>
            
            // Start playing
            togglePlay(true);
        }
        
        function togglePlay(forcePlay = false) {
            const playBtn = document.querySelector('.play-btn i');
            if (forcePlay || !isPlaying) {
                playBtn.classList.remove('fa-play');
                playBtn.classList.add('fa-pause');
                isPlaying = true;
                document.getElementById('playerBar').style.display = 'flex';
                console.log('Đang phát nhạc');
            } else {
                playBtn.classList.remove('fa-pause');
                playBtn.classList.add('fa-play');
                isPlaying = false;
                console.log('Đã tạm dừng');
            }
        }
        
        function changeVolume(value) {
            console.log(`Âm lượng: ${value}%`);
        }
        
        function toggleFavorite(event, songId) {
            event.stopPropagation();
            const heartBtn = event.target.closest('button').querySelector('i');
            
            fetch('toggle_favorite.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `song_id=${songId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'added') {
                    heartBtn.classList.remove('far');
                    heartBtn.classList.add('fas');
                    heartBtn.style.color = '#1db954';
                    showNotification('Đã thêm vào yêu thích');
                } else if (data.status === 'removed') {
                    heartBtn.classList.remove('fas');
                    heartBtn.classList.add('far');
                    heartBtn.style.color = '';
                    showNotification('Đã xóa khỏi yêu thích');
                }
            });
        }
        
        function showNotification(message) {
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: #1db954;
                color: white;
                padding: 15px 25px;
                border-radius: 5px;
                z-index: 1000;
                animation: slideIn 0.3s ease;
            `;
            notification.textContent = message;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
        
        // Search functionality
        document.querySelector('.search-box').addEventListener('keyup', function(event) {
            if (event.key === 'Enter') {
                const query = this.value.trim();
                if (query) {
                    window.location.href = `search.php?q=${encodeURIComponent(query)}`;
                }
            }
        });
        
        // Display error/success messages
        <?php if(isset($_SESSION['error'])): ?>
            showNotification('<?php echo $_SESSION['error']; ?>');
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        
        <?php if(isset($_SESSION['success'])): ?>
            showNotification('<?php echo $_SESSION['success']; ?>');
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
    </script>
</body>
</html>
<?php $conn->close(); ?>
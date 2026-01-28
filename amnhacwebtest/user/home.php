<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../auth/check_login.php';
require_once '../partials/header.php';
require_once '../partials/sidebar.php';
require_once 'homeprocess.php';



if (isset($_GET['song_id'])) {
    foreach ($songList as $song) {
        if ($song['song_id'] == $_GET['song_id']) {
            $_SESSION['current_song'] = $song;
            break;
        }
    }
}

/* Ảnh mặc định khi thiếu cover */
$defaultCover = '../assets/images/default-cover.png';
?>

<style>
    .main-content {
        margin-left: 260px;
        padding: 80px 32px 120px 32px;
        width: calc(100% - 260px);
        min-height: 100vh;
        background: linear-gradient(to bottom, #1e1e1e, var(--bg-black) 40%);
    }

    .section-title {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 24px;
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
    }

    .section-title a {
        font-size: 12px;
        text-transform: uppercase;
        color: var(--text-sub);
        letter-spacing: 1px;
    }

    .section-title a:hover { text-decoration: underline; }

    .grid-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 24px;
        margin-bottom: 48px;
    }

    .song-card {
        background: #181818;
        padding: 16px;
        border-radius: 8px;
        transition: background 0.3s ease;
        cursor: pointer;
        position: relative;
    }

    .song-card:hover { background: #282828; }

    .card-img-wrapper {
        position: relative;
        width: 100%;
        padding-bottom: 100%;
        margin-bottom: 16px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.5);
        border-radius: 4px;
        overflow: hidden;
    }

    .card-img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .play-btn-overlay {
        position: absolute;
        bottom: 8px;
        right: 8px;
        width: 48px;
        height: 48px;
        background: var(--spotify-green);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #000;
        font-size: 20px;
        box-shadow: 0 8px 16px rgba(0,0,0,0.3);
        opacity: 0;
        transform: translateY(8px);
        transition: all 0.3s ease;
    }

    .song-card:hover .play-btn-overlay {
        opacity: 1;
        transform: translateY(0);
    }

    .card-title {
        font-weight: 700;
        font-size: 16px;
        margin-bottom: 8px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .card-subtitle {
        color: var(--text-sub);
        font-size: 14px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .list-container {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .list-item {
        display: flex;
        align-items: center;
        padding: 8px 16px;
        border-radius: 4px;
        transition: background 0.2s;
    }

    .list-item:hover { background: rgba(255,255,255,0.1); }

    .list-item img {
        width: 40px;
        height: 40px;
        border-radius: 4px;
        margin-right: 16px;
        object-fit: cover;
    }

    .artist-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: 24px;
    }

    .artist-card {
        background: #181818;
        padding: 16px;
        border-radius: 8px;
        text-align: center;
        transition: background 0.3s;
    }

    .artist-card:hover { background: #282828; }

    .artist-img {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        margin: 0 auto 16px;
        object-fit: cover;
        box-shadow: 0 8px 24px rgba(0,0,0,0.5);
    }

    .fav-btn {
        background: none;
        border: none;
        color: var(--text-sub);
        cursor: pointer;
        font-size: 16px;
        transition: color 0.2s;
    }

    .fav-btn:hover { color: #fff; transform: scale(1.1); }
    .fav-btn.active { color: var(--spotify-green) !important; }
    .fav-btn.active i { font-weight: 900; }

    /* Slider Styles */
    .slider-container {
        position: relative;
        width: 100%;
        height: 288px;
        border-radius: 12px;
        margin-bottom: 40px;
        overflow: hidden;
        /* bóng mờ nhẹ ở phía dưới*/
        box-shadow: 0 20px 50px -10px rgba(0, 0, 0, 0.8);
    }

    /* Hiệu ứng chuyển màu gradient giúp làm mờ phần dưới nội dung slide vào vùng bóng. */
    .slide::before {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 100px;
        background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
        z-index: 2;
        pointer-events: none;
    }

    .slider-wrapper {
        display: flex;
        width: 100%;
        height: 100%;
        transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .slide {
        min-width: 100%;
        height: 100%;
        position: relative;
        display: flex;
        align-items: center;
        padding: 0 60px;
        background: #121212;
    }

    .slide::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, #121212 20%, transparent 80%);
        z-index: 1;
    }

    .slide-content {
        position: relative;
        z-index: 2;
        max-width: 550px;
        /* Đã chuyển về căn chỉnh trái. */
    }

    .slide-tag {
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        background: var(--spotify-green);
        color: #000;
        padding: 4px 12px;
        border-radius: 40px;
        margin-bottom: 20px;
        display: inline-block;
        letter-spacing: 1px;
    }

    .slide-title {
        font-size: 30px;
        font-weight: 700;
        margin-bottom: 12px;
        line-height: 1.1;
        color: #fff;
    }

    .slide-desc {
        font-size: 16px;
        color: rgba(255,255,255,0.7);
        margin-bottom: 30px;
    }

    .slide-img {
        position: absolute;
        right: 0;
        top: 0;
        width: 60%;
        height: 100%;
        object-fit: cover;
        object-position: top center;
    }

    .slider-dots {
        position: absolute;
        bottom: 24px;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        gap: 8px;
        z-index: 3;
    }

    .dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: rgba(255,255,255,0.3);
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .dot.active {
        background: #fff;
        width: 24px;
        border-radius: 4px;
    }

    .btn-play-now {
        background: #fff;
        color: #000;
        padding: 12px 32px;
        border-radius: 30px;
        font-weight: 700;
        text-decoration: none;
        display: inline-block;
        transition: transform 0.2s;
        margin: 0 auto; /* Added for centering */
    }

    .btn-wrap {
        width: 100%;
        text-align: center; /* Centers the inline-block button */
        margin-top: 20px;
    }

    .btn-play-now:hover {
        transform: scale(1.05);
        background: var(--spotify-green);
    }


</style>

<main class="main-content">
    
    <!-- Hero Slider -->
    <div class="slider-container">
        <div class="slider-wrapper" id="sliderWrapper">
            <!-- Slide 1 -->
            <div class="slide">
                <img src="../assets/img/anh-son-tung-mtp-10.jpg" class="slide-img" alt="Âm thầm bên em">
                <div class="slide-content">
                    <div class="slide-tag">Thịnh hành</div>
                    <h1 class="slide-title">Âm Thầm Bên Em</h1>
                    <p class="slide-desc">Một bản hít đình đám mang đậm phong cách Sơn Tùng M-TP.</p>
                    <div class="btn-wrap">
                        <a href="#" class="btn-play-now">Nghe ngay</a>
                    </div>
                </div>
            </div>
            <!-- Slide 2 -->
            <div class="slide">
                <img src="../assets/img/anh-son-tung-mtp-12.jpg" class="slide-img" alt="Buông đôi tay nhau ra">
                <div class="slide-content">
                    <div class="slide-tag">Đề xuất</div>
                    <h1 class="slide-title">Buông Đôi Tay Nhau Ra</h1>
                    <p class="slide-desc">Cùng thưởng thức giai điệu bắt tai và ca từ ý nghĩa.</p>
                    <div class="btn-wrap">
                        <a href="#" class="btn-play-now">Nghe ngay</a>
                    </div>
                </div>
            </div>
            <!-- Slide 3 -->
            <div class="slide">
                <img src="../assets/img/anh-son-tung-mtp-13.jpg" class="slide-img" alt="Phép màu">
                <div class="slide-content">
                    <div class="slide-tag">Mới phát hành</div>
                    <h1 class="slide-title">Phép Màu</h1>
                    <p class="slide-desc">Giai điệu diệu kỳ xoa dịu tâm hồn bạn mỗi ngày.</p>
                    <div class="btn-wrap">
                        <a href="#" class="btn-play-now">Nghe ngay</a>
                    </div>
                </div>
            </div>
            <!-- Slide 4 -->
            <div class="slide">
                <img src="../assets/img/sontungmtp.jpg" class="slide-img" alt="Mất kết nối">
                <div class="slide-content">
                    <div class="slide-tag">Hot hit</div>
                    <h1 class="slide-title">Mất Kết Nối</h1>
                    <p class="slide-desc">Đừng để âm nhạc của bạn bị gián đoạn, hãy nghe ngay!</p>
                    <div class="btn-wrap">
                        <a href="#" class="btn-play-now">Nghe ngay</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="slider-dots" id="sliderDots">
            <div class="dot active" onclick="goToSlide(0)"></div>
            <div class="dot" onclick="goToSlide(1)"></div>
            <div class="dot" onclick="goToSlide(2)"></div>
            <div class="dot" onclick="goToSlide(3)"></div>
        </div>
    </div>

    <div class="section-title">
        <span>Danh sách bài hát</span>
        <a href="#">Xem tất cả</a>
    </div>

    <div class="grid-container">
        <?php foreach ($songList as $song): ?>
            <?php
                $cover = (!empty($song['cover_image'])) ? $song['cover_image'] : $defaultCover;
            ?>
            <div class="song-card" onclick="window.location.href='home.php?song_id=<?= $song['song_id'] ?>'">
                <div class="card-img-wrapper">
                    <img src="<?= htmlspecialchars($cover) ?>" class="card-img" alt="cover">
                    <div class="play-btn-overlay">
                        <i class="fa-solid fa-play"></i>
                    </div>
                </div>
                <div class="card-title"><?= htmlspecialchars($song['title']) ?></div>
                <div class="card-subtitle" style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        <?= htmlspecialchars($song['artist_name'] ?? 'Nghệ sĩ') ?>
                    </span>
                    <button class="fav-btn <?= ($song['is_favorite'] > 0) ? 'active' : '' ?>" 
                            onclick="toggleFavorite(event, <?= $song['song_id'] ?>, this)" 
                            title="<?= ($song['is_favorite'] > 0) ? 'Xóa khỏi yêu thích' : 'Thêm vào yêu thích' ?>">
                        <i class="<?= ($song['is_favorite'] > 0) ? 'fa-solid' : 'fa-regular' ?> fa-heart"></i>
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="section-title">
        <span>Bài hát thịnh hành</span>
        <a href="#">Khám phá thêm</a>
    </div>

    <div class="list-container">
        <?php foreach ($trendingSongs as $song): ?>
            <?php
                $cover = (!empty($song['cover_image'])) ? $song['cover_image'] : $defaultCover;
            ?>
            <div class="list-item">
                <img src="<?= htmlspecialchars($cover) ?>" alt="cover">
                <div style="flex: 1;">
                    <div style="font-weight: 600;"><?= htmlspecialchars($song['title']) ?></div>
                    <div style="font-size: 13px; color: var(--text-sub);">
                        <?= htmlspecialchars($song['artist_name']) ?>
                    </div>
                </div>
                <div style="color: var(--text-sub); font-size: 13px; margin: 0 20px;">
                    <i class="fa-solid fa-chart-line" style="margin-right: 8px;"></i> Trending
                </div>
                <button onclick="window.location.href='home.php?song_id=<?= $song['song_id'] ?>'" 
                        style="background:none;border:none;color:#fff;cursor:pointer;font-size:18px;">
                    <i class="fa-solid fa-play"></i>
                </button>
            </div>
        <?php endforeach; ?>
    </div>

        <h2 class="section-title" style="margin-top: 48px;">Nghệ sĩ phổ biến</h2> 

        <div class="artist-grid">
            <?php foreach ($popularArtists as $artist): ?>
                <?php $avatar = (!empty($artist['avatar'])) ? '../' . $artist['avatar'] : null; ?>
                <div class="artist-card" onclick="window.location.href='../page/artist_info.php?id=<?= $artist['user_id'] ?>'">
                    <?php if ($avatar): ?>
                        <img src="<?= htmlspecialchars($avatar) ?>" class="artist-img" alt="avatar">
                    <?php else: ?>
                        <div class="artist-img" style="background: #282828; display: flex; align-items: center; justify-content: center;">
                            <i class="fa-solid fa-user" style="font-size: 48px; color: #535353;"></i>
                        </div>
                    <?php endif; ?>
                    <div style="font-weight: 700; margin-bottom: 4px;"><?= htmlspecialchars($artist['username']) ?></div>
                    <div style="font-size: 13px; color: var(--text-sub);">Nghệ sĩ</div>
                </div>
            <?php endforeach; ?>
        </div>
   
</main>

<?php require_once '../partials/player.php'; ?>

<script>
    let currentSlide = 0;
    const slides = document.querySelectorAll('.slide');
    const wrapper = document.getElementById('sliderWrapper');
    const dots = document.querySelectorAll('.dot');
    const totalSlides = slides.length;

    function updateSlider() {
        wrapper.style.transform = `translateX(-${currentSlide * 100}%)`;
        dots.forEach((dot, index) => {
            dot.classList.toggle('active', index === currentSlide);
        });
    }

    function goToSlide(index) {
        currentSlide = index;
        updateSlider();
    }

    function nextSlide() {
        currentSlide = (currentSlide + 1) % totalSlides;
        updateSlider();
    }

    // Auto slide every 5 seconds
    let slideInterval = setInterval(nextSlide, 2000);

    // Pause on hover
    const sliderContainer = document.querySelector('.slider-container');
    sliderContainer.addEventListener('mouseenter', () => clearInterval(slideInterval));
    sliderContainer.addEventListener('mouseleave', () => {
        slideInterval = setInterval(nextSlide, 2000);
    });
    function toggleFavorite(event, songId, button) {
        event.stopPropagation();
        
        const formData = new FormData();
        formData.append('song_id', songId);

        fetch('../favorite/toggle_favorite.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const icon = button.querySelector('i');
                if (data.action === 'added') {
                    button.classList.add('active');
                    button.title = 'Xóa khỏi yêu thích';
                    icon.classList.remove('fa-regular');
                    icon.classList.add('fa-solid');
                } else {
                    button.classList.remove('active');
                    button.title = 'Thêm vào yêu thích';
                    icon.classList.remove('fa-solid');
                    icon.classList.add('fa-regular');
                }
            } else if (data.message === 'Please login first') {
                window.location.href = '../auth/login_form.php';
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
</script>

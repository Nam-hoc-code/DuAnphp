<?php
require_once '../partials/header.php';
require_once '../partials/sidebar.php';

$results = [];
$keyword = '';

if (isset($_GET['q']) && trim($_GET['q']) !== '') {
    require_once 'searchprocess.php';
}
?>

<style>
    .search-content {
        margin-left: 260px;
        padding: 80px 32px 120px 32px;
        width: calc(100% - 260px);
        min-height: 100vh;
    }

    .search-box-container {
        position: sticky;
        top: 80px;
        z-index: 90;
        margin-bottom: 40px;
    }

    .search-form {
        position: relative;
        max-width: 400px;
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

    .search-icon {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: #000;
        font-size: 18px;
    }

    .result-section h2 { font-size: 1.5rem; font-weight: 700; margin-bottom: 24px; }

    .search-table {
        width: 100%;
        border-collapse: collapse;
    }

    .search-table th {
        text-align: left;
        color: var(--text-sub);
        font-size: 12px;
        text-transform: uppercase;
        padding: 12px 16px;
        border-bottom: 1px solid #282828;
    }

    .search-table td {
        padding: 12px 16px;
        border-bottom: 1px solid transparent;
    }

    .search-row:hover { background: rgba(255,255,255,0.1); border-radius: 4px; }
    .search-row:hover td { border-bottom-color: transparent; }

    .song-title { font-weight: 600; color: #fff; display: block; }
    .song-artist { color: var(--text-sub); font-size: 13px; }

    .btn-play-small {
        background: var(--spotify-green);
        color: #000;
        border: none;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: transform 0.2s;
    }
    .btn-play-small:hover { transform: scale(1.1); background: #1ed760; }
</style>

<main class="search-content">
     <!-- <div class="search-box-container">
        <form method="GET" action="search.php" class="search-form">
            <i class="fa-solid fa-magnifying-glass search-icon"></i>
            <input type="text"
                   name="q"
                   class="search-input"
                   placeholder="Bạn muốn nghe gì?"
                   value="<?= htmlspecialchars($keyword) ?>"
                   required>
        </form>
    </div>  -->

    <div class="result-section">
        <?php if (!empty($keyword)): ?>
            <h2>Kết quả tìm kiếm cho: "<?= htmlspecialchars($keyword) ?>"</h2>
            
            <?php if (!empty($results)): ?>
                <table class="search-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Tiêu đề</th>
                            <th>Nghệ sĩ</th>
                            <th style="text-align: right;">Phát</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $index => $song): ?>
                        <tr class="search-row">
                            <td style="color: var(--text-sub);"><?= $index + 1 ?></td>
                            <td>
                                <span class="song-title"><?= htmlspecialchars($song['title']) ?></span>
                            </td>
                            <td>
                                <span class="song-artist"><?= htmlspecialchars($song['artist_name']) ?></span>
                            </td>
                            <td style="text-align: right;">
                                <button class="btn-play-small" onclick="window.location.href='../user/home.php?song_id=<?= $song['song_id'] ?>'">
                                    <i class="fa-solid fa-play"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div style="text-align: center; padding: 60px; color: var(--text-sub);">
                    <i class="fa-solid fa-face-frown" style="font-size: 48px; margin-bottom: 20px;"></i>
                    <p>Rất tiếc, chúng tôi không tìm thấy kết quả phù hợp cho "<?= htmlspecialchars($keyword) ?>".</p>
                    <p style="font-size: 13px;">Vui lòng kiểm tra lại chính tả hoặc thử bằng từ khóa khác.</p>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div style="text-align: center; padding: 100px 0; color: var(--text-sub);">
                <i class="fa-solid fa-music" style="font-size: 80px; margin-bottom: 30px; opacity: 0.1;"></i>
                <h2>Tìm kiếm bài hát yêu thích của bạn</h2>
                <p>Khám phá âm nhạc theo tên bài hát hoặc nghệ sĩ</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php require_once '../partials/player.php'; ?>

<?php
require_once "check_artist.php";
require_once "../config/database.php";
$db = new Database();
$conn = $db->connect();
$artist_id = $_SESSION['user']['id'];
$sql = "SELECT * FROM songs WHERE artist_id = ? AND is_deleted = 0 ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $artist_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <style>
        body { background: #121212; color: white; font-family: sans-serif; padding: 40px; margin: 0; }
        .container { max-width: 1000px; margin: auto; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; background: #181818; border-radius: 8px; overflow: hidden; }
        th { text-align: left; padding: 16px; color: #b3b3b3; border-bottom: 1px solid #282828; font-size: 0.8rem; text-transform: uppercase; }
        td { padding: 16px; border-bottom: 1px solid #282828; vertical-align: middle; }
        .cover-img { width: 50px; height: 50px; border-radius: 4px; object-fit: cover; }
        .status-badge { padding: 4px 12px; border-radius: 4px; font-size: 0.7rem; font-weight: bold; background: #333; }
        .pending { color: #f1c40f; }
        .btn-del { color: #ff4d4d; text-decoration: none; font-size: 0.8rem; }
        .btn-send { color: #1DB954; text-decoration: none; font-size: 0.8rem; font-weight: bold; }
        audio { height: 30px; filter: invert(100%); opacity: 0.7; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h2>Bài hát của tôi</h2>
        <a href="artist_view.php" style="color: #b3b3b3; text-decoration: none;">⬅ Dashboard</a>
    </div>
    <table>
        <thead>
            <tr><th>Bìa</th><th>Tiêu đề</th><th>Nghe thử</th><th>Trạng thái</th><th>Hành động</th></tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><img src="<?= $row['cover_image'] ?>" class="cover-img"></td>
                <td style="font-weight: bold;"><?= htmlspecialchars($row['title']) ?></td>
                <td><audio controls src="<?= $row['cloud_url'] ?>"></audio></td>
                <td><span class="status-badge <?= $row['status'] === 'PENDING' ? 'pending' : '' ?>"><?= $row['status'] ?></span></td>
                <td>
                    <?php if ($row['status'] === 'PENDING'): ?>
                        <a href="send_request.php?id=<?= $row['song_id'] ?>" class="btn-send">Gửi duyệt</a> |
                    <?php endif; ?>
                    <a href="delete_song.php?id=<?= $row['song_id'] ?>" class="btn-del" onclick="return confirm('Xóa bài hát?')">Xóa</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>

<?php
require_once "check_artist.php";
require_once "../config/database.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user']['id'])) {
    die("Chưa đăng nhập");
}

$artist_id = (int) $_SESSION['user']['id'];

$db = new Database();
$conn = $db->connect();

/* =========================
   1️⃣ ĐƠN HÀNG ĐĨA
========================= */
$sql_orders = "
    SELECT 
        o.order_id,
        u.username AS buyer,
        s.title AS disc_name,
        d.price,
        o.status,
        o.created_at
    FROM disc_orders o
    JOIN discs d ON o.disc_id = d.disc_id
    JOIN songs s ON d.song_id = s.song_id
    JOIN users u ON o.user_id = u.user_id
    WHERE s.artist_id = ?
    ORDER BY o.created_at DESC
";
$stmt = $conn->prepare($sql_orders);
$stmt->bind_param("i", $artist_id);
$stmt->execute();
$result_orders = $stmt->get_result();

/* =========================
   2️⃣ SONGS (FORM THÊM ĐĨA)
========================= */
$sql_songs = "
    SELECT song_id, title
    FROM songs
    WHERE artist_id = ? AND is_deleted = 0
";
$stmt_songs = $conn->prepare($sql_songs);
$stmt_songs->bind_param("i", $artist_id);
$stmt_songs->execute();
$result_songs = $stmt_songs->get_result();

/* =========================
   3️⃣ DISCS HIỆN CÓ
========================= */
$sql_discs = "
    SELECT 
        d.disc_id,
        s.title AS song_title,
        d.price,
        (
            SELECT COUNT(*) 
            FROM disc_orders o 
            WHERE o.disc_id = d.disc_id
        ) AS order_count
    FROM discs d
    JOIN songs s ON d.song_id = s.song_id
    WHERE s.artist_id = ?
    ORDER BY d.disc_id DESC
";
$stmt_discs = $conn->prepare($sql_discs);
$stmt_discs->bind_param("i", $artist_id);
$stmt_discs->execute();
$result_discs = $stmt_discs->get_result();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý bán đĩa - Artist</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { background:#000; color:#fff; font-family:'Outfit',sans-serif; }
        table { width:100%; border-collapse:collapse; }
        th,td { padding:12px; border-bottom:1px solid #222; }
        th { color:#b3b3b3; text-transform:uppercase; font-size:13px; }
        .status-pending { color:#ffc107; }
        .status-confirmed { color:#00aaff; }
        .status-shipping { color:#ff5722; }
        .status-done { color:#1db954; }
        .btn { background:#1db954; border:none; padding:8px 16px; cursor:pointer; }
        .btn-danger { background:#f15555; }
    </style>
</head>
<body>

<h1>🎧 Dashboard Bán Đĩa</h1>

<!-- =======================
     ĐƠN HÀNG
======================= -->
<h2>🧾 Đơn hàng đĩa của tôi</h2>

<?php if ($result_orders->num_rows > 0): ?>
<table>
    <thead>
        <tr>
            <th>Mã đơn</th>
            <th>Tên đĩa</th>
            <th>Người mua</th>
            <th>Giá</th>
            <th>Trạng thái</th>
            <th>Thời gian</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
    <?php while ($row = $result_orders->fetch_assoc()): ?>
        <tr>
            <td>#<?= $row['order_id'] ?></td>
            <td><?= htmlspecialchars($row['disc_name']) ?></td>
            <td><?= htmlspecialchars($row['buyer']) ?></td>
            <td><?= number_format($row['price']) ?> VNĐ</td>
            <td class="status-<?= $row['status'] ?>">
                <?= $row['status'] ?>
            </td>
            <td><?= $row['created_at'] ?></td>
            <td>
                <?php if ($row['status'] === 'pending'): ?>
                    <form method="POST" action="update_order_status.php">
                        <input type="hidden" name="order_id" value="<?= $row['order_id'] ?>">
                        <input type="hidden" name="status" value="confirmed">
                        <button class="btn">Xác nhận</button>
                    </form>
                <?php elseif ($row['status'] === 'confirmed'): ?>
                    <form method="POST" action="update_order_status.php">
                        <input type="hidden" name="order_id" value="<?= $row['order_id'] ?>">
                        <input type="hidden" name="status" value="shipping">
                        <button class="btn">Giao hàng</button>
                    </form>
                <?php elseif ($row['status'] === 'shipping'): ?>
                    <form method="POST" action="update_order_status.php">
                        <input type="hidden" name="order_id" value="<?= $row['order_id'] ?>">
                        <input type="hidden" name="status" value="done">
                        <button class="btn">Hoàn tất</button>
                    </form>
                <?php else: ?>
                    —
                <?php endif; ?>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>
<?php else: ?>
<p>Chưa có đơn hàng nào.</p>
<?php endif; ?>

<hr>

<!-- =======================
     THÊM ĐĨA
======================= -->
<h2>➕ Thêm đĩa mới</h2>

<form action="add_disc_process.php" method="POST">
    <label>Bài hát:</label>
    <select name="song_id" required>
        <?php while ($song = $result_songs->fetch_assoc()): ?>
            <option value="<?= $song['song_id'] ?>">
                <?= htmlspecialchars($song['title']) ?>
            </option>
        <?php endwhile; ?>
    </select>
    <br><br>

    <label>Giá:</label>
    <input type="number" name="price" min="1000" required>
    <br><br>

    <button class="btn">Thêm đĩa</button>
</form>

<hr>

<!-- =======================
     ĐĨA HIỆN CÓ
======================= -->
<h2>💿 Đĩa hiện có</h2>

<?php if ($result_discs->num_rows > 0): ?>
<table>
    <thead>
        <tr>
            <th>Bài hát</th>
            <th>Giá</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
    <?php while ($row = $result_discs->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['song_title']) ?></td>
            <td><?= number_format($row['price']) ?> VNĐ</td>
            <td>
                <?= $row['order_count'] > 0 ? "🔒 Đã có đơn" : "🟢 Chưa bán" ?>
            </td>
            <td>
                <?php if ($row['order_count'] == 0): ?>
                    <form action="delete_disc.php" method="POST" onsubmit="return confirm('Xóa đĩa này?')">
                        <input type="hidden" name="disc_id" value="<?= $row['disc_id'] ?>">
                        <button class="btn btn-danger">Xóa</button>
                    </form>
                <?php else: ?>
                    —
                <?php endif; ?>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>
<?php else: ?>
<p>Chưa có đĩa nào.</p>
<?php endif; ?>

</body>
</html>

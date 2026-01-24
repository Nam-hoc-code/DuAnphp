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
?>

<h2>🧾 Đơn hàng đĩa của tôi</h2>

<?php
/* =========================
   1️⃣ DANH SÁCH ĐƠN HÀNG
========================= */
$sql = "
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



$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $artist_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<p>Chưa có đơn hàng nào.</p>";
} else {
    echo "<table border='1' cellpadding='10'>
        <tr>
            <th>Mã đơn</th>
            <th>Tên đĩa</th>
            <th>Người mua</th>
            <th>Giá</th>
            <th>Trạng thái</th>
            <th>Thời gian</th>
            <th>Hành động</th>
        </tr>";

    while ($row = $result->fetch_assoc()) {

    // Mapping trạng thái cho dễ nhìn
    switch ($row['status']) {
        case 'pending':
            $statusText = "🕒 Chờ xác nhận";
            break;
        case 'confirmed':
            $statusText = "📦 Đã xác nhận";
            break;
        case 'shipping':
            $statusText = "🚚 Đang giao";
            break;
        case 'done':
            $statusText = "✅ Hoàn tất";
            break;
        default:
            $statusText = htmlspecialchars($row['status']);
    }

    // ===== HÀNH ĐỘNG THEO TRẠNG THÁI =====
    if ($row['status'] === 'pending') {
        $action = '
            <form action="update_order_status.php" method="POST">
                <input type="hidden" name="order_id" value="' . $row['order_id'] . '">
                <input type="hidden" name="status" value="confirmed">
                <button type="submit">✔ Xác nhận</button>
            </form>
        ';
    } elseif ($row['status'] === 'confirmed') {
        $action = '
            <form action="update_order_status.php" method="POST">
                <input type="hidden" name="order_id" value="' . $row['order_id'] . '">
                <input type="hidden" name="status" value="shipping">
                <button type="submit">🚚 Giao hàng</button>
            </form>
        ';
    } elseif ($row['status'] === 'shipping') {
        $action = '
            <form action="update_order_status.php" method="POST">
                <input type="hidden" name="order_id" value="' . $row['order_id'] . '">
                <input type="hidden" name="status" value="done">
                <button type="submit">✅ Hoàn tất</button>
            </form>
        ';
    } else {
        $action = '—';
    }

    echo "
    <tr>
        <td>#{$row['order_id']}</td>
        <td>" . htmlspecialchars($row['disc_name']) . "</td>
        <td>" . htmlspecialchars($row['buyer']) . "</td>
        <td>" . number_format($row['price']) . " VNĐ</td>
        <td>{$statusText}</td>
        <td>{$row['created_at']}</td>
        <td>{$action}</td>
    </tr>
    ";
}


    echo "</table>";
}
?>

<hr>

<h2>➕ Thêm đĩa mới</h2>

<?php
/* =========================
   2️⃣ FORM THÊM ĐĨA (THEO ĐĨA – ĐÚNG THỰC TẾ)
========================= */
?>
<?php
$sql = "
    SELECT song_id, title
    FROM songs
    WHERE artist_id = ? AND is_deleted = 0
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $artist_id);
$stmt->execute();
$songs = $stmt->get_result();
?>

<form action="add_disc_process.php" method="POST">

    <label>Bài hát trong đĩa:</label><br>
    <select name="song_id" required>
        <?php while ($song = $songs->fetch_assoc()): ?>
            <option value="<?= $song['song_id'] ?>">
                <?= htmlspecialchars($song['title']) ?>
            </option>
        <?php endwhile; ?>
    </select>
    <br><br>

    <label>Giá đĩa (VNĐ):</label><br>
    <input type="number" name="price" min="1000" required>
    <br><br>

    <button type="submit">💿 Thêm đĩa</button>
</form>


<hr>
<h2>💿 Đĩa hiện có của tôi</h2>

<?php
$sql = "
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

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $artist_id);
$stmt->execute();
$discs = $stmt->get_result();

if ($discs->num_rows === 0) {
    echo "<p>Chưa có đĩa nào.</p>";
} else {
    echo "<table border='1' cellpadding='10'>
        <tr>
            <th>Bài hát</th>
            <th>Giá</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
        </tr>";

    while ($row = $discs->fetch_assoc()) {

        if ($row['order_count'] > 0) {
            $status = "🔒 Đã có đơn";
            $action = "—";
        } else {
            $status = "🟢 Chưa bán";
            $action = '
                <form action="delete_disc.php" method="POST" onsubmit="return confirm(\'Xóa đĩa này?\')">
                    <input type="hidden" name="disc_id" value="'.$row['disc_id'].'">
                    <button type="submit">❌ Xóa</button>
                </form>
            ';
        }

        echo "
        <tr>
            <td>".htmlspecialchars($row['song_title'])."</td>
            <td>".number_format($row['price'])." VNĐ</td>
            <td>{$status}</td>
            <td>{$action}</td>
        </tr>
        ";
    }

    echo "</table>";
}
?>

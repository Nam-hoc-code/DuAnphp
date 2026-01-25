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

// 1. Fetch Orders (Danh sách đơn hàng đĩa của tôi)
$sql_orders = "
<?php
/* =========================
   1️⃣ DANH SÁCH ĐƠN HÀNG
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
$stmt = $conn->prepare($sql_orders);
if (!$stmt) {
    // Fallback if schema uses 'id' instead of 'order_id'
    $sql_orders = str_replace("o.order_id", "o.id as order_id", $sql_orders);
    $stmt = $conn->prepare($sql_orders);
}
$stmt->bind_param("i", $artist_id);
$stmt->execute();
$result_orders = $stmt->get_result();

// 2. Fetch Songs for "Add Disc" dropdown
$sql_songs = "



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
?>
<?php
$sql = "
    SELECT song_id, title
    FROM songs
    WHERE artist_id = ? AND is_deleted = 0
";
$stmt_songs = $conn->prepare($sql_songs);
$stmt_songs->bind_param("i", $artist_id);
$stmt_songs->execute();
$result_songs = $stmt_songs->get_result();

// 3. Fetch Existing Discs (Đĩa hiện có của tôi)
$sql_discs = "
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
$stmt_discs = $conn->prepare($sql_discs);
$stmt_discs->bind_param("i", $artist_id);
$stmt_discs->execute();
$result_discs = $stmt_discs->get_result();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Đĩa & Đơn hàng - Artist Dashboard</title>
    <!-- Fonts & Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --spotify-black: #000000;
            --spotify-dark: #121212;
            --spotify-card: #181818;
            --spotify-grey: #282828;
            --spotify-green: #1DB954;
            --spotify-soft-green: #1ed760;
            --text-main: #ffffff;
            --text-sub: #b3b3b3;
            --danger-red: #f15555;
            --accent-blue: #00DBFF;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Outfit', sans-serif;
        }

        body {
            background-color: var(--spotify-black);
            color: var(--text-main);
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 260px;
            background-color: #000000;
            padding: 24px 12px;
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            border-right: 1px solid #1f1f1f;
        }

        .logo-box {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 12px 32px 12px;
            font-size: 24px;
            font-weight: 700;
            color: var(--text-main);
            text-decoration: none;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 12px 16px;
            color: var(--text-sub);
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            border-radius: 6px;
            transition: all 0.3s ease;
            margin-bottom: 4px;
        }

        .nav-link:hover {
            color: #fff;
            background-color: var(--spotify-grey);
        }

        .nav-link.active {
            color: #fff;
            background-color: var(--spotify-grey);
        }

        .nav-link i { font-size: 20px; width: 24px; text-align: center; }

        .nav-link.logout { color: var(--danger-red); margin-top: auto; }
        .nav-link.logout:hover { background-color: rgba(241, 85, 85, 0.1); }

        /* Main Content */
        .main-wrapper {
            margin-left: 260px;
            flex-grow: 1;
            padding: 40px;
            max-width: 1200px;
        }

        header h1 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 40px;
            letter-spacing: -0.5px;
        }

        .content-section {
            margin-bottom: 50px;
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .section-title {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .section-title i { color: var(--spotify-green); }

        /* Tables */
        .card-container {
            background-color: var(--spotify-card);
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            border: 1px solid #222;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            padding: 14px 16px;
            color: var(--text-sub);
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            font-weight: 700;
            border-bottom: 1px solid #282828;
        }

        td {
            padding: 16px;
            border-bottom: 1px solid #222;
            font-size: 15px;
            vertical-align: middle;
        }

        tr:last-child td { border-bottom: none; }

        tr:hover td { background-color: rgba(255,255,255,0.03); }

        .order-id-chip {
            color: var(--spotify-green);
            font-weight: 700;
            font-size: 14px;
        }

        .price-text {
            font-weight: 700;
            color: #fff;
        }

        .time-text {
            color: var(--text-sub);
            font-size: 13px;
        }

        .status-pill {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .status-pending { background: rgba(255, 193, 7, 0.1); color: #FFC107; }
        .status-confirmed { background: rgba(0, 153, 255, 0.1); color: #0099FF; }
        .status-shipping { background: rgba(255, 87, 34, 0.1); color: #FF5722; }
        .status-done { background: rgba(29, 185, 84, 0.1); color: var(--spotify-green); }

        .status-available { color: var(--spotify-green); display: flex; align-items: center; gap: 8px; font-weight: 600; }
        .status-ordered { color: #f1c40f; display: flex; align-items: center; gap: 8px; font-weight: 600; }

        /* Buttons */
        .btn-spotify {
            background-color: var(--spotify-green);
            color: #000;
            border: none;
            padding: 10px 20px;
            border-radius: 500px;
            font-weight: 700;
            font-size: 13px;
            cursor: pointer;
            transition: transform 0.2s, background-color 0.2s;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-spotify:hover {
            transform: scale(1.05);
            background-color: var(--spotify-soft-green);
        }

        .btn-outline-danger {
            background: transparent;
            color: var(--danger-red);
            border: 1px solid var(--danger-red);
            padding: 8px 16px;
            border-radius: 500px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            text-transform: uppercase;
        }

        .btn-outline-danger:hover {
            background: var(--danger-red);
            color: #fff;
        }

        /* Form Styling */
        .modern-form {
            display: grid;
            grid-template-columns: 1.5fr 1fr auto;
            gap: 24px;
            align-items: end;
        }

        .input-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .input-group label {
            font-weight: 600;
            color: var(--text-sub);
            font-size: 14px;
        }

        select, input[type="number"] {
            background-color: var(--spotify-grey);
            border: 1px solid transparent;
            color: #fff;
            padding: 14px 18px;
            border-radius: 8px;
            font-size: 15px;
            outline: none;
            transition: border-color 0.3s;
        }

        select:focus, input[type="number"]:focus {
            border-color: #555;
        }

        .empty-view {
            text-align: center;
            padding: 40px;
            color: var(--text-sub);
        }

        .empty-view i {
            display: block;
            font-size: 40px;
            margin-bottom: 16px;
            opacity: 0.3;
        }
    </style>
</head>
<body>

    <!-- Sidebar Navigation -->
    <aside class="sidebar">
        <a href="artist_view.php" class="logo-box">
            <i class="fa-brands fa-spotify" style="color: var(--spotify-green); font-size: 36px;"></i>
            <span>Artist Studio</span>
        </a>
        <nav>
            <a href="artist_view.php" class="nav-link"><i class="fa-solid fa-house"></i> Trang chủ</a>
            <a href="my_songs.php" class="nav-link"><i class="fa-solid fa-music"></i> Kho bài hát</a>
            <a href="add_song.php" class="nav-link"><i class="fa-solid fa-circle-plus"></i> Upload nhạc mới</a>
            <a href="oders.php" class="nav-link active"><i class="fa-solid fa-cart-shopping"></i> Quản lý bán đĩa</a>
        </nav>
        <a href="../auth/logout.php" class="nav-link logout"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a>
    </aside>

    <!-- Main Content -->
    <div class="main-wrapper">
        <header>
            <h1><i class="fa-solid fa-compact-disc"></i> Dashboard Bán Đĩa</h1>
        </header>

        <!-- Section 1: Đơn hàng đĩa của tôi (Lấy từ ảnh: Mã đơn, Tên đĩa, Người mua, Giá, Trạng thái, Thời gian, Hành động) -->
        <section class="content-section">
            <h2 class="section-title"><i class="fa-solid fa-file-invoice-dollar"></i> Đơn hàng đĩa của tôi</h2>
            <div class="card-container">
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
                            <?php while ($row = $result_orders->fetch_assoc()): 
                                $statusClass = 'status-' . $row['status'];
                                $statusText = "";
                                switch ($row['status']) {
                                    case 'pending': $statusText = "🕒 Chờ xác nhận"; break;
                                    case 'confirmed': $statusText = "📦 Đã xác nhận"; break;
                                    case 'shipping': $statusText = "🚚 Đang giao"; break;
                                    case 'done': $statusText = "✅ Hoàn tất"; break;
                                    default: $statusText = $row['status'];
                                }
                            ?>
                                <tr>
                                    <td class="order-id-chip">#<?= $row['order_id'] ?></td>
                                    <td style="font-weight: 600;"><?= htmlspecialchars($row['disc_name']) ?></td>
                                    <td><?= htmlspecialchars($row['buyer']) ?></td>
                                    <td class="price-text"><?= number_format($row['price'], 0, ',', '.') ?> VNĐ</td>
                                    <td><span class="status-pill <?= $statusClass ?>"><?= $statusText ?></span></td>
                                    <td class="time-text"><?= date('Y-m-d H:i:s', strtotime($row['created_at'])) ?></td>
                                    <td>
                                        <?php if ($row['status'] === 'pending'): ?>
                                            <form action="update_order_status.php" method="POST">
                                                <input type="hidden" name="order_id" value="<?= $row['order_id'] ?>">
                                                <input type="hidden" name="status" value="confirmed">
                                                <button type="submit" class="btn-spotify">Xác nhận</button>
                                            </form>
                                        <?php elseif ($row['status'] === 'confirmed'): ?>
                                            <form action="update_order_status.php" method="POST">
                                                <input type="hidden" name="order_id" value="<?= $row['order_id'] ?>">
                                                <input type="hidden" name="status" value="shipping">
                                                <button type="submit" class="btn-spotify">Giao hàng</button>
                                            </form>
                                        <?php elseif ($row['status'] === 'shipping'): ?>
                                            <form action="update_order_status.php" method="POST">
                                                <input type="hidden" name="order_id" value="<?= $row['order_id'] ?>">
                                                <input type="hidden" name="status" value="done">
                                                <button type="submit" class="btn-spotify">Hoàn tất</button>
                                            </form>
                                        <?php else: ?>
                                            <span style="color: var(--text-sub);">—</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-view">
                        <i class="fa-solid fa-inbox"></i>
                        <p>Bạn chưa có đơn hàng nào.</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Section 2: Thêm đĩa mới -->
        <section class="content-section">
            <h2 class="section-title"><i class="fa-solid fa-plus-circle"></i> Thêm đĩa mới</h2>
            <div class="card-container">
                <form action="add_disc_process.php" method="POST" class="modern-form">
                    <div class="input-group">
                        <label>Bài hát trong đĩa:</label>
                        <select name="song_id" required>
                            <option value="" disabled selected>Chọn bài hát của bạn...</option>
                            <?php while ($song = $result_songs->fetch_assoc()): ?>
                                <option value="<?= $song['song_id'] ?>"><?= htmlspecialchars($song['title']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="input-group">
                        <label>Giá đĩa (VNĐ):</label>
                        <input type="number" name="price" placeholder="Ví dụ: 20000" min="1000" required>
                    </div>
                    <button type="submit" class="btn-spotify" style="height: 52px; padding: 0 40px; border-radius: 8px;">
                        <i class="fa-solid fa-compact-disc"></i> Thêm đĩa
                    </button>
                </form>
            </div>
        </section>

        <!-- Section 3: Đĩa hiện có của tôi (Lấy từ ảnh: Bài hát, Giá, Trạng thái, Hành động) -->
        <section class="content-section">
            <h2 class="section-title"><i class="fa-solid fa-layer-group"></i> Đĩa hiện có của tôi</h2>
            <div class="card-container" style="margin-bottom: 80px;">
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
                                    <td style="font-weight: 600;"><?= htmlspecialchars($row['song_title']) ?></td>
                                    <td class="price-text"><?= number_format($row['price'], 0, ',', '.') ?> VNĐ</td>
                                    <td>
                                        <?php if ($row['order_count'] == 0): ?>
                                            <span class="status-available">🟢 Chưa bán</span>
                                        <?php else: ?>
                                            <span class="status-ordered">🔒 Đã có đơn</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($row['order_count'] == 0): ?>
                                            <form action="delete_disc.php" method="POST" onsubmit="return confirm('Xóa đĩa này?')">
                                                <input type="hidden" name="disc_id" value="<?= $row['disc_id'] ?>">
                                                <button type="submit" class="btn-outline-danger"><i class="fa-solid fa-trash-can"></i> Xóa</button>
                                            </form>
                                        <?php else: ?>
                                            <span style="color: var(--text-sub);">—</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-view">
                        <p>Bạn chưa có đĩa nào trong danh sách bán.</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </div>

</body>
</html>

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

<?php
require_once '../auth/check_login.php';
require_once '../config/database.php';

// Kiểm tra quyền admin
if (($_SESSION['user']['role'] ?? '') !== 'ADMIN') {
    die('Bạn không có quyền sửa sự kiện');
}

$event_id = $_GET['id'] ?? null;
if (!$event_id) {
    die('Thiếu ID');
}

$db = new Database();
$conn = $db->connect();

$sql = "SELECT * FROM events WHERE event_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die('Sự kiện không tồn tại');
}

$event = $result->fetch_assoc();

require_once '../partials/header.php';
require_once '../partials/sidebar.php';
?>

<style>
    .main-content {
        margin-left: 260px;
        padding: 100px 32px 32px 32px;
        min-height: 100vh;
        background: linear-gradient(to bottom, #1e1e1e, #121212);
        display: flex;
        justify-content: center;
        align-items: flex-start;
    }

    .edit-card {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        padding: 40px;
        border-radius: 12px;
        width: 100%;
        max-width: 600px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.5);
    }

    .edit-card h2 {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 12px;
        color: #fff;
    }

    .edit-card h2 i {
        color: var(--spotify-green);
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: var(--text-sub);
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .form-control {
        width: 100%;
        background: #2a2a2a;
        border: 1px solid transparent;
        border-radius: 4px;
        padding: 12px 16px;
        color: #fff;
        font-size: 15px;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        background: #333;
        border-color: var(--spotify-green);
        outline: none;
        box-shadow: 0 0 0 2px rgba(29, 185, 84, 0.2);
    }

    .btn-save {
        background: var(--spotify-green);
        color: #000;
        border: none;
        padding: 14px 28px;
        border-radius: 500px;
        font-weight: 700;
        font-size: 16px;
        cursor: pointer;
        width: 100%;
        margin-top: 10px;
        transition: transform 0.2s, background-color 0.2s;
    }

    .btn-save:hover {
        transform: scale(1.02);
        background: #1ed760;
    }

    .btn-back {
        display: block;
        text-align: center;
        margin-top: 20px;
        color: var(--text-sub);
        font-size: 14px;
        text-decoration: none;
        transition: color 0.2s;
    }

    .btn-back:hover {
        color: #fff;
        text-decoration: underline;
    }
</style>

<main class="main-content">
    <div class="edit-card">
        <h2><i class="fa-solid fa-pen-to-square"></i> Sửa sự kiện</h2>

        <form action="event_update_process.php" method="post">
            <input type="hidden" name="event_id" value="<?= $event['event_id'] ?>">

            <div class="form-group">
                <label>Tên sự kiện</label>
                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($event['name']) ?>" required>
            </div>

            <div class="form-group">
                <label>Ngày tổ chức</label>
                <input type="date" name="event_date" class="form-control" value="<?= $event['event_date'] ?>" required>
            </div>

            <div class="form-group">
                <label>Giá vé (VNĐ)</label>
                <input type="number" name="price" class="form-control" value="<?= $event['price'] ?>" required>
            </div>

            <div class="form-group">
                <label>Link mua vé</label>
                <input type="text" name="buy_url" class="form-control" value="<?= htmlspecialchars($event['buy_url']) ?>" placeholder="https://...">
            </div>

            <button type="submit" class="btn-save">
                <i class="fa-solid fa-floppy-disk" style="margin-right: 8px;"></i> Lưu thay đổi
            </button>
            
            <a href="event_list.php" class="btn-back">Quay lại danh sách</a>
        </form>
    </div>
</main>

<?php require_once '../partials/player.php'; ?>

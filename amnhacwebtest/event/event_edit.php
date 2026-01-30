<?php
require_once '../auth/check_login.php';
require_once '../config/database.php';

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

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa sự kiện</title>
    <link rel="stylesheet" href="../assets/css/event-form.css">

    <style>
        body {
            margin: 0;
            padding: 0;
            background: linear-gradient(180deg, #000, #121212);
            font-family: "Segoe UI", sans-serif;
            color: #fff;
        }

        .event-edit-container {
            max-width: 500px;
            margin: 80px auto;
            background: #181818;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.6);
        }

        .event-edit-container h2 {
            text-align: center;
            margin-bottom: 25px;
            font-size: 24px;
        }

        .event-form .form-group {
            margin-bottom: 18px;
        }

        .event-form label {
            display: block;
            margin-bottom: 6px;
            font-size: 14px;
            color: #ccc;
        }

        .event-form input {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: none;
            outline: none;
            font-size: 15px;
            background: #2a2a2a;
            color: #fff;
        }

        .event-form input:focus {
            background: #333;
            box-shadow: 0 0 0 2px #1db95455;
        }

        .btn-save {
            width: 100%;
            padding: 14px;
            margin-top: 10px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            background: #1db954;
            color: #000;
            transition: all 0.2s ease;
        }

        .btn-save:hover {
            background: #1ed760;
            transform: translateY(-1px);
        }

    </style>
</head>
<body>

<div class="event-edit-container">
    <h2>✏️ Sửa sự kiện</h2>

    <form action="event_update_process.php" method="post" class="event-form">
        <input type="hidden" name="event_id" value="<?= $event['event_id'] ?>">

        <div class="form-group">
            <label>Tên sự kiện</label>
            <input type="text" name="name"
                   value="<?= htmlspecialchars($event['name']) ?>" required>
        </div>

        <div class="form-group">
            <label>Ngày tổ chức</label>
            <input type="date" name="event_date"
                   value="<?= $event['event_date'] ?>" required>
        </div>

        <div class="form-group">
            <label>Giá vé (VND)</label>
            <input type="number" name="price"
                   value="<?= $event['price'] ?>" required>
        </div>

        <div class="form-group">
            <label>Link mua vé</label>
            <input type="text" name="buy_url"
                   value="<?= htmlspecialchars($event['buy_url']) ?>">
        </div>

        <button type="submit" class="btn-save">
            💾 Lưu thay đổi
        </button>
    </form>
</div>

</body>
</html>

<!-- <h2>✏️ Sửa sự kiện</h2>

<form action="event_update_process.php" method="post">
    <input type="hidden" name="event_id" value="<?= $event['event_id'] ?>">

    <label>Tên sự kiện</label><br>
    <input type="text" name="name" value="<?= htmlspecialchars($event['name']) ?>" required><br><br>

    <label>Ngày tổ chức</label><br>
    <input type="date" name="event_date" value="<?= $event['event_date'] ?>" required><br><br>

    <label>Giá vé</label><br>
    <input type="number" name="price" value="<?= $event['price'] ?>" required><br><br>

    <label>Link mua vé</label><br>
    <input type="text" name="buy_url" value="<?= htmlspecialchars($event['buy_url']) ?>"><br><br>

    <button type="submit">💾 Lưu thay đổi</button>
</form> -->

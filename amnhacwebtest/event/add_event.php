<?php
require_once '../auth/check_login.php';
require_once '../config/database.php';

/* =========================
   CHỈ CHO ADMIN
========================= */
if (($_SESSION['user']['role'] ?? '') !== 'ADMIN') {
    die('⛔ Bạn không có quyền truy cập trang này');
}
?>

<h2>➕ Thêm sự kiện mới</h2>

<form action="add_event_process.php" method="POST" enctype="multipart/form-data">

    <label>Tên sự kiện</label><br>
    <input type="text" name="name" required><br><br>

    <label>Ngày diễn ra</label><br>
    <input type="date" name="event_date" required><br><br>

    <label>Giá vé (VNĐ)</label><br>
    <input type="number" name="price" min="0"><br><br>

    <label>Link mua vé</label><br>
    <input type="url" name="buy_url" required><br><br>

    <label>Ảnh banner sự kiện</label><br>
    <input type="file" name="banner" accept="image/*" required><br><br>

    <button type="submit">➕ Thêm sự kiện</button>
</form>

<hr>

<h3>📋 Quản lý sự kiện</h3>
<a href="../event/event_list.php">➡️ Xem danh sách sự kiện</a>

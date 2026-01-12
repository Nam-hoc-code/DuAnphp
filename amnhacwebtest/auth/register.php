<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký</title>
</head>
<body>

<h2>Đăng ký tài khoản</h2>

<form action="registerprocess.php" method="POST">
    <label>Tên tài khoản</label><br>
    <input type="text" name="username" required><br><br>

    <label>Số điện thoại</label><br>
    <input type="text" name="phone"><br><br>

    <label>Mật khẩu</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit">Đăng ký</button>
</form>

<a href="loginform.php">Quay lại đăng nhập</a>

</body>
</html>

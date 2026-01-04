<!DOCTYPE html>
<html>
<head>
    <title>Đăng ký</title>
</head>
<body>

<h2>Đăng ký tài khoản</h2>

<form method="post" action="register_process.php">
    <label>Username</label><br>
    <input type="text" name="username" required><br><br>

    <label>Số điện thoại</label><br>
    <input type="text" name="phone" required><br><br>

    <label>Mật khẩu</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit">Đăng ký</button>
</form>

<p>Đã có tài khoản? <a href="login.php">Đăng nhập</a></p>

</body>
</html>

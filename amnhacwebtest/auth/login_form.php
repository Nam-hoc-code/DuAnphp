<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Spotify</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #000000;
            --input-bg: #1A1A1A;
            --accent-color: #00DBFF;
            --text-color: #ffffff;
            --label-color: #ffffff;
            --border-color: #444444;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Roboto', sans-serif;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 40px 20px;
        }

        .container {
            width: 100%;
            max-width: 900px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .header-section {
            display: flex;
            align-items: center;
            margin-bottom: 50px;
            width: 100%;
            justify-content: center;
            gap: 40px;
        }

        .logo-container {
            width: 180px;
            height: 180px;
            background-color: transparent;
            border-radius: 50%;
            border: 6px solid white;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-shrink: 0;
        }

        .logo-container svg {
            width: 100%;
            height: 100%;
            fill: #1DB954;
        }

        .titles {
            text-align: left;
        }

        .titles h1 {
            font-family: 'Times New Roman', Times, serif;
            font-size: 34px;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 12px;
            font-weight: normal;
            line-height: 1.2;
        }

        .titles h2 {
            font-family: 'Times New Roman', Times, serif;
            font-size: 30px;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: normal;
            line-height: 1.2;
        }

        .login-form {
            width: 100%;
            max-width: 500px;
            text-align: left;
        }

        .form-group {
            margin-bottom: 30px;
        }

        .form-group label {
            display: block;
            margin-bottom: 12px;
            font-weight: bold;
            font-size: 18px;
            color: #ffffff;
        }

        .form-group input {
            width: 100%;
            padding: 16px 20px;
            background-color: #1a1a1a;
            border: 1px solid #333;
            border-radius: 12px;
            color: white;
            font-size: 18px;
            outline: none;
        }

        .form-group input:focus {
            border-color: #555;
        }

        .btn-submit {
            background-color: #00DBFF;
            color: #000;
            border: none;
            padding: 12px 35px;
            border-radius: 12px;
            font-weight: bold;
            font-size: 18px;
            cursor: pointer;
            margin-bottom: 25px;
            transition: transform 0.1s;
        }

        .btn-submit:active {
            transform: scale(0.98);
        }

        .footer-links {
            font-size: 18px;
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 15px;
            width: 100%;
            max-width: 500px;
        }

        .footer-links a {
            color: #ffffff;
            text-decoration: underline;
            font-weight: bold;
            text-underline-offset: 5px;
        }

        @media (max-width: 600px) {
            .header-section {
                flex-direction: column;
                gap: 20px;
                text-align: center;
            }
            .titles {
                text-align: center;
            }
            .logo-container {
                width: 120px;
                height: 120px;
            }
        }

    </style>
</head>
<body>

<div class="container">
    <div class="header-section">
        <div class="logo-container">
            <!-- Spotify SVG Icon -->
            <svg viewBox="0 0 167.5 167.5">
                <path d="M83.7,0C37.5,0,0,37.5,0,83.7s37.5,83.7,83.7,83.7s83.7-37.5,83.7-83.7S130,0,83.7,0z M122.1,120.8 c-1.5,2.4-4.5,3.2-6.9,1.7c-19.1-11.7-43.2-14.3-71.5-7.8c-2.7,0.6-5.4-1-6.1-3.7c-0.6-2.7,1-5.4,3.7-6.1 c30.9-7,57.7-4.1,79.1,9C122.7,115.4,123.5,118.4,122.1,120.8z M132.3,98c-1.9,3-5.8,4-8.8,2.1c-21.9-13.5-55.3-17.4-81.2-9.5 c-3.3,1-6.8-0.8-7.9-4.1c-1-3.3,0.8-6.8,4.1-7.9c30-9.1,67-4.7,92,10.6C133.7,91.1,134.6,95,132.3,98z M133.3,74.5 c-26.2-15.6-69.5-17-94.7-9.4c-4,1.2-8.2-1.1-9.4-5.1c-1.2-4,1.1-8.2,5.1-9.4c30.1-9.1,78.1-7.4,109,10.9 c3.6,2.1,4.8,6.8,2.7,10.4C134,75.1,129.3,76.3,133.3,74.5z"/>
            </svg>
        </div>
        <div class="titles">
            <h1>Chào mừng quay trở lại</h1>
            <h2>Đăng nhập để tiếp tục</h2>
        </div>
    </div>

    <div class="login-form">
        <form action="login_process.php" method="POST">
            <div class="form-group">
                <label for="username">Tên tài khoản</label>
                <input type="text" id="username" name="username" required autocomplete="username">
            </div>

            <div class="form-group">
                <label for="password">Mật khẩu</label>
                <input type="password" id="password" name="password" required autocomplete="current-password">
            </div>

            <button type="submit" class="btn-submit">Xác nhận</button>
        </form>

        <div class="footer-links">
            <span>Bạn chưa có tài khoản?</span>
            <a href="register.php">Đăng ký</a>
        </div>
    </div>
</div>

</body>
</html>


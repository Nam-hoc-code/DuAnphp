<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../config/database.php";

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

function show_message($title, $message, $is_error = true) {
    ?>
    <!DOCTYPE html>
    <html lang="vi">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo $title; ?> - Spotify</title>
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
        <style>
            :root {
                --bg-color: #000000;
                --input-bg: #1A1A1A;
                --accent-color: #00DBFF;
                --text-color: #ffffff;
                --label-color: #ffffff;
                --border-color: #444444;
                --error-color: #ff4d4d;
                --success-color: #1DB954;
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
                max-width: 600px;
                text-align: center;
                background: #111;
                padding: 40px;
                border-radius: 20px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.5);
                border: 1px solid #333;
            }

            .logo-container {
                width: 100px;
                height: 100px;
                margin: 0 auto 30px;
                border: 4px solid white;
                border-radius: 50%;
                padding: 15px;
                display: flex;
                justify-content: center;
                align-items: center;
            }

            .logo-container svg {
                width: 100%;
                height: 100%;
                fill: #1DB954;
            }

            h1 {
                font-size: 28px;
                margin-bottom: 20px;
                color: <?php echo $is_error ? 'var(--error-color)' : 'var(--success-color)'; ?>;
            }

            p {
                font-size: 18px;
                margin-bottom: 40px;
                line-height: 1.6;
                color: #ccc;
            }

            .btn-group {
                display: flex;
                flex-direction: column;
                gap: 15px;
                align-items: center;
            }

            .btn {
                display: inline-block;
                width: 100%;
                max-width: 300px;
                padding: 14px 20px;
                border-radius: 12px;
                font-weight: bold;
                font-size: 16px;
                text-decoration: none;
                transition: transform 0.2s, background-color 0.2s;
                cursor: pointer;
            }

            .btn-primary {
                background-color: var(--accent-color);
                color: #000;
            }

            .btn-secondary {
                background-color: transparent;
                color: #fff;
                border: 1px solid #555;
            }

            .btn:hover {
                transform: translateY(-2px);
            }

            .btn:active {
                transform: translateY(0);
            }

            .btn-secondary:hover {
                background-color: #222;
                border-color: #777;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="logo-container">
                <svg viewBox="0 0 167.5 167.5">
                    <path d="M83.7,0C37.5,0,0,37.5,0,83.7s37.5,83.7,83.7,83.7s83.7-37.5,83.7-83.7S130,0,83.7,0z M122.1,120.8 c-1.5,2.4-4.5,3.2-6.9,1.7c-19.1-11.7-43.2-14.3-71.5-7.8c-2.7,0.6-5.4-1-6.1-3.7c-0.6-2.7,1-5.4,3.7-6.1 c30.9-7,57.7-4.1,79.1,9C122.7,115.4,123.5,118.4,122.1,120.8z M132.3,98c-1.9,3-5.8,4-8.8,2.1c-21.9-13.5-55.3-17.4-81.2-9.5 c-3.3,1-6.8-0.8-7.9-4.1c-1-3.3,0.8-6.8,4.1-7.9c30-9.1,67-4.7,92,10.6C133.7,91.1,134.6,95,132.3,98z M133.3,74.5 c-26.2-15.6-69.5-17-94.7-9.4c-4,1.2-8.2-1.1-9.4-5.1c-1.2-4,1.1-8.2,5.1-9.4c30.1-9.1,78.1-7.4,109,10.9 c3.6,2.1,4.8,6.8,2.7,10.4C134,75.1,129.3,76.3,133.3,74.5z"/>
                </svg>
            </div>
            <h1><?php echo $title; ?></h1>
            <p><?php echo $message; ?></p>
            <div class="btn-group">
                <a href="login_form.php" class="btn btn-primary">Thử lại Đăng nhập</a>
                <a href="../index.php" class="btn btn-secondary">Về trang chủ</a>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

$db = new Database();
$conn = $db->connect();

$sql = "SELECT * FROM users WHERE username = ? LIMIT 1";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    show_message("Lỗi hệ thống", "Đã có lỗi xảy ra. Vui lòng liên hệ quản trị viên.");
}

$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    if (password_verify($password, $user['password'])) {

        session_regenerate_id(true);

        // ✅ Thêm user_id vào session
        $_SESSION['user_id'] = $user['user_id'];
        
        $_SESSION['user'] = [
            'user_id'  => $user['user_id'],
            'username' => $user['username'],
            'role'     => $user['role']
        ];

        if ($user['role'] === 'ADMIN') {
            header("Location: ../admin/admin_view.php");
        } elseif ($user['role'] === 'ARTIST') {
            header("Location: ../artist/artist_view.php");
        } else {
            header("Location: ../user/home.php");
        }
        exit;

    } else {
        show_message("Sai mật khẩu", "Mật khẩu bạn nhập không chính xác. Vui lòng kiểm tra lại.");
    }
} else {
    show_message("Tài khoản không tồn tại", "Chúng tôi không tìm thấy tài khoản tương ứng với tên đăng nhập này.");
}
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Đăng nhập</title>
    <style>
        :root{
            --bg:#0d0d0d;
            --panel:#0f0f10;
            --text:#e9e9e9;
            --muted:rgba(255,255,255,0.55);
            --input-bg:#181818;
            --cyan:#14d1e6;
            --yellow:#ffdf3a;
        }
        html,body{height:100%;}
        body{
            margin:0;
            font-family: Georgia, 'Times New Roman', Times, serif;
            background:var(--bg);
            color:var(--text);
            display:flex;
            align-items:center;
            justify-content:center;
        }
        .wrap{
            width:100%;
            max-width:720px;
            padding:40px 20px;
            box-sizing:border-box;
            text-align:center;
        }
        .logo{
            width:110px;
            height:110px;
            margin:0 auto 18px auto;
            display:flex;
            align-items:center;
            justify-content:center;
            border-radius:50%;
            background:transparent;
            box-shadow:none;
            padding:0;
        }
        .logo svg{width:110px;height:110px;display:block}
        h1{font-size:20px;margin:6px 0 4px 0;letter-spacing:2px;font-weight:700;color:var(--text)}
        h2{font-size:18px;margin:0 0 22px 0;color:var(--text);letter-spacing:2px;font-weight:700}

        form.login{
            max-width:420px;
            margin:0 auto;
            text-align:left;
        }
        label.field{
            display:block;color:var(--text);font-size:13px;margin-bottom:8px;margin-left:6px;font-weight:700
        }
        .field-wrap{margin-bottom:18px}
        input[type=text],input[type=password]{
            width:100%;
            height:40px;
            background:var(--input-bg);
            border:1px solid rgba(255,255,255,0.06);
            border-radius:8px;
            padding:8px 12px;
            color:var(--text);
            box-shadow:inset 0 1px 0 rgba(255,255,255,0.02);
            outline:none;
            font-size:14px;
        }
        .controls{display:flex;gap:24px;justify-content:center;margin-top:8px}
        .btn{
            border:0;padding:10px 26px;border-radius:24px;cursor:pointer;font-weight:600;box-shadow:0 4px 8px rgba(0,0,0,0.35)
        }
        .btn-cyan{background:var(--cyan);color:#03303a}
        .btn-yellow{background:var(--yellow);color:#1b1b00}
        @media (max-width:480px){
            .wrap{padding:28px 12px}
            .controls{gap:14px}
        }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="logo" aria-hidden>
            <!-- single circle with three centered curved bars -->
            <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg" aria-hidden>
                <circle cx="50" cy="50" r="48" fill="#28d363" />
                <g fill="none" stroke="#052826" stroke-width="4.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M28 36 C40 32,60 32,72 36" />
                    <path d="M28 50 C40 46,60 46,72 50" />
                    <path d="M28 64 C40 60,60 60,72 64" />
                </g>
            </svg>
        </div>
        <h1>CHÀO MỪNG ĐÃ QUAY</h1>
        <h2>TRỞ LẠI</h2>
    <h2>Đăng nhập</h2>

    <form method="post" action="loginprocess.php">
        <label>Username:</label><br>
        <input type="text" name="username" required><br><br>

        <form class="login" method="post" action="loginprocess.php">
            <div class="field-wrap">
                <label class="field">Tên tài Khoản</label>
                <input type="text" name="username" required autocomplete="username">
            </div>
            <div class="field-wrap">
                <label class="field">Mật khẩu</label>
                <input type="password" name="password" required autocomplete="current-password">
            </div>

            <div class="controls">
                <button class="btn btn-cyan" type="submit">Xác nhận</button>
                <button class="btn btn-yellow" type="button" onclick="location.href='../register.php'">Đăng ký</button>
            </div>
        </form>
    </div>
        <button type="submit">Đăng nhập</button>
    
    </form>
</body>
</html>

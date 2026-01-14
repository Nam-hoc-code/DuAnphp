<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Đăng nhập</title>
    <style>
        :root{
            --bg:#0b0b0b;
            --panel:#0f0f0f;
            --text:#ffffff;
            --muted:rgba(255,255,255,0.7);
            --input-bg:#1a1a1a;
            --cyan:#25e6f2;
            --accent-dark:#052829;
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
            -webkit-font-smoothing:antialiased;
        }
        .wrap{
            width:100%;
            max-width:720px;
            padding:48px 20px 64px;
            box-sizing:border-box;
            text-align:center;
        }
        .logo{
            width:140px;
            height:140px;
            margin:0 auto 18px auto;
            display:flex;
            align-items:center;
            justify-content:center;
            border-radius:50%;
            background:transparent;
            box-shadow:0 6px 18px rgba(0,0,0,0.6);
            padding:6px;
        }
        .logo .ring{fill:#fff}
        .logo svg{width:140px;height:140px;display:block}

        .title-big{font-size:28px;margin:6px 0 4px 0;letter-spacing:6px;font-weight:700;color:var(--text);}
        .title-sub{font-size:30px;margin:2px 0 18px 0;letter-spacing:6px;font-weight:700;color:var(--text);}

        form.login{
            max-width:520px;
            margin:10px auto 0;
            text-align:left;
        }
        label.field{
            display:block;color:var(--text);font-size:14px;margin-bottom:8px;margin-left:6px;font-weight:700
        }
        .field-wrap{margin-bottom:26px}
        input[type=text],input[type=password]{
            width:100%;
            height:44px;
            background:var(--input-bg);
            border:1px solid rgba(255,255,255,0.08);
            border-radius:10px;
            padding:10px 14px;
            color:var(--text);
            box-shadow:inset 0 2px 0 rgba(255,255,255,0.02);
            outline:none;
            font-size:15px;
        }
        .centered{display:flex;flex-direction:column;align-items:center;gap:12px;margin-top:4px}
        .btn-confirm{background:var(--cyan);color:var(--accent-dark);border-radius:18px;padding:14px 48px;border:0;font-weight:800;cursor:pointer;font-size:18px;box-shadow:0 8px 20px rgba(0,0,0,0.6)}
        .hint{font-size:14px;color:var(--muted);text-align:center;margin-top:12px}
        .hint a{display:block;color:var(--text);text-decoration:none;margin-top:6px;font-weight:700}

        @media (max-width:480px){
            .wrap{padding:28px 12px}
            .logo{width:110px;height:110px}
            .logo svg{width:110px;height:110px}
            .title-big{font-size:20px;letter-spacing:4px}
            .title-sub{font-size:22px}
            .btn-confirm{padding:12px 34px;font-size:16px}
        }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="logo" aria-hidden>
            <svg viewBox="0 0 120 120" xmlns="http://www.w3.org/2000/svg" aria-hidden>
                <circle cx="60" cy="60" r="58" fill="#111"/>
                <circle cx="60" cy="60" r="46" fill="#1bd760" />
                <circle cx="60" cy="60" r="36" fill="none" stroke="#fff" stroke-width="4" />
                <g fill="none" stroke="#052826" stroke-width="4" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M32 44 C46 38,74 38,88 44" />
                    <path d="M32 60 C46 54,74 54,88 60" />
                    <path d="M32 76 C46 70,74 70,88 76" />
                </g>
            </svg>
        </div>

        <div class="title-big">CHÀO MỪNG ĐÃ QUAY</div>
        <div class="title-sub">TRỞ LẠI</div>

        <form class="login" method="post" action="login_process.php">
            <div class="field-wrap">
                <label class="field">Tên tài Khoản</label>
                <input type="text" name="username" required autocomplete="username">
            </div>
            <div class="field-wrap">
                <label class="field">Mật khẩu</label>
                <input type="password" name="password" required autocomplete="current-password">
            </div>

            <div class="centered">
                <button class="btn-confirm" type="submit">Xác nhận</button>
                <div class="hint">Bạn chưa có tài khoản? <a href="../auth/register.php">Đăng ký</a></div>
            </div>
        </form>
    </div>
</body>
</html>

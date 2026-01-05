<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Đăng ký</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;900&family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root{--bg:#0f0f10;
            --panel:#141414;
            --input:#1f1f1f;
            --muted:rgba(255,255,255,0.72);
            --accent:#10d7ec
        }
        html,body{height:100%;
            margin:0;font-family: 'Montserrat', system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial;
            background:var(--bg);color:#fff
        }
        .wrap{min-height:100%;display:flex;align-items:center;
            justify-content:center;padding:48px
        }
        .card{width:820px;display:flex;gap:28px;
            align-items:flex-start
        }

        /* left logo column */
        .brand{width:180px;
            display:flex;
            align-items:flex-start;
            justify-content:flex-start}
        .logo{width:120px;
            height:120px;
            border-radius:50%;
            display:flex;
            align-items:center;
            justify-content:center;
            position:relative}
        .logo svg{width:110px;
            height:110px;
            display:block}

        /* right content column */
        .content{flex:1;
            display:flex;
            flex-direction:column;
            align-items:center}
        .title{margin:6px 0 26px;
            text-align:center}
        .title h1{font-family:'Playfair Display', serif;
            font-size:26px;
            margin:0;
            letter-spacing:4px;
            font-weight:700;
            color:#fff}
        .title h2{font-family:'Playfair Display', serif;
            font-size:26px;
            margin:8px 0 0;
            color:#f6f6f6;
            font-weight:600}

        .form-panel{width:420px}
        form{background:transparent;
            padding:0}
        label{display:block;
            font-weight:700;
            margin-bottom:8px;
            color:#eee;
            font-size:15px}
        .field{margin-bottom:18px}
        input[type=text],input[type=password]{width:100%;
            padding:12px 14px;
            border-radius:6px;
            background:var(--input);
            border:1px solid rgba(255,255,255,0.06);
            color:#fff;
            outline:none;
            box-sizing:border-box}
        input::placeholder{color:rgba(255,255,255,0.35)}

        .btn{display:inline-block;
            padding:10px 16px;
            border-radius:8px;
            background:var(--accent);
            color:#042;
            cursor:pointer;
            border:none;
            font-weight:700}
        .meta{margin-top:12px;
            color:var(--muted);
            font-size:14px}
        .meta a{color:#d7c9ff;
            text-decoration:underline;
            text-underline-offset:6px}
        .small-link{display:flex;
            align-items:center;gap:10px;margin-top:12px}

        /* center page responsiveness */
        @media(max-width:760px){.card{flex-direction:column;align-items:center}.brand{order:0}.content{width:100%}.form-panel{width:100%}}
    </style>
</head>
<body>

<div class="wrap">
    <div class="card">
        <div class="brand">
            <div class="logo" aria-hidden="true">
                <!-- improved SVG matching provided image: outer dark rings, white ring, green center, three centered dark stripes -->
                <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="logo">
                    <defs>
                        <filter id="drop" x="-50%" y="-50%" width="200%" height="200%">
                            <feDropShadow dx="0" dy="6" stdDeviation="12" flood-color="#000" flood-opacity="0.7" />
                        </filter>
                    </defs>
                    <!-- outer dark rings -->
                    <circle cx="100" cy="100" r="98" fill="#0b0b0b" />
                    <circle cx="100" cy="100" r="90" fill="#111" />
                    <!-- white ring -->
                    <circle cx="100" cy="100" r="74" fill="#fff" />
                    <!-- green inner circle -->
                    <circle cx="100" cy="100" r="62" fill="#1ed760" filter="url(#drop)" />

                    <!-- three centered curved stripes -->
                    <path d="M56 86 C86 72,114 72,144 86" stroke="#0a2a21" stroke-width="8" stroke-linecap="round" fill="none" />
                    <path d="M52 106 C86 96,114 96,148 106" stroke="#0a2a21" stroke-width="8" stroke-linecap="round" fill="none" />
                    <path d="M56 126 C86 118,114 118,144 126" stroke="#0a2a21" stroke-width="8" stroke-linecap="round" fill="none" />
                </svg>
            </div>
        </div>

        <div class="content">
            <div class="title">
                <h1>ĐĂNG KÝ ĐỂ TẬN HƯỞNG NGAY</h1>
                <h2>NHỮNG BÀI HÁT YÊU THÍCH</h2>
            </div>

            <div class="form-panel">
                <form method="post" action="register_process.php">
                    <div class="field">
                        <label for="username">Tên tài Khoản</label>
                        <input id="username" name="username" type="text" placeholder="Nhập tên tài khoản" required />
                    </div>

                    <div class="field">
                        <label for="phone">Số điện thoại</label>
                        <input id="phone" name="phone" type="text" placeholder="Nhập số điện thoại" required />
                    </div>

                    <div class="field">
                        <label for="password">Mật khẩu</label>
                        <input id="password" name="password" type="password" placeholder="Mật khẩu" required />
                    </div>

                    <div class="field">
                        <label for="password2">Xác nhận mật khẩu</label>
                        <input id="password2" name="password2" type="password" placeholder="Nhập lại mật khẩu" required />
                    </div>

                    <div class="field">
                        <button class="btn" type="submit">Đăng Ký</button>
                    </div>

                    <div class="small-link">
                        <div class="meta">Bạn đã có tài khoản?</div>
                        <div class="meta"><a href="login.php">Đăng nhập</a></div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>

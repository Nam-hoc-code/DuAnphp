<?php require_once "check_artist.php"; ?>
<!DOCTYPE html>
<html>
<head>
    <style>
        body { background: #121212; color: white; font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .form-card { background: #181818; padding: 40px; border-radius: 8px; width: 400px; }
        h2 { margin-top: 0; margin-bottom: 24px; text-align: center; }
        label { display: block; margin-bottom: 8px; color: #b3b3b3; font-size: 0.8rem; font-weight: bold; }
        input[type="text"], input[type="file"] { width: 100%; padding: 12px; margin-bottom: 20px; background: #282828; border: 1px solid transparent; border-radius: 4px; color: white; box-sizing: border-box; }
        input[type="text"]:focus { border-color: #1DB954; outline: none; }
        button { width: 100%; background: #1DB954; color: black; border: none; padding: 14px; border-radius: 50px; font-weight: bold; cursor: pointer; }
        button:hover { transform: scale(1.02); background: #1ed760; }
    </style>
</head>
<body>
<div class="form-card">
    <h2>Thêm bài hát mới</h2>
    <form action="add_song_process.php" method="POST" enctype="multipart/form-data">
        <label>TÊN BÀI HÁT</label>
        <input type="text" name="title" required placeholder="Nhập tên bài hát">
        <label>TẬP TIN NHẠC (MP3)</label>
        <input type="file" name="audio" accept=".mp3" required>
        <label>ẢNH BÌA (COVER)</label>
        <input type="file" name="cover" accept="image/*" required>
        <button type="submit">UPLOAD</button>
    </form>
    <div style="text-align: center; margin-top: 20px;">
        <a href="artist_view.php" style="color: #b3b3b3; text-decoration: none; font-size: 0.8rem;">⬅ Quay lại dashboard</a>
    </div>
</div>
</body>
</html>

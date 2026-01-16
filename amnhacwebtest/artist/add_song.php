<?php require_once "check_artist.php"; ?>

<h2>Thêm bài hát</h2>

<form action="add_song_process.php" method="POST" enctype="multipart/form-data">

    <label>Tên bài hát</label><br>
    <input type="text" name="title" required><br><br>

    <label>File nhạc (mp3)</label><br>
    <input type="file" name="audio" accept=".mp3" required><br><br>

    <label>Ảnh cover</label><br>
    <input type="file" name="cover" accept="image/*" required><br><br>

    <button type="submit">Upload</button>
</form>

<a href="artist_view.php">⬅ Quay lại dashboard</a>

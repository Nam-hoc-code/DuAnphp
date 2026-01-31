<?php
require_once '../auth/check_login.php';

$page = $_GET['page'] ?? 'home';

$allowPages = [
    'home',
    'profile',
    'notifications',
    'song_detail'
];

if (!in_array($page, $allowPages)) {
    $page = 'home';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>User</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<?php require_once '../partials/header.php'; ?>

<div class="main-wrapper">

    <?php require_once '../partials/sidebar.php'; ?>

    <div class="main-content" id="mainContent">

        <?php
        $file = __DIR__ . '/' . $page . '.php';
        require $file;
        ?>

    </div>

</div>

<?php require_once '../partials/player.php'; ?>

</body>
</html>

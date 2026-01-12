<?php
session_start();

/* Xóa toàn bộ session */
$_SESSION = [];
session_destroy();

/* Quay về trang login */
header("Location: loginform.php");
exit;

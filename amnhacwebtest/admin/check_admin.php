<?php
session_start();

if (
    !isset($_SESSION['user']) ||
    $_SESSION['user']['role'] !== 'ADMIN'
) {
    die("Access denied");
}

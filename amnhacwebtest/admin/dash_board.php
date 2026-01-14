<?php
require_once "check_admin.php";
require_once "../config/database.php";

$db = new Database();
$conn = $db->connect();

$totalUsers = $conn->query(
    "SELECT COUNT(*) total FROM users"
)->fetch_assoc()['total'];

$totalSongs = $conn->query(
    "SELECT COUNT(*) total FROM songs"
)->fetch_assoc()['total'];

$pendingSongs = $conn->query(
    "SELECT COUNT(*) total FROM songs WHERE status = 'PENDING'"
)->fetch_assoc()['total'];



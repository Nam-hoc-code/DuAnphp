<?php
require_once '../config/database.php';

$name = $_POST['name'];
$event_date = $_POST['event_date'];
$price = $_POST['price'] ?? 0;
$buy_url = $_POST['buy_url'];

$db = new Database();
$conn = $db->connect();

$sql = "
INSERT INTO events (name, event_date, price, buy_url)
VALUES (?, ?, ?, ?)
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sids", $name, $event_date, $price, $buy_url);
$stmt->execute();

header("Location: ../event/event_list.php");
exit;
?>

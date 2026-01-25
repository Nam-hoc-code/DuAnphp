<?php
require_once "config/database.php";
$db = new Database();
$conn = $db->connect();

$tables = ['disc_orders', 'discs', 'songs', 'users'];
foreach ($tables as $table) {
    echo "<h3>Table: $table</h3>";
    $res = $conn->query("DESCRIBE $table");
    if (!$res) {
        echo "Error: " . $conn->error . "<br>";
        continue;
    }
    echo "<table border='1'>";
    while ($row = $res->fetch_assoc()) {
        echo "<tr><td>" . implode("</td><td>", $row) . "</td></tr>";
    }
    echo "</table>";
}
?>

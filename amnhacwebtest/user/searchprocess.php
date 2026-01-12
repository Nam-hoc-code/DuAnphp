
<?php
require_once '../../config/database.php';

$db = (new Database())->connect();

$keyword = $_GET['keyword'] ?? '';

$sql = "SELECT * FROM songs 
        WHERE title LIKE ? OR artist LIKE ?";
$stmt = $db->prepare($sql);
$search = "%$keyword%";
$stmt->bind_param("ss", $search, $search);
$stmt->execute();

$result = $stmt->get_result();
$searchResults = $result->fetch_all(MYSQLI_ASSOC);

// Sau này có thể include search_result.php để hiển thị

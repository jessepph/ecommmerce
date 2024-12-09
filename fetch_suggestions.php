<?php
include 'db.php'; // Include your database connection

$keyword = isset($_GET['query']) ? $con->real_escape_string(trim($_GET['query'])) : '';

if ($keyword) {
    $sql = "SELECT name FROM products WHERE name LIKE '%$keyword%' LIMIT 5";
    $result = $con->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<div class="suggestion-item">' . htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') . '</div>';
        }
    } else {
        echo '<div>No suggestions found</div>';
    }
}
?>

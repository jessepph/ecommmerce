<?php
// Database connection
$host = 'localhost'; // Your database host
$user = 'root'; // Your database username
$password = 'CoheedAndCambria666!'; // Your database password
$dbname = 'market'; // Your database name
$port = 888;

$conn = new mysqli($host, $user, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the search keyword from the request
$searchTerm = isset($_GET['term']) ? $_GET['term'] : '';

// Prepare and execute the SQL query
$query = $conn->prepare("SELECT vendor_name FROM products WHERE vendor_name LIKE CONCAT('%', ?, '%') LIMIT 10");
$query->bind_param("s", $searchTerm);
$query->execute();
$result = $query->get_result();

// Prepare the results in an array
$suggestions = [];
while ($row = $result->fetch_assoc()) {
    // Ensure you're using the correct column name
    $suggestions[] = ['label' => $row['vendor_name'], 'value' => $row['vendor_name']];
}

// Return the JSON response
echo json_encode($suggestions);

// Close the connection
$conn->close();
?>
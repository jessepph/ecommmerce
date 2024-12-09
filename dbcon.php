<?

// Database connection parameters
$servername = "localhost";
$dbusername = "root";
$password = "CoheedAndCambria666!";
$dbname = "market";
$port = 888;
// Create a new database connection
$conn = new mysqli($servername, $dbusername, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

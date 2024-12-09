<?php
session_start();

// BitGo API credentials
$bitgoApiToken = 'v2xc7b81210f2cbc5f63c54711d7b76ab5de4211b631a7248aa9009722daace496c'; // Replace with your actual API token

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

// Ensure user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// Function to generate a new Bitcoin address using BitGo API
function generateBitcoinAddress($apiToken) {
    $url = "https://app.bitgo.com/api/v2/btc/wallet/generate";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $apiToken",
        "Content-Type: application/json"
    ]);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Output response for debugging
    if ($http_code !== 200) {
        throw new Exception("API request failed with status code $http_code. Response: $response");
    }

    $data = json_decode($response, true);
    if (isset($data['address'])) {
        return $data['address'];
    } else {
        throw new Exception("Failed to generate Bitcoin address. Response: $response");
    }
}

try {
    $bitcoin_address = generateBitcoinAddress($bitgoApiToken);

    // Insert or update wallet information in the database
    $query = "INSERT INTO wallets (username, bitcoin_address) VALUES (?, ?) ON DUPLICATE KEY UPDATE bitcoin_address = VALUES(bitcoin_address)";
    $stmt = $conn->prepare($query);

    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ss", $username, $bitcoin_address);
    $stmt->execute();
    $stmt->close();

    // Fetch wallet details
    $query = "SELECT bitcoin_address, balance FROM wallets WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $wallet = $result->fetch_assoc();
    $stmt->close();

} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

// Close the connection
$conn->close();

// Encode data as JSON
$walletData = json_encode($wallet);

// Prepare the URL for the blockchain explorer
$explorerUrl = "https://www.blockchain.com/explorer/assets/btc/" . urlencode($bitcoin_address);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Wallet</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <script>
        // Pass PHP data to JavaScript
        var walletData = <?php echo $walletData; ?>;
        var explorerUrl = "<?php echo $explorerUrl; ?>";

        // Log data to console
        console.log('Wallet Data:', walletData);

        // Create a link to the blockchain explorer
        console.log('View Address on Blockchain Explorer:', explorerUrl);

        // Optionally, create a clickable link on the page
        var link = document.createElement('a');
        link.href = explorerUrl;
        link.target = '_blank';
        link.textContent = 'View Address on Blockchain Explorer';
        document.body.appendChild(link);
    </script>
</body>
</html>

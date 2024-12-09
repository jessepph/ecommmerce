<?php // Database connection parameters
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'market';

// Create database connection using PDO
try {
    $dsn = "mysql:host=$host;dbname=$database;";
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
// Initialize the wallet address variable and available balance variable
$bitcoin_wallet_address = '';
$available_balance = 0;

// Check if the user is logged in
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username']; // Get logged-in username

    // Fetch bitcoin wallet address from the database
    $query = "SELECT bitcoin_wallet_address FROM register WHERE username = :username";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);

    // Execute query and retrieve the address
    if ($stmt->execute()) {
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result && isset($result['bitcoin_wallet_address'])) {
            $bitcoin_wallet_address = htmlspecialchars($result['bitcoin_wallet_address'], ENT_QUOTES, 'UTF-8');

            // Fetch balance using cURL from BlockCypher API
            $api_url = "https://api.blockcypher.com/v1/btc/main/addrs/$bitcoin_wallet_address/balance";
            $ch = curl_init($api_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);

            if ($response === false) {
                $error = curl_error($ch);
                error_log("Error fetching balance for address $bitcoin_wallet_address: $error");
                $available_balance = 0; // Set balance to 0 on error
            } else {
                curl_close($ch);
                $data = json_decode($response, true);

                // Set balance if valid data is returned
                if (isset($data['final_balance'])) {
                    $available_balance = $data['final_balance'] / 100000000; // Convert satoshis to BTC
                } else {
                    $available_balance = 0; // If no valid balance data, set to 0
                }

                // Update the `available_bitcoin_balance` in the register table
                $update_query = "UPDATE register SET available_bitcoin_balance = :available_balance WHERE username = :username";
                $update_stmt = $pdo->prepare($update_query);
                $update_stmt->bindParam(':available_balance', $available_balance, PDO::PARAM_STR);
                $update_stmt->bindParam(':username', $username, PDO::PARAM_STR);

                // Execute the update query
                if ($update_stmt->execute()) {
                    // Optionally, you can add a success message or log it
                    error_log("Successfully updated available_bitcoin_balance for user: $username");
                } else {
                    error_log("Failed to update available_bitcoin_balance for user: $username");
                }
            }
        } else {
            error_log("No Bitcoin wallet address found for username: $username");
        }
    }
}


// Initialize the wallet address variable and available balance variable
$bitcoin_wallet_address = '';
$available_balance = 0;

// Check if the user is logged in
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username']; // Get logged-in username

    // Fetch bitcoin wallet address from the database
    $query = "SELECT bitcoin_wallet_address FROM register WHERE username = :username";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);

    // Execute query and retrieve the address
    if ($stmt->execute()) {
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result && isset($result['bitcoin_wallet_address'])) {
            $bitcoin_wallet_address = htmlspecialchars($result['bitcoin_wallet_address'], ENT_QUOTES, 'UTF-8');

            // Generate QR code URL
            $qr_code_url = "https://api.qrserver.com/v1/create-qr-code/?data=" . urlencode($bitcoin_wallet_address) . "&size=150x150";

            // Fetch balance using cURL
            $api_url = "https://api.blockcypher.com/v1/btc/main/addrs/$bitcoin_wallet_address/balance";
            $ch = curl_init($api_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);

            if ($response === false) {
                $error = curl_error($ch);
                error_log("Error fetching balance for address $bitcoin_wallet_address: $error");
                $available_balance = 0; // Set balance to 0 on error
            } else {
                curl_close($ch);
                $data = json_decode($response, true);

                // Set balance if valid data is returned
                $available_balance = isset($data['final_balance']) ? $data['final_balance'] / 100000000 : 0;
            }
        }
    }
}
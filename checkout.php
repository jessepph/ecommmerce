<?php
require_once("db.php");
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    exit("<h1>You must be logged in to view your cart.</h1>");
}

$username = $_SESSION['username'];  // Get logged-in user

// Check if the form was submitted to process the order
if (isset($_POST['process_order'])) {
    // Retrieve the data from the POST request
    $bitcoin_wallet_address = $_POST['bitcoin_wallet_address'];
    $total_cart_price = floatval($_POST['total_cart_price']);
    $total_price_in_btc = floatval($_POST['total_price_in_btc']);

    // Fetch Bitcoin balance from the register table
    $query = "SELECT bitcoin_wallet_address, available_bitcoin_balance FROM register WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $user_bitcoin_wallet = $row['bitcoin_wallet_address'];
        $available_bitcoin_balance = $row['available_bitcoin_balance'];

        // Ensure the user has enough Bitcoin balance to proceed
        if ($available_bitcoin_balance >= $total_price_in_btc) {
            // Deduct the Bitcoin from the user's account
            $new_balance = $available_bitcoin_balance - $total_price_in_btc;
            $update_balance_query = "UPDATE register SET available_bitcoin_balance = ? WHERE username = ?";
            $update_balance_stmt = $conn->prepare($update_balance_query);
            $update_balance_stmt->bind_param("ds", $new_balance, $username);
            $update_balance_stmt->execute();

            // Insert the order into the orders table
            $order_query = "INSERT INTO orders (username, bitcoin_wallet_address, total_price_usd, total_price_btc, order_status, created_at) VALUES (?, ?, ?, ?, 'Pending', NOW())";
            $order_stmt = $conn->prepare($order_query);
            $order_stmt->bind_param("ssdd", $username, $bitcoin_wallet_address, $total_cart_price, $total_price_in_btc);
            $order_stmt->execute();

            // Display a confirmation message
            echo "<h1>Order Placed Successfully!</h1>";
            echo "<p>Your order has been placed and is pending payment confirmation.</p>";
            echo "<p>Total Price: USD " . number_format($total_cart_price, 2) . " or BTC " . number_format($total_price_in_btc, 8) . "</p>";

            // Optionally, send an email or other notifications here

        } else {
            // Insufficient balance
            echo "<p style='color: red;'>Insufficient Bitcoin balance to complete the transaction.</p>";
        }
    } else {
        // User doesn't have a Bitcoin wallet
        echo "<p style='color: red;'>You don't have a Bitcoin wallet address linked to your account.</p>";
    }
}
?>

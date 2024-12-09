<?php
require_once("db.php"); // Include database connection
session_start();

// Check if product_name and expected_amount_btc are set in the session
if (!isset($_SESSION['product_name'])) {
    exit("Error: Product Name is missing.");
}

if (!isset($_SESSION['expected_amount_btc'])) {
    exit("Error: Missing expected BTC amount.");
}

// Retrieve the product_name and expected_amount_btc from session
$productName = $_SESSION['product_name'];
$expectedAmountBtc = $_SESSION['expected_amount_btc'];

// Fetch the vendor's Bitcoin wallet address from the products table based on the product_name
$query = "SELECT id, bitcoin_wallet_address FROM products WHERE product_name = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $productName);  // 's' stands for string
$stmt->execute();
$stmt->store_result();

// Check if the query returned a result
if ($stmt->num_rows === 0) {
    exit("Error: Product not found or Bitcoin wallet address missing.");
}

// Fetch the result (id and vendor's Bitcoin wallet address)
$stmt->bind_result($productId, $vendorWallet);
$stmt->fetch();

// Close the statement
$stmt->close();

// Check if the Bitcoin wallet address is empty
if (empty($vendorWallet)) {
    exit("Error: Bitcoin wallet address is missing for this product.");
}

// Generate the Bitcoin payment URL using the bitcoin: URI scheme
$paymentUrl = "bitcoin:$vendorWallet?amount=$expectedAmountBtc";

// Redirect the user to their Bitcoin wallet app (payment link)
header("Location: $paymentUrl");
exit;
?>
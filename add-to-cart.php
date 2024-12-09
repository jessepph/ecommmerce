<?php
session_start();
require("db.php");

// Ensure user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Capture POST data from the form
$productId = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
$productName = isset($_POST['product_name']) ? $_POST['product_name'] : '';
$sellingPrice = isset($_POST['selling_price']) ? floatval($_POST['selling_price']) : 0;
$shippingPrice = isset($_POST['shipping_price']) ? floatval($_POST['shipping_price']) : 0;
$totalPrice = isset($_POST['total_price']) ? floatval($_POST['total_price']) : 0;
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
$username = $_SESSION['username']; // Get the logged-in user's username

// Sanitize input to avoid SQL injection
$productName = $conn->real_escape_string($productName);

// Optional: Bitcoin wallet address (could be NULL if not provided)
$bitcoinWalletAddress = NULL; // Or $_POST['bitcoin_wallet_address'] if you collect this info

// Get current timestamp
$createdAt = date('Y-m-d H:i:s');

// SQL query to insert the product into the cart table
$query = "INSERT INTO cart (username, product_id, quantity, total_price, bitcoin_wallet_address, created_at, order_count, name)
          VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($query);
if ($stmt === false) {
    die("Prepared statement failed: " . $conn->error);
}

// Bind parameters
$stmt->bind_param("siidssis", $username, $productId, $quantity, $totalPrice, $bitcoinWalletAddress, $createdAt, $quantity, $productName);

// Execute the statement
if ($stmt->execute()) {
    echo "Product added to cart successfully!";
} else {
    echo "Error adding product to cart: " . $stmt->error;
}

$stmt->close();
$conn->close();

// Redirect back to the cart page or another page after adding the product
header("Location: cart.php");
exit();
?>

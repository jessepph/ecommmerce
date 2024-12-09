<?php
require_once("db.php");
session_start();

// Ensure user is logged in
if (!isset($_SESSION['username'])) {
    echo "<h1>You must be logged in to complete checkout.</h1>";
    exit;
}

$username = $_SESSION['username'];

// Get form data
$shipping_address = $_POST['shipping_address'];
$payment_method = $_POST['payment_method'];
$total_price = floatval($_POST['total_price']);

// Get cart items for the user
$query = "
    SELECT c.product_id, c.name, c.quantity, c.total_price, p.product_name AS full_product_name
    FROM cart c
    LEFT JOIN products p ON c.product_id = p.product_id
    WHERE c.username = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

$cart_details = [];  // This will store cart items as an array for the order details
$order_details = ''; // This will store a string of cart details for later retrieval

while ($row = $result->fetch_assoc()) {
    $productName = htmlspecialchars($row['name']) ?: htmlspecialchars($row['full_product_name']);
    $quantity = (int)$row['quantity'];
    $totalPriceItem = number_format(floatval($row['total_price']), 2);
    
    $cart_details[] = [
        'product_name' => $productName,
        'quantity' => $quantity,
        'total_price' => $totalPriceItem
    ];

    $order_details .= "$productName x$quantity - USD $totalPriceItem<br>";
}

// Insert order into the `orders` table
$order_query = "
    INSERT INTO bitcoin orders (username, total_price, shipping_address, payment_method, order_details, payment_status) 
    VALUES (?, ?, ?, ?, ?, 'pending')
";
$order_stmt = $conn->prepare($order_query);
$order_stmt->bind_param("sdsss", $username, $total_price, $shipping_address, $payment_method, $order_details);
$order_stmt->execute();

// Get the last inserted order ID
$order_id = $conn->insert_id;

// Empty the user's cart after the order is created
$clear_cart_query = "DELETE FROM cart WHERE username = ?";
$clear_cart_stmt = $conn->prepare($clear_cart_query);
$clear_cart_stmt->bind_param("s", $username);
$clear_cart_stmt->execute();

// Redirect to an order confirmation page
header("Location: order_confirmation.php?order_id=$order_id");
exit;
?>

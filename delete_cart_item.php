<?php
require_once('db.php');
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to delete items.']);
    exit;
}

if (isset($_POST['cart_ids'])) {
    $cart_ids = json_decode($_POST['cart_ids'], true); // Decode the JSON array

    if (empty($cart_ids)) {
        echo json_encode(['success' => false, 'message' => 'No items selected.']);
        exit;
    }

    $username = $_SESSION['username'];
    $cart_ids_placeholder = implode(',', array_fill(0, count($cart_ids), '?')); // Create placeholders for the cart ids

    // Prepare the SQL query to delete selected cart items
    $query = "DELETE FROM cart WHERE cart_id IN ($cart_ids_placeholder) AND username = ?";
    $stmt = $conn->prepare($query);

    if ($stmt) {
        // Bind the cart IDs and username as parameters
        $stmt->bind_param(str_repeat('i', count($cart_ids)) . 's', ...$cart_ids, $username);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Items deleted successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error deleting items.']);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Error preparing the query.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No cart IDs provided.']);
}

$conn->close();
?>

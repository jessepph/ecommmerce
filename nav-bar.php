<?php
// Include necessary files for DB connection and functions
require_once('db.php');
include("myfunctions.php");

session_start();

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Get logged-in username
$username = $_SESSION['username'];

// Database connection parameters
$servername = "localhost";
$dbusername = "root";
$password = "";
$dbname = "market";

// Create a new database connection
$conn = new mysqli($servername, $dbusername, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Update the cart with product names
$updateQuery = "
    UPDATE cart c
    JOIN products p ON c.name = p.product_id
    SET c.name = p.product_name
    WHERE c.username = ?";
$stmt = $conn->prepare($updateQuery);
if (!$stmt) {
    die("SQL error: " . $conn->error);
}

$stmt->bind_param("s", $username);

if ($stmt->execute()) {
    // Cart updated successfully (optional feedback)
    // echo "Cart updated with product names.";
} else {
    die("Error updating cart: " . $stmt->error);
}
$stmt->close();

// Query to get cart items for the logged-in user
$query = "SELECT c.cart_id, c.product_id, c.quantity, c.total_price, c.name
          FROM cart c
          WHERE c.username = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("SQL error: " . $conn->error);
}

$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

// Display cart items
if ($result->num_rows > 0) {
    echo "<h1>Your Cart</h1><table border='1'>
            <tr>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Total Price</th>
            </tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row['name']) . "</td>
                <td>" . htmlspecialchars($row['quantity']) . "</td>
                <td>$" . number_format($row['total_price'], 2) . "</td>
              </tr>";
    }
    echo "</table>";
    echo '<form action="process_checkout.php" method="POST">
            <input type="submit" value="Proceed to Payment" />
          </form>';
} else {
    echo "Your cart is empty.";
}

$stmt->free_result();

// Retrieve user role
$query = "SELECT account_role FROM register WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($user_role);
$stmt->fetch();
$stmt->close();

// Determine whether to show control panel
$showControlPanel = ($user_role !== 'Buyer');

// Query to get unread message count
$query = "SELECT COUNT(*) AS unread_count FROM messages WHERE ToUser = ? AND is_read = 0";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$unread_count = $row['unread_count'] ?? 0;
$stmt->close();

// Query to get total items in cart
$query = "SELECT SUM(quantity) AS total_item_count FROM cart WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$totalItemCount = $row['total_item_count'] ?? 0;
$stmt->close();

// Query to get the user's order count
$query = "SELECT IFNULL(order_count, 0) AS order_count FROM cart WHERE username = ? LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$order_count = $row['order_count'] ?? 0;
$stmt->close();

// Determine badge classes based on counts
$cart_badge_class = $totalItemCount > 0 ? 'badge-danger' : 'badge-grey';
$cart_badge_text = $totalItemCount > 0 ? $totalItemCount : '0';

$message_badge_class = $unread_count > 0 ? 'badge-danger' : 'badge-grey';
$message_badge_text = $unread_count > 0 ? $unread_count : '0';

// Close DB connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asmodeus - Homepage</title>
    <link rel="stylesheet" href="Listings_files/style.css">
    <link rel="stylesheet" href="Listings_files/fontawesome-all.min.css">
    <link rel="icon" href="img/pentagram.jpg" type="image/jpeg">
</head>
<body>
<div class="navigation">
    <div class="wrapper">
        <ul>
            <li class="nav-logo"><a href="homepage.php"><img src="Listings_files/logo_small.png" alt="Logo" style="height: 100%;"></a></li>
            <li class="active"><a href="homepage.php">Home</a></li>
            <li class="dropdown-link dropdown-large">
                <a href="orders.php?action=orders">Orders</a>
                <div class="dropdown-content right-dropdown">
                    <a href="processing.php">Processing</a>
                    <a href="dispatched.php">Dispatched</a>
                    <a href="completed.php">Completed</a>
                    <a href="disputed.php">Disputed</a>
                    <a href="canceled.php">Canceled</a>
                </div>
            </li>
            <li><a href="listings.php">Listings</a></li>
            <li class="dropdown-link dropdown-large">
                <a href="messages.php">Messages <span class="badge <?php echo $message_badge_class; ?>"><?php echo $message_badge_text; ?></span></a>
            </li>
            <li class="dropdown-link dropdown-large">
                <a href="wallet.php">Wallet</a>
            </li>
            <li class="dropdown-link dropdown-large">
                <a href="bug-report.php">Support</a>
            </li>
            <?php if ($showControlPanel): ?>
                <li class="dropdown-link dropdown-large">
                    <a href="control-panel.php">C Panel</a>
                    <div class="dropdown-content right-dropdown">
                        <a href="products.php">Products</a>
                        <a href="category.php">Categories</a>
                        <a href="add-product.php">Add Products</a>
                    </div>
                </li>
            <?php endif; ?>
            <li><a href="become-a-merchant.php"><b>Become A Merchant</b></a></li>
            <li class="right shopping-cart-link">
                <a href="cart.php">
                    <img src="cart.png" alt="Cart">
                    <span class="badge <?php echo $cart_badge_class; ?>"><?php echo $cart_badge_text; ?></span>
                </a>
            </li>
            <li class="right user-nav">
                <button class="dropbtn"><?php echo $_SESSION["username"]; ?> <div class="sprite sprite--caret"></div></button>
                <div class="dropdown-content">
                    <a href="profile-page.php">My Profile</a>
                    <a href="logout.php">Logout</a>
                </div>
            </li>
        </ul>
    </div>
</div>
</body>
</html>

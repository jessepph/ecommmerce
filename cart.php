<?php
include("myfunctions.php");
require_once("bitcoin_balance.php");
session_start();

// Ensure user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Database connection parameters
$servername = "localhost";
$dbusername = "root";
$password = "";
$dbname = "market";

// Create a new database connection
$conn = new mysqli($servername, $dbusername, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve the user's role from the database
$username = $_SESSION['username'];
$user_role = 'Buyer'; // Default value

$query = "SELECT account_role FROM register WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($user_role);
if (!$stmt->fetch()) {
    // Default to 'Buyer' if no role found
    $user_role = 'Buyer';
}
$stmt->close();

// Check user role and conditionally show C Panel
$showControlPanel = ($user_role !== 'Buyer');

// Get unread message count for the current user
$query = "SELECT COUNT(*) AS unread_count FROM messages WHERE ToUser = ? AND is_read = 0";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($unread_count);
if (!$stmt->fetch()) {
    // Default to 0 if no unread messages
    $unread_count = 0;
}
$stmt->close();

// Get the total item count in the cart for the user
$query = "SELECT SUM(quantity) AS total_items FROM cart WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($totalItemCount);
if (!$stmt->fetch()) {
    // Default to 0 if no items in the cart
    $totalItemCount = 0;
}
$stmt->close();

// Define badge colors based on the counts
$badge_color = $unread_count > 0 ? 'red' : 'grey';
$badge_class = $unread_count > 0 ? 'badge-danger' : '';
$cart_badge_color = $totalItemCount > 0 ? 'red' : 'grey';
$cart_badge_text = $totalItemCount > 0 ? $totalItemCount : '0';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Asmodeus - Homepage</title>
    <link rel="stylesheet" href="Listings_files/flexboxgrid.min.css">
    <link rel="stylesheet" href="Listings_files/fontawesome-all.min.css">
    <link rel="stylesheet" href="Listings_files/style.css">
    <link rel="stylesheet" href="Listings_files/main.css">
    <link rel="stylesheet" href="Listings_files/responsive.css">
    <link rel="icon" href="img/pentagram.jpg" type="image/jpeg">
    <style>
        .sidebar-navigation ul li {
            display: block;
            height: auto;
            max-height: 50px;
            overflow: visible;
            transition: max-height 0.15s ease-out;
        }
        .container {
            padding: 20px;
            margin: 0 auto;
            max-width: 1200px;
        }
        .container-header {
            font-size: 24px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
<div class="navigation" style="margin-top:0px;">
    <div class="wrapper">
        <ul>
            <li class="nav-logo"><a href="homepage.php"><img src="Listings_files/logo_small.png" style="height: 100%;"></a></li>
            <div class="responsive-menu">
                <li class="menu-toggler"><a href="homepage.php">Navigation&nbsp; <div class="sprite sprite--caret" style="float: none; display: inline-block; margin-left:5px;"></div></a></li>
                <div class="menu-links">
                    <li class="active"><a href="homepage.php">Home</a></li>
                    <li class="dropdown-link dropdown-large">
                        <a href="orders.php?action=orders" class="dropbtn">
                            Orders
                        </a>
                        <div class="dropdown-content right-dropdown">
                            <a href="processing.php">Processing&nbsp; <span class="badge badge-secondary right">0</span></a>
                            <a href="dispatched.php">Dispatched&nbsp; <span class="badge badge-danger right">1</span></a>
                            <a href="completed.php">Completed&nbsp; <span class="badge badge-danger right">1</span></a>
                            <a href="disputed.php">Disputed&nbsp; <span class="badge badge-secondary right">0</span></a>
                            <a href="canceled.php">Canceled</a>
                        </div>
                    </li>
                    <?php 
                    $current_user = $_SESSION['username'];

// Sanitize user input
$safe_current_user = $conn->real_escape_string($current_user);

// Query to count unread messages for the current user
$sql = "SELECT COUNT(*) AS unread_count FROM messages WHERE ToUser = '$safe_current_user' AND is_read = 0";
$result = $conn->query($sql);

// Initialize variables for badge
$badge_color = 'grey'; // Default color
$badge_text = '0';     // Default text

if ($result) {
    $row = $result->fetch_assoc();
    $unread_count = $row['unread_count'];
    
    if ($unread_count > 0) {
        $badge_color = 'red';   // Set badge color to red if there are unread messages
        $badge_text = $unread_count; // Display the number of unread messages
    }
} else {
    // Handle query error
    echo "Error fetching unread messages count: " . $conn->error;
}

                    
                    ?>
                    <li class=""><a href="listings.php">Listings</a></li>
                    <li class="dropdown-link dropdown-large">
                        <a href="messages.php" class="dropbtn">
    Messages&nbsp;
    <span class="badge" style="
        padding: 0.3em 0.4em;
        font-size: 75%;
        font-weight: 700;
        border-radius: 0.25rem;
        background-color: <?php echo htmlspecialchars($badge_color); ?>;
        color: white;
        ">
        <?php echo htmlspecialchars($badge_text); ?>
    </span>
</a>
                        <div class="dropdown-content right-dropdown">
                            <a href="compose-message.php?action=compose">Compose Message</a>
                            <a href="pm_inbox.php">Inbox</a>
                            <a href="pm_outbox.php">Sent Items</a>
                        </div>
                    </li>
                    <li class="dropdown-link dropdown-large">
                        <a href="wallet.php?action=wallet" class="dropbtn">Wallet</a>
                        <div class="dropdown-content right-dropdown">
                            <a href="exchange.php?action=exchange">Exchange</a>
                        </div>
                    </li>
                    <li class="dropdown-link dropdown-large">
                        <a href="bug-report.php" class="dropbtn">
                            Support
                        </a>
                        <div class="dropdown-content right-dropdown">
                            <a href="faq.php">F.A.Q</a>
                            <a href="support-tickets-and-bug-reports.php">Support Tickets</a>
                            <a href="bug-report.php">Report Bug</a>
                        </div>
                    </li>
                </div>
            </div>

            <li class="dropdown-link user-nav right fix-gap">
                <button class="dropbtn" style="margin-top:10px;"><?php echo "" . $_SESSION["username"] . "<br>"; ?>&nbsp; <div class="sprite sprite--caret" style="float: right; top: 1px;"></div></button>
                <div class="dropdown-content">
                    <div class="user-balance">
                        <span class="shadow-text">Balances</span><br>
                        <span class="balance">$</span>4.73 <sup>0.00016300 BTC</sup><br><span class="balance">$</span>0.23 <sup>0.00141754 XMR</sup><br>
                    </div>
                    <a href="profile-page.php?id=60Agent">My Profile</a>
                    <a href="theme.php">Night Mode</a>
                    <a href="usercp.php">User CP</a>
                    <a href="logout.php">Logout</a>
                </div>
            </li>
            <?php
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
            // SQL query to count unread messages for the current user
$query = "
    SELECT COUNT(*) AS unread_count
    FROM messages
    WHERE ToUser = ? AND is_read = 0
";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$unread_count = $row['unread_count'];

// Determine the background color based on unread count
$badge_class = $unread_count > 0 ? 'badge-danger' : 'badge-grey';
$badge_text2 = $unread_count > 0 ? $unread_count : '';
$current_user = $_SESSION['username'];

// Sanitize user input
$safe_username = $conn->real_escape_string($current_user);
// Prepare a statement to get the order count for the current user
$stmt = $conn->prepare("SELECT IFNULL(order_count, 0) AS order_count FROM cart WHERE username = ? LIMIT 1");
$stmt->bind_param("s", $safe_username);

// Execute the statement
if ($stmt->execute()) {
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $order_count = $row['order_count'];
    } else {
        $order_count = 0; // No orders found
    }
} else {
    // Handle query error
    error_log("Query error: " . $stmt->error);
    $order_count = 0;
}


// Query to get the total order count for the user
$sql = "SELECT SUM(order_count) AS total_order_count FROM cart WHERE username = '$safe_username'";
$result = $conn->query($sql);

// Initialize total order count
$total_order_count = 0;

// Check if query was successful
if ($result) {
    $row = $result->fetch_assoc();
    $total_order_count = $row['total_order_count'];
} else {
    // Handle query error
    echo "Error fetching total order count: " . $conn->error;
}


// Query to get cart items for the user
$query = "SELECT product_id, username, quantity, total_price FROM cart WHERE username = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("s", $user); // Use "s" for string binding
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

// Calculate total price and total item count
$totalPrice = 0;
$totalItemCount = 0; // Initialize total item count

while ($row = $result->fetch_assoc()) {
    $totalPrice += floatval($row['total_price']); // Sum the total price
    $totalItemCount += intval($row['quantity']); // Sum the total quantity
}


$conn->close();


// Determine badge styles and content
if ($order_count > 0) {
    $badge_class = 'badge-danger';
    $badge_color = 'red';
    $badge_text_color = 'white';
    $badge_text = $order_count;
} else {
    $badge_class = '';
    $badge_color = 'grey';
    $badge_text_color = 'white';
    $badge_text = '0';
}
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

// Get the current username from the session
$current_user = $_SESSION['username'] ?? '';
$safe_username = $conn->real_escape_string($current_user);

// Query to get cart items for the user
$query = "SELECT quantity FROM cart WHERE username = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("s", $safe_username);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

// Calculate total item count
$totalItemCount = 0; // Initialize total item count

while ($row = $result->fetch_assoc()) {
    $totalItemCount += intval($row['quantity']); // Sum the total quantity
}


            
            ?>
  <li class="right shopping-cart-link">
                    <a href="cart.php">
                        <img src="cart.png" style="
                        width: 20px;
                        height: 25px;
                        display: inline-block;
                        margin-top: 20px;
                        float: none;
                        ">
                        &nbsp;<span class="badge" style="
                        padding: 0.3em 0.4em;
                        font-size: 75%;
                        font-weight: 700;
                        top: 24px;
                        line-height: 1;
                        position: absolute;
                        text-align: center;
                        white-space: nowrap;
                        vertical-align: baseline;
                        color: white;
                        border-radius: 0.25rem;
                        background-color: <?php echo htmlspecialchars($badge_color); ?>;">
                        <?php echo htmlspecialchars($totalItemCount); ?>
                        </span>               
                    </a>
                </li>
<li class="right shopping-cart-link">
    <a href="messages.php">
        <img src="alert-bell.png" style="width: 20px; height: 25px; display: inline-block; margin-top: 20px; float:none;">
        &nbsp;<span class="badge <?php echo $badge_class; ?>" style="padding: 0.3em 0.4em; font-size: 75%; font-weight: 700; top: 24px; line-height: 1; position: absolute; text-align: center; white-space: nowrap; vertical-align: baseline; color:white; border-radius: 0.25rem; background-color:<?php echo $unread_count > 0 ? 'red' : 'grey'; ?>;"><?php echo $badge_text2 > 0 ? $badge_text2 : '0'; ?></span>
    </a>
</li>
            <?php if ($showControlPanel): ?>
                <li class="dropdown-link dropdown-large" style="margin-left:260px; position:absolute; width:210px; margin-top:-15px;">
                    <a href="control-panel.php" class="dropbtn">
                        <p>C Panel</p>
                    </a>
                    <div class="dropdown-content right-dropdown">
                        <a href="products.php">Products</a>
                        <a href="category.php">All Categories</a>
                        <a href="add-category.php">Add Category</a>
                        <a href="add-product.php">Add Products</a>
                        <a href="category.php">List Of Categories</a>
                        <a href="categories.php">View Categories</a>
                        <a href="add-category.php">Categories</a>
                        <a href="edit-category.php">Edit Category</a>
                    </div>
                </li>
            <?php endif; ?>
            <li class="right fix-gap" style="list-style:none;"><a href="become-a-merchant.php"><b>Become A Merchant</b></a></li>
        </ul>
    </div>
</div>
<?php
require_once("db.php");
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    echo "<h1>You must be logged in to view your cart.</h1>";
    exit;
}

$username = $_SESSION['username'];  // Get logged-in user

// Query to get cart items for the user, including product name, price, image, and vendor bitcoin_wallet_address
$query = "
    SELECT c.product_id, c.name, c.quantity, c.total_price, p.product_name, p.price AS unit_price, p.image AS product_image, p.bitcoin_wallet_address
    FROM cart c
    LEFT JOIN products p ON c.product_id = p.product_id
    WHERE c.username = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);  // Bind the username parameter securely
$stmt->execute();  // Execute the query
$result = $stmt->get_result();  // Get the result set

// Debugging: check if any rows are returned
if ($result->num_rows === 0) {
    echo "<h1>Your cart is empty.</h1>";
    exit;
}

// Display cart items in the table
echo "<h1>Your Cart</h1>";

echo "<form action='cart.php' method='POST'>
        <div style='width: 80%; margin: 0 auto; background-color: #fff; padding: 20px; box-shadow: 0 0 10px rgba(0,0,0,0.1);'>";

echo "<table border='1' cellpadding='10' style='width: 100%; border-collapse: collapse; margin: 0 auto;'>
        <thead>
            <tr>
                <th>Select</th>
                <th>Product Name</th>
                <th>Image</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total Price</th>
                <th>Vendor Bitcoin Wallet Address</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>";

$totalCartPrice = 0;
$products = [];  // Store the products for order confirmation

// Loop through the cart items and display them
while ($row = $result->fetch_assoc()) {
    $productName = htmlspecialchars($row['name']);
    $productImage = htmlspecialchars($row['product_image']);
    $quantity = (int)$row['quantity'];  // Ensure quantity is an integer
    $unitPrice = number_format(floatval($row['unit_price']), 2);  // Format unit price
    $totalPriceItem = number_format(floatval($row['total_price']), 2);  // Format total price
    $productId = htmlspecialchars($row['product_id']);  // Sanitize product ID
    $vendorBtcAddress = htmlspecialchars($row['bitcoin_wallet_address']);

    // Add item total price to the cart total price
    $totalCartPrice += floatval($row['total_price']);
    
    // Store product data for later use in the order confirmation
    $products[] = [
        'product_id' => $row['product_id'],
        'quantity' => $quantity,
        'total_price' => $row['total_price'],
        'bitcoin_wallet_btc_address' => $vendorBtcAddress  // Ensure address is assigned
    ];

    // Display the item row in the cart table
    echo "<tr>
            <td><input type='checkbox' name='selected_items[]' value='$productId'></td>
            <td>$productName</td>
            <td><img src='$productImage' alt='$productName' style='width: 100px; height: 100px;'></td>
            <td>$quantity</td>
            <td>USD $unitPrice</td>
            <td>USD $totalPriceItem</td>
            <td>$vendorBtcAddress</td>
            <td><a href='cart.php?remove_id=$productId'>Remove</a></td>
        </tr>";
}

echo "</tbody></table>";

// Display total cart price
echo "<p><strong>Total Cart Price: USD " . number_format($totalCartPrice, 2) . "</strong></p>";

// Check if bitcoin_wallet_address exists
$vendorBtcAddress = isset($row['bitcoin_wallet_address']) ? htmlspecialchars($row['bitcoin_wallet_address']) : null;

// Confirm Order Button
echo "<input type='submit' name='confirm_order' value='Confirm Order and Proceed to Blockchain' style='padding: 10px 20px; background-color: #4CAF50; color: white; border: none; cursor: pointer;'>";

echo "</div></form>";

// Close database connection
$conn->close();
?>





















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

        span {
  margin-left: 0px;
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


// Check if user is logged in
if (!isset($_SESSION['username'])) {
    die("User is not logged in.");
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

// Fetch user data
$user = $_SESSION['username'];
$query = "SELECT total_spent_usd, bitcoin_balance, total_btc, total_xmr, dateJoined, account_role FROM register WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $_SESSION["total_spent_usd"] = $row['total_spent_usd'];
    $bitcoin_balance = $row['bitcoin_balance'];
    $total_btc = $row['total_btc'];
    $total_xmr = $row['total_xmr'];
    $dateJoined = $row['dateJoined'];
    $account_role = $row['account_role'];
} else {
    $_SESSION["total_spent_usd"] = 0; // Default to 0
    $bitcoin_balance = $total_btc = $total_xmr = 0.0; // Default values
}

$stmt->close();
$conn->close();

// Assuming you have the current price of XMR
$xmr_price = 150; // Example price; replace with dynamic value if needed
?>

<div class="wrapper">
    <div class="row" style="margin-bottom: 1rem;">
        <div class="col-md-3 no-padding-left">
            <div class="container" style="height: 100%;">
                <div class="row">
                    <div class="col col-md-4">
                        <img src="Bohemia%20-%20Homepage_files/image_002.png" alt="User Image">
                    </div>
                    <div class="col col-md-8">
                        <div class="user-detail-row">
                            <strong><span style="color:grey;"><?php echo htmlspecialchars($_SESSION["username"]); ?></span></strong>
                        </div>
                        <div class="user-detail-row">
                            <div>Status:</div>
                            <div><span style="color:grey;"><?php echo htmlspecialchars($account_role); ?></span></div>
                        </div>
                        <div class="user-detail-row">
                            <div>Joined:</div>
                            <div><?php echo htmlspecialchars($dateJoined); ?></div>
                        </div>
                        <div class="user-detail-row">
                            <div style="display: flex; align-items: center; justify-content: center">
                                <?php
                                $total_spent = $_SESSION["total_spent_usd"];
                                echo "Total Spent:"; ?>
                                <div style="color: <?php echo $total_spent > 0 ? "green" : "red"; ?>; margin-left: 30px;">
                                    <?php
                                    $locale = "$";
                                    echo $locale . " " . number_format($total_spent, 2, ".", ","); ?>
                                </div>
                            </div>
                        </div>







<?php


// Fetch current BTC price from CoinGecko API
function getBitcoinPrice() {
    $url = 'https://api.coingecko.com/api/v3/simple/price?ids=bitcoin&vs_currencies=usd';
    $response = file_get_contents($url);
    $data = json_decode($response, true);
    return $data['bitcoin']['usd'] ?? null; // Return price or null if not available
}

// Get the current price of BTC
$market_share_price = getBitcoinPrice();

if ($market_share_price !== null) {
    // Assuming $bitcoin_balance and $total_xmr are already defined
    $total_btc = $bitcoin_balance * $market_share_price; // Calculate total BTC in USD
} else {
    $total_btc = 0; // Default value if price couldn't be fetched
}




?>







                       <div class="user-balance" style="margin-top: 1em;">
    <strong>Balances</strong><br>
    <strong class="balance"><?php echo number_format((float)$bitcoin_balance, 8, '.', ''); ?> BTC</strong>
    <sup>$<?php echo isset($total_btc) ? number_format((float)$total_btc, 2, '.', '') : 'N/A'; ?></sup><br>
    <strong class="balance"><?php echo number_format((float)$total_xmr, 8, '.', ''); ?> XMR</strong>
    <sup>$<?php echo number_format((float)$total_xmr * $xmr_price, 2, '.', ''); ?></sup><br>
</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 no-padding-left">
            <div class="container nopadding" style="height: 100%;">
                <div class="container-header">
                    <div class="sprite sprite--info"></div>&nbsp; Information Board
                </div>
                <div class="container-content">
                    Your personal login phrase is: <b>Hello...</b> - if this is incorrect, please leave immediately and seek a new mirror.<br><br>
                    Please take precautions prior to placing any orders, this means vetting the merchants you wish to use, and encrypting any sensitive data with PGP.<br><br>
                    Always verify the mirror you are visiting; safety is key.
                </div>
            </div>
        </div>

        <div class="col-md-5 no-padding-left">
            <div class="container nopadding" style="height: 100%;">
                <div class="container-header">
                    <div class="sprite sprite--exchange"></div>&nbsp; Exchange Rates
                </div>
                <table class="table exchange-table" cellspacing="0" cellpadding="0">
                    <tbody>
                        <tr>
                            <th></th>
                            <th class="text-center">USD</th>
                            <th class="text-center">EUR</th>
                            <th class="text-center">GBP</th>
                            <th class="text-center">CAD</th>
                            <th class="text-center">AUD</th>
                        </tr>
                        <tr>
                            <td><strong><div class="sprite sprite--bitcoin" style="top:2px;"></div>&nbsp;Bitcoin</strong></td>
                            <td class="text-center"><?php include("usd-current-price-btc.php"); ?></td>
                            <td class="text-center"><?php include("euro-current-price-btc.php"); ?></td>
                            <td class="text-center"><?php include("GBP-current-price-btc.php"); ?></td>
                            <td class="text-center"><?php include("cad-current-price-btc.php"); ?></td>
                            <td class="text-center"><?php include("aud-current-price-btc.php"); ?></td>
                        </tr>
                        <tr>
                            <td><strong><div class="sprite sprite--monero" style="top:2px;"></div>&nbsp;Monero</strong></td>
                            <td class="text-center"><?php include("usd-current-price-monero.php"); ?></td>
                            <td class="text-center"><?php include("euro-current-price-monero.php"); ?></td>
                            <td class="text-center"><?php include("british-pound-current-price-monero.php"); ?></td>
                            <td class="text-center"><?php include("cad-current-price-monero.php"); ?></td>
                            <td class="text-center"><?php include("aud-current-price-monero.php"); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

             <?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "market";

// Create connection
$con = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Initialize search parameters
$productKeyword = isset($_GET['product_name']) ? $con->real_escape_string(trim($_GET['product_name'])) : '';
$categoryId = isset($_GET['catid']) ? intval($_GET['catid']) : 0;
$vendor = isset($_GET['vendor']) ? $con->real_escape_string(trim($_GET['vendor'])) : '';
$minPrice = isset($_GET['min_price']) ? floatval($_GET['min_price']) : 0;
$maxPrice = isset($_GET['max_price']) ? floatval($_GET['max_price']) : 0;

// Build the SQL query for products
$sql = "SELECT * FROM products WHERE 1=1";
if ($productKeyword) {
    $sql .= " AND name LIKE '%$productKeyword%'";
}
if ($categoryId) {
    $sql .= " AND category_id = $categoryId";
}
if ($vendor) {
    $sql .= " AND vendor_name LIKE '%$vendor%'";
}
if ($minPrice > 0) {
    $sql .= " AND selling_price >= $minPrice";
}
if ($maxPrice > 0) {
    $sql .= " AND selling_price <= $maxPrice";
}

// Check if the search form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['query'])) {
    $query = htmlspecialchars($_GET['query']);
    $sortby = htmlspecialchars($_GET['sortby']);
    $priceFrom = htmlspecialchars($_GET['priceFrom']);
    $priceTo = htmlspecialchars($_GET['priceTo']);
    $shipFrom = htmlspecialchars($_GET['shipFrom']);
    $shipTo = htmlspecialchars($_GET['shipTo']);
    $autoship = isset($_GET['autoship']) ? 1 : 0;
    $currencies = isset($_GET['currencies']) ? $_GET['currencies'] : [];
    $type = htmlspecialchars($_GET['type']);

    // Prepare your SQL query to fetch the filtered results based on the captured parameters
    // For example:
    $sql = "SELECT * FROM products WHERE name LIKE '%$query%'";

    // Add additional filters based on other parameters
    if ($priceFrom) {
        $sql .= " AND price >= $priceFrom";
    }
    if ($priceTo) {
        $sql .= " AND price <= $priceTo";
    }
    if ($shipFrom) {
        $sql .= " AND ships_from = '$shipFrom'";
    }
    if ($shipTo) {
        $sql .= " AND ships_to = '$shipTo'";
    }
    if ($autoship) {
        $sql .= " AND autoship = 1";
    }
    if (!empty($currencies)) {
        $currenciesList = implode("','", $currencies);
        $sql .= " AND currency IN ('$currenciesList')";
    }
    if ($type && $type != 'all') {
        $sql .= " AND product_type = '$type'";
    }

    // Execute the query and fetch results (use prepared statements for security)
    // Example with PDO:
    /*
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll();
    */

    // Display results (this part will depend on your existing HTML structure)
    // foreach ($results as $item) {
    //     echo "<div>{$item['name']} - {$item['price']}</div>";
    // }
}

// Execute the query
$result = $con->query($sql);
?>
        <div class="row">
            <div class="col-md-3 sidebar-navigation">
                  <div class="container listing-sorting detail-container" style="margin-top:15px;">
    <div class="container-header">
        <div class="sprite sprite--search"></div>&nbsp; Advanced Search
    </div>
    <div class="container-content">
        <form action="listings.php" method="GET">
            <div class="form-group">
                <div><label>Search by Keyword / Merchant Name</label></div>
                <input type="text" class="form-control" name="query" required>
            </div>
            <div class="form-group">
                <div><label>Sort by</label></div>
                <select class="form-control" name="sortby">
                    <option value="mosthighest" selected="selected">Highest to lowest price</option>
                    <option value="cheapest">Lowest to highest price</option>
                    <option value="oldest">Oldest to newest</option>
                    <option value="newest">Newest to oldest</option>
                    <option value="mostrated">Highest rated</option>
                </select>
            </div>
            <div class="form-inline">
                <div><label>Price range</label></div>
                <div class="form-group" style="width: 45%;">
                    <input type="number" name="priceFrom" step="0.01" class="form-control" placeholder="Price from">
                </div>
                <div class="form-group" style="float: right; width: 45%;">
                    <input type="number" name="priceTo" step="0.01" class="form-control" placeholder="Price to">
                </div>
            </div>
            <div class="form-group">
                <div><label>Ships from</label></div>
                <select class="form-control" name="shipFrom">
                    <option selected="selected"></option>
                       <option value="1">Afghanistan</option><option value="2">Albania</option><option value="3">Algeria</option><option value="4">American Samoa</option><option value="5">Andorra</option><option value="6">Angola</option><option value="7">Anguilla</option><option value="8">Antarctica</option><option value="9">Antigua and Barbuda</option><option value="10">Argentina</option><option value="11">Armenia</option><option value="12">Aruba</option><option value="13">Australia</option><option value="14">Austria</option><option value="15">Azerbaijan</option><option value="16">Bahamas</option><option value="17">Bahrain</option><option value="18">Bangladesh</option><option value="19">Barbados</option><option value="20">Belarus</option><option value="21">Belgium</option><option value="22">Belize</option><option value="23">Benin</option><option value="24">Bermuda</option><option value="25">Bhutan</option><option value="26">Bolivia</option><option value="27">Bosnia and Herzegovina</option><option value="28">Botswana</option><option value="29">Bouvet Island</option><option value="30">Brazil</option><option value="31">British Indian Ocean Territory</option><option value="32">Brunei Darussalam</option><option value="33">Bulgaria</option><option value="34">Burkina Faso</option><option value="35">Burundi</option><option value="36">Cambodia</option><option value="37">Cameroon</option><option value="38">Canada</option><option value="39">Cape Verde</option><option value="40">Cayman Islands</option><option value="41">Central African Republic</option><option value="42">Chad</option><option value="43">Chile</option><option value="44">China</option><option value="45">Christmas Island</option><option value="46">Cocos (Keeling) Islands</option><option value="47">Colombia</option><option value="48">Comoros</option><option value="51">Cook Islands</option><option value="52">Costa Rica</option><option value="53">Croatia (Hrvatska)</option><option value="54">Cuba</option><option value="55">Cyprus</option><option value="56">Czech Republic</option><option value="49">Democratic Republic of the Congo</option><option value="57">Denmark</option><option value="58">Djibouti</option><option value="59">Dominica</option><option value="60">Dominican Republic</option><option value="61">East Timor</option><option value="62">Ecuador</option><option value="63">Egypt</option><option value="64">El Salvador</option><option value="65">Equatorial Guinea</option><option value="66">Eritrea</option><option value="67">Estonia</option><option value="68">Ethiopia</option><option value="69">Falkland Islands (Malvinas)</option><option value="70">Faroe Islands</option><option value="71">Fiji</option><option value="72">Finland</option><option value="73">France</option><option value="74">France, Metropolitan</option><option value="75">French Guiana</option><option value="76">French Polynesia</option><option value="77">French Southern Territories</option><option value="78">Gabon</option><option value="79">Gambia</option><option value="80">Georgia</option><option value="81">Germany</option><option value="82">Ghana</option><option value="83">Gibraltar</option><option value="85">Greece</option><option value="86">Greenland</option><option value="87">Grenada</option><option value="88">Guadeloupe</option><option value="89">Guam</option><option value="90">Guatemala</option><option value="84">Guernsey</option><option value="91">Guinea</option><option value="92">Guinea-Bissau</option><option value="93">Guyana</option><option value="94">Haiti</option><option value="95">Heard and Mc Donald Islands</option><option value="96">Honduras</option><option value="97">Hong Kong</option><option value="98">Hungary</option><option value="99">Iceland</option><option value="100">India</option><option value="102">Indonesia</option><option value="103">Iran (Islamic Republic of)</option><option value="104">Iraq</option><option value="105">Ireland</option><option value="101">Isle of Man</option><option value="106">Israel</option><option value="107">Italy</option><option value="108">Ivory Coast</option><option value="110">Jamaica</option><option value="111">Japan</option><option value="109">Jersey</option><option value="112">Jordan</option><option value="113">Kazakhstan</option><option value="114">Kenya</option><option value="115">Kiribati</option><option value="116">Korea, Democratic People's Republic of</option><option value="117">Korea, Republic of</option><option value="118">Kosovo</option><option value="119">Kuwait</option><option value="120">Kyrgyzstan</option><option value="121">Lao People's Democratic Republic</option><option value="122">Latvia</option><option value="123">Lebanon</option><option value="124">Lesotho</option><option value="125">Liberia</option><option value="126">Libyan Arab Jamahiriya</option><option value="127">Liechtenstein</option><option value="128">Lithuania</option><option value="129">Luxembourg</option><option value="130">Macau</option><option value="132">Madagascar</option><option value="133">Malawi</option><option value="134">Malaysia</option><option value="135">Maldives</option><option value="136">Mali</option><option value="137">Malta</option><option value="138">Marshall Islands</option><option value="139">Martinique</option><option value="140">Mauritania</option><option value="141">Mauritius</option><option value="142">Mayotte</option><option value="143">Mexico</option><option value="144">Micronesia, Federated States of</option><option value="145">Moldova, Republic of</option><option value="146">Monaco</option><option value="147">Mongolia</option><option value="148">Montenegro</option><option value="149">Montserrat</option><option value="150">Morocco</option><option value="151">Mozambique</option><option value="152">Myanmar</option><option value="153">Namibia</option><option value="154">Nauru</option><option value="155">Nepal</option><option value="156">Netherlands</option><option value="157">Netherlands Antilles</option><option value="158">New Caledonia</option><option value="159">New Zealand</option><option value="160">Nicaragua</option><option value="161">Niger</option><option value="162">Nigeria</option><option value="163">Niue</option><option value="164">Norfolk Island</option><option value="131">North Macedonia</option><option value="165">Northern Mariana Islands</option><option value="166">Norway</option><option value="167">Oman</option><option value="168">Pakistan</option><option value="169">Palau</option><option value="170">Palestine</option><option value="171">Panama</option><option value="172">Papua New Guinea</option><option value="173">Paraguay</option><option value="174">Peru</option><option value="175">Philippines</option><option value="176">Pitcairn</option><option value="177">Poland</option><option value="178">Portugal</option><option value="179">Puerto Rico</option><option value="180">Qatar</option><option value="50">Republic of Congo</option><option value="181">Reunion</option><option value="182">Romania</option><option value="183">Russian Federation</option><option value="184">Rwanda</option><option value="185">Saint Kitts and Nevis</option><option value="186">Saint Lucia</option><option value="187">Saint Vincent and the Grenadines</option><option value="188">Samoa</option><option value="189">San Marino</option><option value="190">Sao Tome and Principe</option><option value="191">Saudi Arabia</option><option value="192">Senegal</option><option value="193">Serbia</option><option value="194">Seychelles</option><option value="195">Sierra Leone</option><option value="196">Singapore</option><option value="197">Slovakia</option><option value="198">Slovenia</option><option value="199">Solomon Islands</option><option value="200">Somalia</option><option value="201">South Africa</option><option value="202">South Georgia South Sandwich Islands</option><option value="203">South Sudan</option><option value="204">Spain</option><option value="205">Sri Lanka</option><option value="206">St. Helena</option><option value="207">St. Pierre and Miquelon</option><option value="208">Sudan</option><option value="209">Suriname</option><option value="210">Svalbard and Jan Mayen Islands</option><option value="211">Swaziland</option><option value="212">Sweden</option><option value="213">Switzerland</option><option value="214">Syrian Arab Republic</option><option value="215">Taiwan</option><option value="216">Tajikistan</option><option value="217">Tanzania, United Republic of</option><option value="218">Thailand</option><option value="219">Togo</option><option value="220">Tokelau</option><option value="221">Tonga</option><option value="222">Trinidad and Tobago</option><option value="223">Tunisia</option><option value="224">Turkey</option><option value="225">Turkmenistan</option><option value="226">Turks and Caicos Islands</option><option value="227">Tuvalu</option><option value="228">Uganda</option><option value="229">Ukraine</option><option value="230">United Arab Emirates</option><option value="231">United Kingdom</option><option value="233">United States</option><option value="234">Uruguay</option><option value="235">Uzbekistan</option><option value="236">Vanuatu</option><option value="237">Vatican City State</option><option value="238">Venezuela</option><option value="239">Vietnam</option><option value="240">Virgin Islands (British)</option><option value="241">Virgin Islands (U.S.)</option><option value="242">Wallis and Futuna Islands</option><option value="243">Western Sahara</option><option value="244">Yemen</option><option value="245">Zambia</option><option value="246">Zimbabwe</option>  
                </select>
            </div>
            <div class="form-group">
                <div><label>Ships to</label></div>
                <select class="form-control" name="shipTo">
                    <option selected="selected"></option>
                      <option value="1">Afghanistan</option><option value="2">Albania</option><option value="3">Algeria</option><option value="4">American Samoa</option><option value="5">Andorra</option><option value="6">Angola</option><option value="7">Anguilla</option><option value="8">Antarctica</option><option value="9">Antigua and Barbuda</option><option value="10">Argentina</option><option value="11">Armenia</option><option value="12">Aruba</option><option value="13">Australia</option><option value="14">Austria</option><option value="15">Azerbaijan</option><option value="16">Bahamas</option><option value="17">Bahrain</option><option value="18">Bangladesh</option><option value="19">Barbados</option><option value="20">Belarus</option><option value="21">Belgium</option><option value="22">Belize</option><option value="23">Benin</option><option value="24">Bermuda</option><option value="25">Bhutan</option><option value="26">Bolivia</option><option value="27">Bosnia and Herzegovina</option><option value="28">Botswana</option><option value="29">Bouvet Island</option><option value="30">Brazil</option><option value="31">British Indian Ocean Territory</option><option value="32">Brunei Darussalam</option><option value="33">Bulgaria</option><option value="34">Burkina Faso</option><option value="35">Burundi</option><option value="36">Cambodia</option><option value="37">Cameroon</option><option value="38">Canada</option><option value="39">Cape Verde</option><option value="40">Cayman Islands</option><option value="41">Central African Republic</option><option value="42">Chad</option><option value="43">Chile</option><option value="44">China</option><option value="45">Christmas Island</option><option value="46">Cocos (Keeling) Islands</option><option value="47">Colombia</option><option value="48">Comoros</option><option value="51">Cook Islands</option><option value="52">Costa Rica</option><option value="53">Croatia (Hrvatska)</option><option value="54">Cuba</option><option value="55">Cyprus</option><option value="56">Czech Republic</option><option value="49">Democratic Republic of the Congo</option><option value="57">Denmark</option><option value="58">Djibouti</option><option value="59">Dominica</option><option value="60">Dominican Republic</option><option value="61">East Timor</option><option value="62">Ecuador</option><option value="63">Egypt</option><option value="64">El Salvador</option><option value="65">Equatorial Guinea</option><option value="66">Eritrea</option><option value="67">Estonia</option><option value="68">Ethiopia</option><option value="69">Falkland Islands (Malvinas)</option><option value="70">Faroe Islands</option><option value="71">Fiji</option><option value="72">Finland</option><option value="73">France</option><option value="74">France, Metropolitan</option><option value="75">French Guiana</option><option value="76">French Polynesia</option><option value="77">French Southern Territories</option><option value="78">Gabon</option><option value="79">Gambia</option><option value="80">Georgia</option><option value="81">Germany</option><option value="82">Ghana</option><option value="83">Gibraltar</option><option value="85">Greece</option><option value="86">Greenland</option><option value="87">Grenada</option><option value="88">Guadeloupe</option><option value="89">Guam</option><option value="90">Guatemala</option><option value="84">Guernsey</option><option value="91">Guinea</option><option value="92">Guinea-Bissau</option><option value="93">Guyana</option><option value="94">Haiti</option><option value="95">Heard and Mc Donald Islands</option><option value="96">Honduras</option><option value="97">Hong Kong</option><option value="98">Hungary</option><option value="99">Iceland</option><option value="100">India</option><option value="102">Indonesia</option><option value="103">Iran (Islamic Republic of)</option><option value="104">Iraq</option><option value="105">Ireland</option><option value="101">Isle of Man</option><option value="106">Israel</option><option value="107">Italy</option><option value="108">Ivory Coast</option><option value="110">Jamaica</option><option value="111">Japan</option><option value="109">Jersey</option><option value="112">Jordan</option><option value="113">Kazakhstan</option><option value="114">Kenya</option><option value="115">Kiribati</option><option value="116">Korea, Democratic People's Republic of</option><option value="117">Korea, Republic of</option><option value="118">Kosovo</option><option value="119">Kuwait</option><option value="120">Kyrgyzstan</option><option value="121">Lao People's Democratic Republic</option><option value="122">Latvia</option><option value="123">Lebanon</option><option value="124">Lesotho</option><option value="125">Liberia</option><option value="126">Libyan Arab Jamahiriya</option><option value="127">Liechtenstein</option><option value="128">Lithuania</option><option value="129">Luxembourg</option><option value="130">Macau</option><option value="132">Madagascar</option><option value="133">Malawi</option><option value="134">Malaysia</option><option value="135">Maldives</option><option value="136">Mali</option><option value="137">Malta</option><option value="138">Marshall Islands</option><option value="139">Martinique</option><option value="140">Mauritania</option><option value="141">Mauritius</option><option value="142">Mayotte</option><option value="143">Mexico</option><option value="144">Micronesia, Federated States of</option><option value="145">Moldova, Republic of</option><option value="146">Monaco</option><option value="147">Mongolia</option><option value="148">Montenegro</option><option value="149">Montserrat</option><option value="150">Morocco</option><option value="151">Mozambique</option><option value="152">Myanmar</option><option value="153">Namibia</option><option value="154">Nauru</option><option value="155">Nepal</option><option value="156">Netherlands</option><option value="157">Netherlands Antilles</option><option value="158">New Caledonia</option><option value="159">New Zealand</option><option value="160">Nicaragua</option><option value="161">Niger</option><option value="162">Nigeria</option><option value="163">Niue</option><option value="164">Norfolk Island</option><option value="131">North Macedonia</option><option value="165">Northern Mariana Islands</option><option value="166">Norway</option><option value="167">Oman</option><option value="168">Pakistan</option><option value="169">Palau</option><option value="170">Palestine</option><option value="171">Panama</option><option value="172">Papua New Guinea</option><option value="173">Paraguay</option><option value="174">Peru</option><option value="175">Philippines</option><option value="176">Pitcairn</option><option value="177">Poland</option><option value="178">Portugal</option><option value="179">Puerto Rico</option><option value="180">Qatar</option><option value="50">Republic of Congo</option><option value="181">Reunion</option><option value="182">Romania</option><option value="183">Russian Federation</option><option value="184">Rwanda</option><option value="185">Saint Kitts and Nevis</option><option value="186">Saint Lucia</option><option value="187">Saint Vincent and the Grenadines</option><option value="188">Samoa</option><option value="189">San Marino</option><option value="190">Sao Tome and Principe</option><option value="191">Saudi Arabia</option><option value="192">Senegal</option><option value="193">Serbia</option><option value="194">Seychelles</option><option value="195">Sierra Leone</option><option value="196">Singapore</option><option value="197">Slovakia</option><option value="198">Slovenia</option><option value="199">Solomon Islands</option><option value="200">Somalia</option><option value="201">South Africa</option><option value="202">South Georgia South Sandwich Islands</option><option value="203">South Sudan</option><option value="204">Spain</option><option value="205">Sri Lanka</option><option value="206">St. Helena</option><option value="207">St. Pierre and Miquelon</option><option value="208">Sudan</option><option value="209">Suriname</option><option value="210">Svalbard and Jan Mayen Islands</option><option value="211">Swaziland</option><option value="212">Sweden</option><option value="213">Switzerland</option><option value="214">Syrian Arab Republic</option><option value="215">Taiwan</option><option value="216">Tajikistan</option><option value="217">Tanzania, United Republic of</option><option value="218">Thailand</option><option value="219">Togo</option><option value="220">Tokelau</option><option value="221">Tonga</option><option value="222">Trinidad and Tobago</option><option value="223">Tunisia</option><option value="224">Turkey</option><option value="225">Turkmenistan</option><option value="226">Turks and Caicos Islands</option><option value="227">Tuvalu</option><option value="228">Uganda</option><option value="229">Ukraine</option><option value="230">United Arab Emirates</option><option value="231">United Kingdom</option><option value="233">United States</option><option value="234">Uruguay</option><option value="235">Uzbekistan</option><option value="236">Vanuatu</option><option value="237">Vatican City State</option><option value="238">Venezuela</option><option value="239">Vietnam</option><option value="240">Virgin Islands (British)</option><option value="241">Virgin Islands (U.S.)</option><option value="242">Wallis and Futuna Islands</option><option value="243">Western Sahara</option><option value="244">Yemen</option><option value="245">Zambia</option><option value="246">Zimbabwe</option>  
                </select>
            </div>
            <div class="form-group">
                <label><input type="checkbox" name="autoship" value="1">&nbsp; Autoship</label>
            </div>
            <div class="form-group text-right">
                <button type="submit" class="btn btn-larger btn-blue" style="margin-bottom: 2px;">Search</button>
            </div>
        </form>
    </div>
</div>
                <div class="container listing-sorting detail-container" style="height: auto;">
        <div class="container-header">
            <div class="sprite sprite--diagram">
                
            </div>
            &nbsp; Browse Categories
        </div>
        <div style="overflow:visible;">
            <ul>
                <?php
                 // SQL query to count products
$sql = "SELECT COUNT(*) AS product_count FROM products";
$result = $con->query($sql);

if ($result->num_rows > 0) {
    // Output data of each row
    while($row = $result->fetch_assoc()) {
        // Display the product count inside the span with class "amount"
        echo '<a href="localhost/bohemia/listings.php"><input type="checkbox" name="catid" value="">
                    <b>Drugs and Chemicals</b>
                    <span class="amount">' . $row["product_count"] . '</span></a>';
    }
} else {
    echo "0 results";
}
                // SQL query to get categories and their product counts
                $query = "SELECT c.id AS category_id, c.name AS category_name, COUNT(p.id) AS product_count
                          FROM categories c
                          LEFT JOIN products p ON c.id = p.category_id
                          GROUP BY c.id, c.name";
                $result = $con->query($query);
                
                if ($result->num_rows > 0) {
                    // Output data for each category
                    while ($row = $result->fetch_assoc()) {
                        echo '<li>
                            <a href="http://wbxnudwhsfpta4ljzwm7mhkzph4ntpsvidubwr2gptfqyg4fohyrv7ad.onion/bohemia//listings.php?category_id=' . $row['category_id'] . '">
                                <input type="checkbox" name="catid" value="' . $row['category_id'] . '">
                                <b>' . htmlspecialchars($row['category_name']) . '</b>
                                <span class="amount">' . $row['product_count'] . '</span>
                            </a>
                        </li>';
                    }
                } else {
                    echo '<li>No categories found</li>';
                }
                ?>
            </ul>
        </div>
    </div>


                <div class="container nopadding" style="margin-top:100px;">
                    <div class="container-header">
                    <div class="sprite sprite--affiliate"></div>&nbsp; Affiliate Program
                    </div>
                    <div class="container-content">
                        <p>Copy your personal referral link and share it
 with your friends and general public. Passively receive 25% of the 
profit made on each purchase made by the user(s) you have referred.</p>
                        <input type="text" class="form-control" value="" readonly="readonly">
                    </div>
                    <table class="referrals-stats-table" cellspacing="0" cellpadding="0">
                        <tbody><tr>
                            <td><b>Total Referrals</b></td>
                            <td style="text-align: right; padding-right: 15px;">0</td>
                        </tr>
                        <tr>
                            <td><b>Total Earned</b></td>
                            <td style="text-align: right; padding-right: 15px;">$0.00</td>
                        </tr>
                    </tbody></table>
                </div>
                
            </div>
            <div class="col-md-9 sidebar-navigation">
    <h2 style="text-align:center;">Featured Listings</h2>
    <?php
// Function to retrieve all products
function getAllProducts($con) {
    $query = "SELECT * FROM products";
    $result = mysqli_query($con, $query);
    return $result;
}

// Function to retrieve category name by ID (using prepared statement)
function getCategoryNamePrepared($con, $category_id) {
    $query = "SELECT name FROM categories WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $category = $result->fetch_assoc();
    $stmt->close();
    return $category ? $category['name'] : 'Unknown';
}

// Function to retrieve products based on parameters
function getProducts($con, $categoryId = null, $categoryName = null) {
    if ($categoryId !== null && $categoryName !== null) {
        // Prepare a query to fetch products that match both category_id and category_name
        $query = "SELECT * FROM products WHERE category_id = ? AND category_name = ? ORDER BY id";
        $stmt = $con->prepare($query);
        $stmt->bind_param("is", $categoryId, $categoryName);
    } else {
        // Fetch all products if no category_id or category_name provided
        $query = "SELECT * FROM products ORDER BY id";
        $stmt = $con->prepare($query);
    }
    $stmt->execute();
    return $stmt->get_result();
}

// Function to get vendor info
function getVendorInfo3($conn, $vendorName) {
    $query = "SELECT vendor_rating, total_orders, level FROM vendors WHERE name = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $vendorName);
    $stmt->execute();
    $result = $stmt->get_result();
    $vendor = $result->fetch_assoc();
    $stmt->close();
    return $vendor;
}

// Function to convert USD to Bitcoin
function convertToBitcoin2($usdPrice) {
    // Placeholder for actual conversion logic
    $btcRate = 0.000023; // Example rate; replace with actual conversion logic or API call
    return $usdPrice * $btcRate;
}

// Function to format currency
function formatCurrency2($amount) {
    return number_format($amount, 2);
}

// Function to format price as currency
function formatCurrency($price) {
    return number_format($price, 2); // Format to 2 decimal places
}

// Function to convert USD price to Bitcoin (example implementation)
function convertToBitcoin3($usdPrice) {
    // This function should return the Bitcoin equivalent of the given USD price
    // Example conversion rate (you should use a real-time rate in a real application)
    $conversionRate = 0.000025; // Example conversion rate
    return $usdPrice * $conversionRate;
}

// Set the number of products per page
$products_per_page = 10;

// Retrieve products (assuming no category parameters)
$products_result = getProducts($con); // You might need to pass parameters if necessary

// Loop through the products and display them
while ($row = $products_result->fetch_assoc()) {
    // Format the selling price as currency
    $formatted_price = formatCurrency($row['selling_price']);
    // Convert USD selling price to Bitcoin
    $bitcoinPrice = number_format(convertToBitcoin2($row['selling_price']), 8); // Ensure the Bitcoin price is formatted as a string with up to 8 decimal places

    // Prepare product details for the link
    $productId = htmlspecialchars($row['id']);
    $productName = htmlspecialchars($row['product_name']);
    ?>
    <div class="product-listing">
        <div class="product-link">
            <div class="product">
                <div class="container">
                    <div class="product-photo">
                        <img src="uploads/<?= htmlspecialchars($row['image'] ?? 'default.jpg', ENT_QUOTES, 'UTF-8'); ?>" alt="<?= $productName; ?>">
                    </div>
                    <div class="product-details">
                        <div class="product-heading">
                            <h2><a href="http://wbxnudwhsfpta4ljzwm7mhkzph4ntpsvidubwr2gptfqyg4fohyrv7ad.onion/bohemia/product-view.php?name=<?= urlencode($productName); ?>" class="product-link"><?= $productName; ?></a></h2>
                            <span class="shadow-text smalltext">In <strong><?= htmlspecialchars(getCategoryNamePrepared($con, $row['category_id'])); ?></strong></span><br>
                            <span>
                                <b>Sold By <a href="#/profile.php?id=DrunkDragon"><?= htmlspecialchars($row['vendor_name']); ?></a>(<img src="images/icons8-star-48.png" style="width:15px;height10px;"/> <?= htmlspecialchars($row['vendor_rating']); ?>) </b>
                                <span class="badge badge-pill" style="background-color: black; color: white;">Level 1</span>
                                <span class="sprite sprite--shopping-cart" title="Total Sales"></span> <?= htmlspecialchars($row['times_sold_last_48_hr']); ?>
                            </span><br>
                            <b>Shipped From</b> <?= htmlspecialchars($row['ships_from'] ?? 'Unknown'); ?><br>
                            <b>Shipped To</b> <?= htmlspecialchars($row['ships_to'] ?? 'Unknown'); ?><br>
                        </div>
                        <div class="product-details-bottom">
                            <div class="sold-amount smalltext">Sold <?= htmlspecialchars($row['times_sold_last_48_hr']); ?> in the last 48 hours</div>
                            <span class="smalltext">Sold <?= htmlspecialchars($row['total_sold']); ?> in total</span>
                        </div>
                    </div>
                    <div class="product-price">
                        <span class="badge badge-primary">Unlimited Available</span>
                        <h2>USD <?= $formatted_price; ?></h2>
                        <span class="shadow-text smalltext boldtext"><?= $bitcoinPrice; ?> BTC</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}
?>

<div class="container nopadding">
    <div class="container-header">
        Latest Announcements &nbsp;―&nbsp; <a href="http://wbxnudwhsfpta4ljzwm7mhkzph4ntpsvidubwr2gptfqyg4fohyrv7ad.onion/bohemia/homepage.php">View All</a>
    </div>
    <div class="container-content">
        <!-- Announcement content here -->
        Hello, this site isn't for illegal activity or phishing it is merely a hobby project all listings and data are fake as of right now.
    </div>
</div>

<div class="col col-md-12" style="padding: 0;">
    <div class="container detail-container">
        <div class="container-header">
            <div class="sprite sprite--links"></div>&nbsp; Mirrors
        </div>
        <div class="detail-row">
            <div><a href="http://bohemiaobko4cecexkj5xmlaove6yn726dstp5wfw4pojjwp6762paqd.onion/">Mirror 1</a></div>
        </div>
        <div class="detail-row">
            <div><a href="http://bohemiaobbpsjvkexpdpnekqai2ebi32xgr6sbhdpapipv547rm6jhad.onion/">Mirror 2</a></div>
        </div>
        <div class="detail-row">
            <div><a href="http://bhmia6i7dnfazb7n6clhzhsiwscb3fijgjxtpuoihwyixeysb3oq.b32.i2p/">I2P Mirror 3</a></div>
        </div>
    </div>
</div>

<div style="text-align:center;font-weight:bold;">
    <!-- Links to other platforms -->
</div>
</div>
</div>
    
               

</body>
</html>
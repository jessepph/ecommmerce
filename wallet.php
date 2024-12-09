<?php
include("myfunctions.php");
session_start();
require("db.php");
//require("create-wallet.php");
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

// Ensure user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Retrieve the user's role from the database
$user_role = 'Buyer'; // Default value

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    
    // Prepare and execute the query to get the user's role
    $query = "SELECT account_role FROM register WHERE username = ?";
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($user_role);
        $stmt->fetch();
        $stmt->close();
    } else {
        // Handle preparation error
        die("Database query preparation failed: " . $conn->error);
    }
}

// Check user role and conditionally show C Panel
$showControlPanel = ($user_role = 'vendor' || 'admin');

// Get the category_id from the URL
$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;

// Function to get category name
function getCategoryName2($conn, $category_id) {
    $query = "SELECT name FROM categories WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $category = $result->fetch_assoc();
    $stmt->close();
    return $category ? $category['name'] : 'Unknown';
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

// Check if Alertify box should be displayed
$showAlertify = true; // You can set this dynamically if needed

// Function to generate Alertify script
//function getAlertifyScript() {
    /*return <<<EOD
    <script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css" />
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        alertify.alert('Attention', 'This is a test site it is for research purposes at this time. And is not being used in a real scenario all data is fake as is all products! Do not attempt to make changes or order without admin consent. I do not condone the use of illegal activities. And remove all liability off me as the admin.', function(){ 
            alertify.success('Understood'); 
        }).set('background-color', '#ff0000').set('color', '#ffffff');
    });
    </script>
EOD;
}*/

// Output Alertify script if needed
//if ($showAlertify) {
   // echo getAlertifyScript();
//}

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






// Assuming $conn is your database connection
$current_user = $_SESSION['username'];
$safe_current_user = $conn->real_escape_string($current_user);

// Query to count unread messages for the current user
$sql = "SELECT COUNT(*) AS unread_count FROM messages WHERE ToUser = '$safe_current_user' AND is_read = 0";
$result = $conn->query($sql);

// Initialize badge variables
$badge_color = 'grey'; // Default color for both badges
$badge_text = '0';     // Default text
$badge_class = '';     // Default class

if ($result) {
    $row = $result->fetch_assoc();
    $unread_count = $row['unread_count'];
    
    if ($unread_count > 0) {
        $badge_color = 'red';   // Set badge color to red if there are unread messages
        $badge_text = $unread_count; // Display the number of unread messages
        $badge_class = 'badge-danger';
    }
} else {
    echo "Error fetching unread messages count: " . $conn->error;
}

// Query to get the total item count in the cart
$totalItemCount = 0; // Default total item count
// Assume you have a function or query that retrieves this count

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

$cart_badge_color = $totalItemCount > 0 ? 'red' : 'grey'; // Set cart badge color
$cart_badge_text = $totalItemCount > 0 ? $totalItemCount : '0'; // Set cart badge text
$cart_badge_class = $totalItemCount > 0 ? 'badge-danger' : ''; // Set cart badge class




?>

<!DOCTYPE html>
<html>
<head>
    <title>Asmodeus - Wallet</title>
    <meta http-equiv="Content-type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="Listings_files/flexboxgrid.min.css">
    <link rel="stylesheet" type="text/css" href="fontawesome-all.min.css">
    <link rel="stylesheet" type="text/css" href="Listings_files/style.css">
    <link rel="stylesheet" type="text/css" href="Listings_files/main.css">
    <link rel="stylesheet" type="text/css" href="Listings_files/responsive.css">
    <link rel="stylesheet" type="text/css" href="product-view.css">
    <link rel="stylesheet" type="text/css" href="sprite.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.2/css/all.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha384-...your-integrity-hash..." crossorigin="anonymous">

    <link rel="stylesheet" href="style.css">
    <!-- Add your pentagram favicon -->
    <link rel="icon" type="image/jpeg" href="img/pentagram.jpg">
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
        .listing-sorting {
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .amount {
            font-weight: bold;
            color: #555;
        }
        .detail-container {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 4px;
        }
        .container-header .sprite {
            display: inline-block;
            width: 24px;
            height: 24px;
            background: url('sprite.png') no-repeat;
            background-size: cover;
        }
    </style>
</head>
<?php
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

?>
<body>

<div class="navigation" style="margin-top:0px;">
    <div class="wrapper">
        <ul>
            <li class="nav-logo"><a href="homepage.php"><img src="Listings_files/logo_small.png" style="height: 100%;"></a></li>
            <div class="responsive-menu">
                <li class="menu-toggler"><a href="homepage.php">Navigation&nbsp; <div class="sprite sprite--caret" style="float: none; display: inline-block; margin-left:5px;"></div></a></li>
                <div class="menu-links">
                    <li class=""><a href="homepage.php">Home</a></li>
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
            <?php
$bitcoin_wallet_address = '';
$available_balance = 0;
$btc_to_usd = 0;

// Check if the user is logged in
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    // Prepare the query to fetch the bitcoin_wallet_address
    $queryBitcoin_Wallet_Address = "SELECT bitcoin_wallet_address FROM register WHERE username = ?";

    // Prepare the statement
    $stmt = $conn->prepare($queryBitcoin_Wallet_Address);
    if ($stmt) {
        $stmt->bind_param('s', $username); // 's' specifies the variable type => 'string'

        // Execute the query
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $rowBitcoin_Wallet_Address = $result->fetch_assoc();

            // Check if the wallet address is returned
            if (isset($rowBitcoin_Wallet_Address['bitcoin_wallet_address'])) {
                $bitcoin_wallet_address = htmlspecialchars($rowBitcoin_Wallet_Address['bitcoin_wallet_address'], ENT_QUOTES, 'UTF-8');

                // Fetch balance from BlockCypher API
                $api_url = "https://api.blockcypher.com/v1/btc/main/addrs/$bitcoin_wallet_address/balance";
                $response = @file_get_contents($api_url); // Suppress errors temporarily

                if ($response === FALSE) {
                    // Handle error gracefully (maybe log it or display a message)
                    echo "Error fetching data from BlockCypher API.";
                    $available_balance = 0;
                } else {
                    // Proceed with your logic to process the balance
                    $data = json_decode($response, true);
                    if (isset($data['final_balance'])) {
                        $available_balance = $data['final_balance'] / 100000000; // Convert satoshis to BTC
                    } else {
                        $available_balance = 0; // Handle missing balance data
                    }
                }
            } else {
                // No Bitcoin wallet address found
                echo "No Bitcoin wallet address found for the user.";
            }
        } else {
            // Query execution failed
            echo "Error executing query.";
        }

        // Close the statement
        $stmt->close();
    } else {
        // Statement preparation failed
        echo "Error preparing statement.";
    }
} else {
    // User not logged in
    echo "User not logged in.";
}
?>

            <li class="dropdown-link user-nav right fix-gap">
                <button class="dropbtn" style="margin-top:10px;"><?php echo "" . $_SESSION["username"] . "<br>"; ?>&nbsp; <div class="sprite sprite--caret" style="float: right; top: 1px;"></div></button>
                <div class="dropdown-content">
                    <div class="user-balance">
                        <span class="shadow-text">Balances</span><br>
                        <?php echo number_format($available_balance, 8); ?>BTC</span><br>0.23 <sup>0.00141754 XMR</sup><br>
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
            <?php    
            
            // Retrieve the user's role from the database
$user_role = 'buyer'; // Default value

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    // Debug line
    // echo "Logged in as: " . $username; 

    // Prepare and execute the query to get the user's role
    $query = "SELECT TRIM(account_role) FROM register WHERE username = ?";
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($user_role);
        
        if (!$stmt->fetch()) {
            // Debug line
            // echo "No role found for user."; 
        }
        
        $stmt->close();
    } else {
        die("Database query preparation failed: " . $conn->error);
    }
}

// Check user role and conditionally show C Panel
$showControlPanel = ($user_role === 'vendor' || $user_role === 'admin');
            
            if ($showControlPanel): ?>
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
    
<div class="container">
        <div class="wrapper">
                    




        
        

            <div class="wrapper">
            <div class="row">
                <div class="col-md-3 sidebar-navigation">
                    <ul class="box">
    <li class="title"><h2>User Control Panel</h2></li>
    <li><a href="orders.php"><div class="sprite sprite--home" style="top: 2px; margin-right: 15px;"></div>User CP Home</a></li> 
    <li><a href="orders.php?action=following"><div class="sprite sprite--star" style="top: 2px; margin-right: 15px;"></div>Favorite Merchants</a></li>
   
    <li><a href="orders.php?action=orders"><div class="sprite sprite--clipboardlist" style="top: 2px; margin-right: 15px;"></div>Orders</a></li>
    <li>
	<a href="orders.php?action=wallet" class=""><div class="sprite sprite--wallet" style="top: 2px; margin-right: 15px;"></div>Wallet</a>	
    </li>
    <li><a href="orders.php?action=exchange"><div class="sprite sprite--affiliate" style="top: 2px; margin-right: 15px;"></div>Exchange </a></li>
    <li><a href="orders.php?action=affiliate"><div class="sprite sprite--money" style="top: 2px; margin-right: 15px;"></div>Affiliate Programme</a></li>
    <li><a href="orders.php?action=editprofile"><div class="sprite sprite--card" style="top: 2px; margin-right: 15px;"></div>Edit Profile</a></li>
    <li><a href="orders.php?action=changepin"><div class="sprite sprite--qr" style="top: 2px; margin-right: 15px;"></div>Change PIN</a></li>
    <li><a href="orders.php?action=changepassword"><div class="sprite sprite--lock" style="top: 2px; margin-right: 15px;"></div>Change Password</a></li>
    <li><a href="orders.php?action=settings"><div class="sprite sprite--cog" style="top: 2px; margin-right: 15px"></div>Settings</a></li>
</ul>
                </div>
                <div class="col-md-9 sidebar-content-right">
                    <h1>Wallet</h1>
                    
                    
                    <div class="container nopadding">
                        <div class="responsive-table">
                            <table class="wallet-table" cellspacing="0" cellpadding="0">
                                <thead>
                                    <tr>
                                        <th width="10%">Coin</th>
                                        <th width="15%">Name</th>
                                        <th width="15%">Total Balance</th>
                                        <th width="15%">Available Balance</th>
                                        <th width="15%">In Order</th>
                                        <th width="10%">Value</th>
                                        <th style="min-width: 100px; text-align: center;" width="20%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                    
                                  <?php

// Fetch user data
$user = $_SESSION['username'];
$query = "SELECT total_spent_usd, bitcoin_balance, available_bitcoin_balance, total_btc, total_xmr, dateJoined, account_role FROM register WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $_SESSION["total_spent_usd"] = $row['total_spent_usd'];
    $bitcoin_balance = $row['bitcoin_balance'];
    $available_bitcoin_balance = $row['available_bitcoin_balance']; // Corrected this line
    $total_btc = $row['total_btc'];
    $total_xmr = $row['total_xmr'];
    $dateJoined = $row['dateJoined'];
    $account_role = $row['account_role'];
} else {
    $_SESSION["total_spent_usd"] = 0; // Default to 0
    $bitcoin_balance = $total_btc = $total_xmr = 0.0; // Default values
}

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


$bitcoin_wallet_address = '';
$available_balance = 0;
$bitcoin_wallet_address = '';
$available_balance = 0;

// Check if the user is logged in
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    // Prepare the query to fetch the bitcoin_wallet_address
    $queryBitcoin_Wallet_Address = "SELECT bitcoin_wallet_address FROM register WHERE username = ?";

    // Prepare the statement
    $stmt = $conn->prepare($queryBitcoin_Wallet_Address);

    if ($stmt) {
        $stmt->bind_param('s', $username); // 's' specifies the variable type => 'string'

        // Execute the query
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $rowBitcoin_Wallet_Address = $result->fetch_assoc();

            // Check if the address is set
            if (isset($rowBitcoin_Wallet_Address['bitcoin_wallet_address'])) {
                $bitcoin_wallet_address = htmlspecialchars($rowBitcoin_Wallet_Address['bitcoin_wallet_address'], ENT_QUOTES, 'UTF-8');

                // Fetch balance from Blockchain.com API (using the correct endpoint)
                $api_url = "https://blockchain.info/rawaddr/$bitcoin_wallet_address";
                
                // Use cURL for error handling and better flexibility
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $api_url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($ch);

                // Check for cURL errors
                if (curl_errno($ch)) {
                    echo "Error fetching data from Blockchain API: " . curl_error($ch);
                    $available_balance = 0;
                } else {
                    $data = json_decode($response, true);

                    // Check if the balance is present in the response
                    if (isset($data['final_balance'])) {
                        $available_balance = $data['final_balance'] / 100000000; // Convert satoshis to BTC
                    } else {
                        $available_balance = 0; // Handle missing balance data
                    }
                }
                curl_close($ch); // Close cURL

                // Generate the QR code URL
                $qr_code_url = "https://api.qrserver.com/v1/create-qr-code/?data=" . urlencode($bitcoin_wallet_address) . "&size=150x150"; // QR code URL
            } else {
                $bitcoin_wallet_address = 'No address available.';
            }
        } else {
            $bitcoin_wallet_address = 'Error fetching address.';
        }
        $stmt->close(); // Close the statement
    } else {
        $bitcoin_wallet_address = 'Error preparing statement.';
    }
} else {
    $bitcoin_wallet_address = 'User not logged in.';
}




$bitcoin_wallet_address = '';
$available_balance = 0;
$btc_to_usd = 0;

// Check if the user is logged in
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    // Prepare the query to fetch the bitcoin_wallet_address
    $queryBitcoin_Wallet_Address = "SELECT bitcoin_wallet_address FROM register WHERE username = ?";
    
    // Prepare the statement
    $stmt = $conn->prepare($queryBitcoin_Wallet_Address);
    if ($stmt) {
        $stmt->bind_param('s', $username); // 's' specifies the variable type => 'string'

        // Execute the query
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $rowBitcoin_Wallet_Address = $result->fetch_assoc();

            // Check if the address is set
            if (isset($rowBitcoin_Wallet_Address['bitcoin_wallet_address'])) {
                $bitcoin_wallet_address = htmlspecialchars($rowBitcoin_Wallet_Address['bitcoin_wallet_address'], ENT_QUOTES, 'UTF-8');

// BlockCypher API URL to fetch balance from a Bitcoin address
$api_url = "https://api.blockcypher.com/v1/btc/main/addrs/$bitcoin_wallet_address/balance";
// Fetch current BTC to USD exchange rate from CoinPaprika
$exchange_api_url = "https://api.coinpaprika.com/v1/tickers/btc-bitcoin";
$exchange_response = file_get_contents($exchange_api_url);
$exchange_data = json_decode($exchange_response, true);
$btc_to_usd = isset($exchange_data['quotes']['USD']['price']) ? $exchange_data['quotes']['USD']['price'] : 0;

// Calculate USD value
$available_balance_usd = $available_balance * $btc_to_usd;

$qr_code_url = "https://api.qrserver.com/v1/create-qr-code/?data=" . urlencode($bitcoin_wallet_address) . "&size=450x450"; // QR code URL
            } else {
                $bitcoin_wallet_address = 'No address available.';
            }
        } else {
            $bitcoin_wallet_address = 'Error fetching address.';
        }
        $stmt->close(); // Close the statement
    } else {
        $bitcoin_wallet_address = 'Error preparing statement.';
    }
} else {
    $bitcoin_wallet_address = 'User not logged in.';
}



// Initialize variable
$in_order = 0;

// Check if the user is logged in
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    // Prepare the query to fetch the in_order value
    $queryInOrder = "SELECT in_order FROM register WHERE username = ?";
    
    // Prepare the statement
    $stmt = $conn->prepare($queryInOrder);
    if ($stmt) {
        $stmt->bind_param('s', $username); // 's' specifies the variable type => 'string'

        // Execute the query
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            // Check if the in_order value is set
            if (isset($row['in_order'])) {
                $in_order = $row['in_order']; // Assign the in_order value to the variable
            } else {
                // Handle case where in_order is not set
                $in_order = 0; // or some default value
            }
        } else {
            // Handle query execution error
            echo "Error executing query.";
        }
        $stmt->close(); // Close the statement
    } else {
        // Handle statement preparation error
        echo "Error preparing statement.";
    }
} else {
    // Handle case where user is not logged in
    echo "User not logged in.";
}

// Now $in_order holds the value of the in_order column for the logged-in user
?>

<tr>
    <td>BTC</td>
    <td>Bitcoin</td>
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
$api_error_message = ''; // To store any API error messages

// Check if the user is logged in by verifying the session variable
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username']; // Retrieve the logged-in username

    // Prepare the query to fetch the bitcoin_wallet_address for the specific user
    $queryBitcoin_Wallet_Address = "SELECT bitcoin_wallet_address FROM register WHERE username = :username";
    $stmt = $pdo->prepare($queryBitcoin_Wallet_Address);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);

    // Execute the query
    if ($stmt->execute()) {
        $rowBitcoin_Wallet_Address = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch the associative array

        // Check if the address is set and not empty
        if ($rowBitcoin_Wallet_Address && isset($rowBitcoin_Wallet_Address['bitcoin_wallet_address'])) {
            // Sanitize and store the bitcoin wallet address
            $bitcoin_wallet_address = htmlspecialchars($rowBitcoin_Wallet_Address['bitcoin_wallet_address'], ENT_QUOTES, 'UTF-8');

            // Generate the QR code URL for the bitcoin wallet address
            $qr_code_url = "https://api.qrserver.com/v1/create-qr-code/?data=" . urlencode($bitcoin_wallet_address) . "&size=150x150";

            // Fetch balance from Blockchain.com API (in satoshis)
            $api_url = "https://blockchain.info/rawaddr/$bitcoin_wallet_address";
            $response = @file_get_contents($api_url); // Suppress errors temporarily

            // Check if the response is valid
            if ($response !== false) {
                $data = json_decode($response, true);

                // Check if the response contains a valid balance
                if (isset($data['final_balance'])) {
                    // Convert satoshis to BTC (divide by 100,000,000)
                    $available_balance = $data['final_balance'] / 100000000;
                } else {
                    // Handle missing balance data
                    $api_error_message = "Balance data is missing in the API response.";
                    $available_balance = 0;
                }
            } else {
                // If the API response is invalid, set balance to 0
                $api_error_message = "Error fetching data from Blockchain API.";
                $available_balance = 0;
            }
        } else {
            // No Bitcoin wallet address found for the user
            $bitcoin_wallet_address = 'No address available.';
            $available_balance = 0;
        }
    } else {
        // Query execution failed
        $api_error_message = "Error executing query to fetch Bitcoin wallet address.";
        $bitcoin_wallet_address = 'Error fetching address.';
        $available_balance = 0;
    }
} else {
    // User is not logged in
    $bitcoin_wallet_address = 'User not logged in.';
    $available_balance = 0;
    $api_error_message = "User not logged in.";
}

?>

<!-- Display information in the HTML -->

    <td><?php echo number_format($available_balance, 8); ?> BTC</td> <!-- Display bitcoin_balance -->
    <td><?php echo number_format($available_balance, 8); ?> BTC</td>
    <td><?php echo number_format($in_order, 8); ?>BTC</td>
    <td><?php echo number_format($available_balance, 8); ?> BTC</td>
    <td class="text-center">
        <div style="display: flex; gap: 5px; justify-content: center;">
            <a href="wallet-btc-deposit.php" class="btn btn-sm btn-warning" style="color: #b5bbbf;">Deposit</a>
            <a href="orders.php?action=wallet&amp;curid=4&amp;do=withdraw" class="btn btn-sm btn-green">Withdraw</a>
        </div>
    </td>
</tr>
    <!--<td class="text-center">
        <div style="display: flex; gap: 5px; justify-content: center;">
            <a href="wallet-btc-deposit.php" class="btn btn-sm btn-warning" style="color: #b5bbbf;">Deposit</a>
            <a href="orders.php?action=wallet&amp;curid=4&amp;do=withdraw" class="btn btn-sm btn-green">Withdraw</a>
        </div>
    </td>-->
</tr>

   <!-- <td>XMR</td>
    <td>Monero</td>
    <td>0.00141754</td>
    <td>0.00141754</td>
    <td>0.00000000</td>
    <td class="text-center">
        <div style="display: flex; flex-direction: column; align-items: center;">
            <a href="wallet-xmr-deposit.php" class="btn btn-sm btn-warning" style="color: #b5bbbf; margin-bottom: 5px;">Deposit</a>
            <a href="orders.php?action=wallet&amp;curid=5&amp;do=withdraw" class="btn btn-sm btn-green">Withdraw</a>
        </div>
    </td>
</tr>
<tr>
    <td>XMR</td>
    <td>Monero</td>
    <td>0.00141754</td>
    <td>0.00141754</td>
    <td>0.00000000</td>
    <td class="text-center">
        <div style="display: flex; flex-direction: column; align-items: center;">
            <a href="wallet-xmr-deposit.php" class="btn btn-sm btn-warning" style="color: #b5bbbf; margin-bottom: 5px;">Deposit</a>
            <a href="orders.php?action=wallet&amp;curid=5&amp;do=withdraw" class="btn btn-sm btn-green">Withdraw</a>
        </div>
    </td>
</tr>-->
   
                            
       
                             <div class="container">
    <div class="content profile">
		
			<div class="block">
<center>
	

<table style="width:85%"><tbody><tr><td>
<h4><i style="font-size:25px;" class="fab fa-btc"></i> Bitcoin Wallet</h4></td><td align="right">
<a href="balancexmr.php"><h6><i style="font-size:15px;" class="fab fa-monero"></i> Monero Wallet</h6></a>
</td></tr></tbody></table>
		
<hr><br>



				<table style="width:80%" border="0">

	<tbody><tr>
<td>



	<table style=" border-radius: 20px 20px 20px 20px;" border="0" bgcolor="#F2F2F2">
		
	<tbody><tr><td>
		<center>
<style>
     img{
        width:10%;
     }
    </style>
 <img width="10%;" src="temp/bc1qjkxjklhzn0rj7zyqth9cz4wcee0gqd3sj73svg.png" rel="nofollow" alt="qr code">

 


	</center></td><td>
<h6>
    
				<font style="font-size: 20px;" color="#3f345f">





                <?php
$api_token = "6fcd4a8a0a4a4bf383b8da45f1f84410"; // Your BlockCypher API token
$host = 'localhost'; // Database host
$dbname = 'market'; // Database name
$username_db = 'root'; // Database username
$password_db = 'CoheedAndCambria666!'; // Database password

// Start the session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Start session to use $_SESSION variables
}

// Function to generate a Bitcoin address using BlockCypher API
function generate_bitcoin_address($api_token) {
    $api_url = "https://api.blockcypher.com/v1/btc/main/addrs";
    
    // cURL setup
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $api_token"
    ]);

    // Execute cURL request
    $response = curl_exec($ch);

    // Check for any cURL errors
    if (curl_errno($ch)) {
        error_log('cURL Error: ' . curl_error($ch));
        curl_close($ch);
        return false;
    }

    curl_close($ch);
    
    // Decode the API response
    $response_data = json_decode($response, true);

    // Check if the address is present in the response
    if (isset($response_data['address'])) {
        return $response_data['address'];
    } else {
        error_log('Error: No address found in response. Response: ' . print_r($response_data, true));
        return false;
    }
}

// Function to insert wallet data into the database
function insert_wallet($username, $wallet_address) {
    global $host, $dbname, $username_db, $password_db;

    // Create a connection to the database
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username_db, $password_db);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepare the insert statement
        $stmt = $pdo->prepare("INSERT INTO wallets (username, wallet_address, created_at) VALUES (:username, :wallet_address, NOW())");

        // Bind the parameters
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':wallet_address', $wallet_address);

        // Execute the statement
        $stmt->execute();
        return true;
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return false;
    }
}

// Function to get or create a wallet for the user
function get_or_create_wallet($username) {
    global $host, $dbname, $username_db, $password_db;

    // Create a connection to the database
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username_db, $password_db);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Check if a wallet already exists for the user and if it is within the last 12 hours
        $stmt = $pdo->prepare("SELECT wallet_address, created_at FROM wallets WHERE username = :username ORDER BY created_at DESC LIMIT 1");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $wallet = $stmt->fetch(PDO::FETCH_ASSOC);

        // If no wallet exists or the wallet is older than 12 hours, generate a new one
        if (!$wallet || strtotime($wallet['created_at']) < time() - 43200) {  // 12 hours = 43200 seconds
            // Generate a new wallet address
            $new_wallet_address = generate_bitcoin_address($GLOBALS['api_token']);
            if ($new_wallet_address) {
                // Insert the new wallet into the database
                if (insert_wallet($username, $new_wallet_address)) {
                    return $new_wallet_address;
                } else {
                    error_log("Failed to insert wallet into the database for user $username");
                    return false;
                }
            } else {
                error_log("Failed to generate Bitcoin address for user $username");
                return false;
            }
        } else {
            // Return the existing wallet address
            return $wallet['wallet_address'];
        }
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return false;
    }
}

// Check if the user is logged in and proceed with fetching or creating a wallet
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username']; // Unique identifier for each user

    // Get or create a wallet for the user
    $wallet_address = get_or_create_wallet($username);

    // Check if the wallet address was successfully obtained
    if ($wallet_address) {
        echo "Bitcoin Address for user $username: " . $wallet_address . "<br>";
    } else {
        echo "Error generating or fetching Bitcoin address. Please try again later.";
    }
} else {
    echo "User not logged in.";
}
?>











</div>
<br>
<h6><font style="font-size: 14px;">

</font></h6>
				<table style="width:100%" border="0"><tbody><tr><td align="right">
<a href="wallet.php"><h6>Refresh Balance ↻</h6></a>
</td></tr></tbody></table>
</td></tr></tbody></table>



</td></tr></tbody></table>

 <br>





<table style="width:90%" border="0"><tbody><tr><td>




<table style="width:100%; border-radius: 20px;" bgcolor="#F2F2F2">
    <tbody>
        <tr>
            <td style="border-radius: 20px 20px 0px 0px;" colspan="3" bgcolor="#2f3947">
                <center>
                    <font color="white">
                        <i class="fas fa-coins"></i> Balance
                    </font>
                </center>
            </td>
        </tr>
        <tr>
            <td>
                <table border="0">
                    <tbody>
                        <tr>
                            <td colspan="3">
                                <center>
                                    <h6></h6>
                                    <h5><b>USD </b><font style="font-size: 40px;" color="#3f345f">
                                        <?php echo $available_balance_usd; ?>
                                    </font></b></h5>
                                    <h5><b><font color="#3f345f"><font color="#808080">
                                        <b><?php echo $bitcoin_balance; ?> BTC</b>
                                    </font></font></b></h5>
                                </center>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>

	</b></b></center></td></tr>



</tbody></table>
</td></tr></tbody></table>



</td><td>

<?php
// Database connection (replace with your actual connection details)
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'market';

try {
    $dsn = "mysql:host=$host;dbname=$database;";
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Fetch escrow data (replace 'your_table' with the actual table where the escrow data is stored)
$query = "SELECT escrow_btc, escrow_usd FROM register WHERE username = :username";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':username', $_SESSION['username'], PDO::PARAM_INT); // Assuming username is in session
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

// Retrieve BTC balance and USD from the result
$escrow_btc = $row['escrow_btc'];
$escrow_usd = $row['escrow_usd'];

// Fetch the current Bitcoin to USD exchange rate from CoinGecko (or any other API)
$btc_to_usd_api_url = 'https://api.coingecko.com/api/v3/simple/price?ids=bitcoin&vs_currencies=usd';
$btc_to_usd_response = file_get_contents($btc_to_usd_api_url);
$btc_to_usd_data = json_decode($btc_to_usd_response, true);

if (isset($btc_to_usd_data['bitcoin']['usd'])) {
    $btc_to_usd = $btc_to_usd_data['bitcoin']['usd'];
} else {
    $btc_to_usd = 0; // Default to 0 if the API request fails
}

// Convert BTC to USD if escrow_btc is available
if ($escrow_btc > 0) {
    $btc_in_usd = $escrow_btc * $btc_to_usd; // Convert BTC to USD
} else {
    $btc_in_usd = 0; // Set default value if no BTC in escrow
}

// Format values for display
$escrow_btc = number_format($escrow_btc, 8); // Format BTC to 8 decimal places
$btc_in_usd = number_format($btc_in_usd, 2); // Format USD to 2 decimal places
$escrow_usd = number_format($escrow_usd, 2); // Format USD to 2 decimal places
?>
<table style="width:100%; border-radius: 20px;" bgcolor="#F2F2F2">
    <tbody>
        <tr>
            <td style="border-radius: 20px 20px 0px 0px;" colspan="3" bgcolor="#2f3947">
                <center><font color="white">
                    <i class="fas fa-clock"></i> Escrow
                </font></center>
            </td>
        </tr>
        <tr>
            <td>
                <table border="0">
                    <tbody>
                        <tr>
                            <td colspan="3">
                                <center>
                                    <h5><b><font style="font-size: 40px;" color="#3f345f">
                                        <b>USD </b><font style="font-size: 40px;" color="#3f345f">
                                            <?php echo $escrow_usd; ?>
                                        </font></font></b></h5>
                                    <h5><b><font color="#3f345f">
                                        <b>₿</b> <?php echo $escrow_btc; ?>
                                    </font></b></h5>
                                    <h5><b><font color="#808080">
                                        <b>Escrow BTC (USD equivalent):</b> <?php echo $btc_in_usd; ?> USD
                                    </font></b></h5>
                                    <h5><b><font color="#808080">Escrow BTC:</b> <?php echo $escrow_btc; ?> BTC</font></h5>
                                    <h5><b><font color="#808080">Escrow USD:</b> <?php echo $escrow_usd; ?> USD</font></h5>
                                </center>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>
</td></tr></tbody></table>

</td></tr></tbody></table>


<br><br>
 <table style="width:90%" border="0">
 	<tbody><tr>
 		<td style="width:45%" valign="top">







<table style="width:100%; border-radius: 20px;" bgcolor="#F2F2F2">
<tbody><tr><td style=" border-radius: 20px 20px 0px 0px;" colspan="3" bgcolor="#2f3947">
	<center><font color="white"><i class="fas fa-arrow-alt-circle-down"></i> 
Deposit History - Last 5 Deposit


</font></center></td></tr>
<tr><td>
<table border="0">

	<tbody><tr><td><center>Status</center></td><td><center><h6><b>Amount</b></h6></center></td><td><center><h6><b>Ago</b></h6></center></td></tr>

	<tr><td><center>

<button style="background-color:green; color:white;" class="accept button3">&nbsp; 
 Confirmed &nbsp;</button>

		</center></td><td><center><h6><font color="green">+ ₿ 0.00114244 </font></h6></center></td><td><center><h6>2 days </h6></center></td></tr>



	<tr><td><center>

<button style="background-color:green; color:white;" class="accept button3">&nbsp; 
 Confirmed &nbsp;</button>

		</center></td><td><center><h6><font color="green">+ ₿ 0.0014653 </font></h6></center></td><td><center><h6>27 days </h6></center></td></tr>





</tbody></table>
</td></tr></tbody></table>
<hr><h6><center>Deposits appear here after 1 confirmation </center>
</h6>
</td>

<td valign="top">

<br><center>




</center></td><td colspan="2">




<table style="width:100%; border-radius: 20px;" bgcolor="#F2F2F2">
<tbody><tr><td style=" border-radius: 20px 20px 0px 0px;" bgcolor="#2f3947">
<font color="white"></font><center><font color="white"><i class="fas fa-arrow-alt-circle-up"></i> 
Withdraw
</font><h6><font color="white"></font>

</h6></center></td></tr>
	<tr><td bgcolor="#F2F2F2">
<h6>

<style> 
input[type=text] {
  width: 100%;
  padding: 4px 20px;
  margin: 8px 0;
  box-sizing: border-box;
}
</style>


	<form action="balance.php?action=withdraw" method="POST">
<center>
<div class="alert alert-info" role="alert">
	<b>Withdrawal is possible from


  
USD  60</b>
</div>
<h6>
 </h6><br><center>
<i class="fas fa-coins"></i> Amount (BTC)<br>
<input class="form-control" type="text" style="width:70%" name="6087435976434563456" value="0.000225485"> 
<br>

<a href="profile.php?action=edit#Withdrawalpop">Withdrawal Settings</a>








	</center></center></form></h6></td></tr>
<tr><td style=" border-radius: 0px 0px 20px 20px;" colspan="3" bgcolor="#F2F2F2">&nbsp;</td></tr>
</tbody></table>

</td></tr>

</tbody></table>




<br>
<table style="width:100%; border-radius: 20px;">
<tbody><tr><td style=" border-radius: 20px 20px 0px 0px;" colspan="7" bgcolor="#2f3947">
	<center><font color="white">
<i class="fas fa-arrow-alt-circle-up"></i> Withdraw History - Last 10 payouts

</font></center></td></tr>


<tr><td>
<table style="width:103%" border="0">



<tbody><tr>
	<td></td>
<td><b><font color="#4a536e"><h6><b>Status</b></h6></font></b></td>
<td style="width:40%"><b><font color="#4a536e"><center><h6><b>Wallet Address</b></h6></center></font></b></td>

<td><b><font color="#4a536e"><center><h6><b>Amount</b></h6></center></font></b></td>

<td><b><font color="#4a536e"><center><h6><b>Fees</b></h6></center></font></b></td>

<td><b><font color="#4a536e"><center><h6><b>Mix</b></h6></center></font></b></td>

<td><b><font color="#4a536e"><center><h6><b>Ago</b></h6></center></font></b></td>
</tr>
<tr><td colspan="9" bgcolor="#F2F2F2"></td></tr>
	<tr><td colspan="7"><center><h6>You have not made any withdrawals</h6></center></td></tr>
<tr><td style=" border-radius: 0px 0px 20px 20px;" colspan="9" bgcolor="#F2F2F2"></td></tr>
</tbody></table>
</td></tr>


</tbody></table>


<hr><h6>All processed history is deleted after 30 days</h6></center>



		</div>




		
  <div class="content profile">
            <div class="block">
<style type="text/css">

#showcurren,#contentcurren{display:none;}
    #showcurren:checked~#contentcurren{display:block;}
</style>

<input id="showcurren" type="checkbox">

     
<style type="text/css">

#showbible,#contentbible{display:none;}
    #showbible:checked~#contentbible{display:block;}
</style>

<input id="showbible" type="checkbox">


<style type="text/css">

#showdnlive,#contentdnlive{display:none;}
    #showdnlive:checked~#contentdnlive{display:block;}
</style>

<input id="showdnlive" type="checkbox">


<center>
<table style="width:75%" border="0"><tbody><tr><td style="width:10"></td>

<td>
 <font color="#3f345f">  <label for="showdnlive">▽ <b> Darknet News</b></label></font>

</td>

  <td>
 <font color="#3f345f">  <label for="showcurren">▽ <b>Exchange rate</b></label></font>

</td><td>
     
     
 <font color="#3f345f">  <label for="showbible">▽ <b>The Drug Users Bible</b></label></font>

</td></tr></tbody></table>
</center>

 <div id="contentdnlive">
<br>

<table style="width:80%" border="0">



<tbody><tr><td style="width:20%"></td><td><h6> <b> Counterfeit Adderall vendors  MrJohnson   NuveoDeluxe  and  AllStateRx  Busted</b></h6>2024-02-26<br>Three men were charged for participating in the distribution of counterfeit Adderall pills through m... <a target="_" href="http://darknetlidvrsli6iso7my54rjayjursyw637aypb6qambkoepmyq2yd.onion/post/counterfeit-adderall-vendors-mrjohnson-nuveodeluxe-and-allstaterx-busted-734c1f28">read more</a><br><br>
<hr>

</td></tr>




<tr><td style="width:20%"></td><td><h6> <b> California Woman Imprisoned for Distributing Drugs</b></h6>2024-02-24<br>A California woman was sentenced to 66 months in federal prison for conspiring in the distribution o... <a target="_" href="http://darknetlidvrsli6iso7my54rjayjursyw637aypb6qambkoepmyq2yd.onion/post/california-woman-imprisoned-for-distributing-drugs-cdb000b8">read more</a><br><br>
<hr>

</td></tr>




<tr><td style="width:20%"></td><td><h6> <b> Operation Cronos: LockBit Ransomware Operations Disrupted</b></h6>2024-02-22<br>International law enforcement agencies took down LockBit s operation in an operation led by the UK s... <a target="_" href="http://darknetlidvrsli6iso7my54rjayjursyw637aypb6qambkoepmyq2yd.onion/post/operation-cronos-lockbit-ransomware-operations-disrupted-895341f5">read more</a><br><br>
<hr>

</td></tr>




<tr><td style="width:20%"></td><td><h6> <b> Moderators of a Child Abuse Site Sentenced</b></h6>2024-02-20<br>Two UK men were sentenced to a combined total of over 21 years in prison for their roles in the mode... <a target="_" href="http://darknetlidvrsli6iso7my54rjayjursyw637aypb6qambkoepmyq2yd.onion/post/moderators-of-a-child-abuse-site-sentenced-6df91477">read more</a><br><br>
<hr>

</td></tr>




<tr><td style="width:20%"></td><td><h6> <b> Monopoly Market Admin Sentenced</b></h6>2024-02-16<br>A Serbian man was sentenced to 168 months in federal prison for his role in the creation and operati... <a target="_" href="http://darknetlidvrsli6iso7my54rjayjursyw637aypb6qambkoepmyq2yd.onion/post/monopoly-market-admin-sentenced-51c8ee98">read more</a><br><br>
<hr>

</td></tr>




<tr><td style="width:20%"></td><td><h6> <b> Dream Drugs Vendor  CaliCartel  Imprisoned</b></h6>2024-02-14<br>A Los Angeles man was sentenced to prison for leading a dark web drug trafficking operation that dis... <a target="_" href="http://darknetlidvrsli6iso7my54rjayjursyw637aypb6qambkoepmyq2yd.onion/post/dream-drugs-vendor-calicartel-imprisoned-79193c72">read more</a><br><br>
<hr>

</td></tr>




<tr><td style="width:20%"></td><td><h6> <b> Fentanyl Vendor  Fent4U  Sentenced to Prison</b></h6>2024-02-13<br>A Texas man was sentenced to more than 24 years in federal prison after he was convicted of multiple... <a target="_" href="http://darknetlidvrsli6iso7my54rjayjursyw637aypb6qambkoepmyq2yd.onion/post/fentanyl-vendor-fent4u-sentenced-to-prison-7106900f">read more</a><br><br>
<hr>

</td></tr>




<tr><td style="width:20%"></td><td><h6> <b> Former Navy SEAL Sentenced for Producing Child Pornography</b></h6>2024-02-11<br>A Los Angeles man was sentenced to federal prison after he was convicted of producing child sexual a... <a target="_" href="http://darknetlidvrsli6iso7my54rjayjursyw637aypb6qambkoepmyq2yd.onion/post/former-navy-seal-sentenced-for-producing-child-pornography-75a4d463">read more</a><br><br>
<hr>

</td></tr>




<tr><td style="width:20%"></td><td><h6> <b> Belarusian Man Charged for Role in BTC-e Operations</b></h6>2024-02-09<br>The US Department of Justice has charged a Belarusian and Cypriot national for his involvement in th... <a target="_" href="http://darknetlidvrsli6iso7my54rjayjursyw637aypb6qambkoepmyq2yd.onion/post/belarusian-man-charged-for-role-in-btc-e-operations-42979c44">read more</a><br><br>
<hr>

</td></tr>




<tr><td style="width:20%"></td><td><h6> <b> Empire Market Opioids Vendor  chlnsaint  Imprisoned</b></h6>2024-02-06<br>A Florida man who used the abbreviation of his name as his vendor name has been sentenced to federal... <a target="_" href="http://darknetlidvrsli6iso7my54rjayjursyw637aypb6qambkoepmyq2yd.onion/post/empire-market-opioids-vendor-chlnsaint-imprisoned-a0030833">read more</a><br><br>
<hr>

</td></tr>

</tbody></table>


 </div>


 <div id="contentbible"><br><center>
<h6>
    <b>THE DRUG USERS BIBLE</b><br><br>
We would like to draw your attention to an important book called THE DRUG USERS BIBLE, authored by Dominic Milton Trott.<br><br>

A comprehensive and essential resource for all drug users.<br>
This free PDF book provides crucial harm reduction and safety data for 182 drugs, including chemicals and botanicals.<br>
With dose thresholds, onset times, duration, and subjective experience reports, it aims to educate and protect users around the world.<br>
In an era where politicians, media, and law enforcement neglect our well-being,<br> it is our responsibility as a community to look out for one another.<br>
Additionally, the book offers information on lab testing and drug test kits available online. <br>
Stay tuned for updates on testing options and where to order or conduct lab tests. <br><br>Stay informed. Stay safe.</h6>

<br>
The Drug Users Bible can be downloaded from the cloud: <br><a target="_blank" href="https://icedrive.net/s/GuvZ8iTTSTT7BaVufvxZAWBuTACY">https://icedrive.net/s/GuvZ8iTTSTT7BaVufvxZAWBuTACY</a><br><br>

or it can be viewed on Dread: <br><a target="_blank" href="http://g66ol3eb5ujdckzqqfmjsbpdjufmjd5nsgdipvxmsh7rckzlhywlzlqd.onion/DrugUsersBible.pdf">http://g66ol3eb5ujdckzqqfmjsbpdjufmjd5nsgdipvxmsh7rckzlhywlzlqd.onion/DrugUsersBible.pdf</a><br><br>


or you can choose to download from other cloud services, which can be found on the following site: <br><a target="_blank" href="https://www.drugusersbible.com/p/toc.html">https://www.drugusersbible.com/p/toc.html</a><br><br>



For those who would appreciate printed format of this book it is possible to order it on Amazon.<br><br>

Feel free to subscribe to our subdread

<br><a target="_blank" href="http://g66ol3eb5ujdckzqqfmjsbpdjufmjd5nsgdipvxmsh7rckzlhywlzlqd.onion/d/TorZonMarket">http://g66ol3eb5ujdckzqqfmjsbpdjufmjd5nsgdipvxmsh7rckzlhywlzlqd.onion/d/TorZonMarket</a>

<br><br>

<b>TorZon Team</b>

<hr>


<br>
</center></div>


 <div id="contentcurren">
  <br>

<table border="0"><tbody><tr><td>

                <table border="0">
                    <tbody><tr>
                        <td style="width: 40%;" valign="top"><center>
<button class="bitcoinsmall button3">&nbsp;   BTC &nbsp;</button> Exchange


<table valign="top" border="0"><tbody><tr><td colspan="2">

               </td></tr>
                        

                            <tr><td style="width:30%" align="right"></td><td> <h6>1 Bitcoin = 63565.18 (USD)</h6></td></tr><tr><td align="right"></td><td><h6> 1 Bitcoin =  57043.81 (EUR)</h6></td></tr><tr><td align="right"></td><td><h6> 1 Bitcoin = 47449.48 (GBP)</h6></td></tr><tr><td align="right"></td><td><h6> 1 Bitcoin = 86006.8 (CAD)</h6></td></tr><tr><td align="right"></td><td><h6> 1 Bitcoin =  91761.93 (AUD)</h6></td></tr><tr><td align="right"></td><td><h6> 1 Bitcoin = 5352364.58 (INR)</h6></td></tr><tr><td align="right"></td><td><h6> 1 Bitcoin = 5995569.63 (RUB)</h6></td></tr>
</tbody></table>
</center></td></tr></tbody></table>


</td><td valign="top">




                <table valign="top" border="0">
                    <tbody><tr>
                        <td style="width: 40%;" valign="top">

<table valign="top" border="0"><tbody><tr><td colspan="2"><center>
                      <button class="monerosmall button3">&nbsp;   XMR &nbsp;</button> Exchange</center></td></tr>
                         


                            <tr><td style="width:30%" align="right"></td><td> <h6>1 Monero = 154.82 (USD)</h6></td></tr><tr><td align="right"></td><td><h6> 1 Monero =  138.79 (EUR)</h6></td></tr><tr><td align="right"></td><td><h6> 1 Monero = 115.46 (GBP)</h6></td></tr><tr><td align="right"></td><td><h6> 1 Monero = 209.21 (CAD)</h6></td></tr><tr><td align="right"></td><td><h6> 1 Monero =  223.21 (AUD)</h6></td></tr><tr><td align="right"></td><td><h6> 1 Monero = 14860.19 (INR)</h6></td></tr><tr><td align="right"></td><td><h6> 1 Monero = 14610.42 (RUB)</h6></td></tr>
</tbody></table>
<br>


</td></tr></tbody></table>

</td></tr></tbody></table>
</div>

                
                        <center><b><u><br>V3 Onion Mirrors</u></b><br>
                          Save the links and only use those<br><br>

                          <table border="0">
    
<tbody><tr><td style="width:25%" align="right"><h6><font color="green"><i class="fas fa-shield-alt"></i> </font> </h6></td><td><font color="black"> http://torzon4kv5swfazrziqvel2imhxcckc4otcvopiv5lnxzpqu4v4m5iyd.onion</font></td></tr>


<tr><td style="width:25%" align="right"><h6><font color="green"><i class="fas fa-shield-alt"></i> </font> </h6></td><td><font color="black">http://q46wfsee26kj6oead5hg643oi363lgqiz3m45b2dwrizefryu2zdfrqd.onion</font></td></tr>


<tr><td style="width:25%" align="right"><h6><font color="green"><i class="fas fa-shield-alt"></i> </font> </h6></td><td><font color="black">http://sglgj2fytneccvyn6n4u3pacj4zhdhscfoptnhxxes3uvljmontru2yd.onion</font></td></tr>

</tbody></table>
<a target="_blank" href="../mirrors.txt"> &gt;&gt; More mirrors &lt;&lt; </a>
                    
                    
                

            </center></div>
        </div></div></div>
            
        </div>
   
                        </div>
                    </div>
                </div>
            </div>
</div>

</body></html>
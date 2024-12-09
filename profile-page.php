<?php
session_start();

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
    die("User is not logged in.");
}

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
    echo "Error fetching unread messages count: " . $conn->error;
}

// Query to get cart items for the user
$query = "SELECT quantity FROM cart WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $safe_current_user);
$stmt->execute();
$result = $stmt->get_result();

$totalItemCount = 0; // Initialize total item count
while ($row = $result->fetch_assoc()) {
    $totalItemCount += intval($row['quantity']); // Sum the total quantity
}

// Function to get vendor info
function getVendorInfo($conn, $vendorName) {
    $query = "SELECT username, vendor_rating, total_orders, time_seen, trust_level, level FROM register WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $vendorName);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc() ?? false;
}

// Get vendor information
$vendorInfo = getVendorInfo($conn, $current_user);

// Close statement and connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="profile-page_files/flexboxgrid.min.css">
    <link rel="icon" href="img/pentagram.jpg" type="image/jpeg">
    <link rel="stylesheet" href="profile-page_files/fontawesome-all.min.css">
    <link rel="stylesheet" href="profile-page_files/style.css">
    <link rel="stylesheet" href="profile-page_files/main.css">
    <link rel="stylesheet" href="profile-page_files/responsive.css">        
    <link rel="stylesheet" href="profile-page_files/sprite.css">
    <title>Bohemia - Profile Page</title>
</head>
<body>

<div class="navigation" style="margin-top:0px;">
    <div class="wrapper">
        <ul>
            <li class="nav-logo"><a href="homepage.php"><img src="Listings_files/logo_small.png" alt="Logo" style="height: 100%;"></a></li>
            <div class="responsive-menu">
                <li class="menu-toggler"><a href="homepage.php">Navigation&nbsp;<div class="sprite sprite--caret" style="float: none; display: inline-block; margin-left:5px;"></div></a></li>
                <div class="menu-links">
                    <li class=""><a href="homepage.php">Home</a></li>
                    <li class="dropdown-link dropdown-large">
                        <a href="orders.php?action=orders" class="dropbtn">Orders</a>
                        <div class="dropdown-content right-dropdown">
                            <a href="processing.php">Processing&nbsp; <span class="badge badge-secondary right">0</span></a>
                            <a href="dispatched.php">Dispatched&nbsp; <span class="badge badge-danger right">1</span></a>
                            <a href="completed.php">Completed&nbsp; <span class="badge badge-danger right">1</span></a>
                            <a href="disputed.php">Disputed&nbsp; <span class="badge badge-secondary right">0</span></a>
                            <a href="canceled.php">Canceled</a>
                        </div>
                    </li>
                    <li><a href="listings.php">Listings</a></li>
                    <li class="dropdown-link dropdown-large">
                        <a href="messages.php" class="dropbtn">
                            Messages&nbsp;
                            <span class="badge" style="background-color: <?php echo htmlspecialchars($badge_color); ?>; color: white; padding: 0.3em 0.4em; font-size: 75%; font-weight: 700; border-radius: 0.25rem;">
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
                        <a href="bug-report.php" class="dropbtn">Support</a>
                        <div class="dropdown-content right-dropdown">
                            <a href="faq.php">F.A.Q</a>
                            <a href="support-tickets-and-bug-reports.php">Support Tickets</a>
                            <a href="bug-report.php">Report Bug</a>
                        </div>
                    </li>
                </div>
            </div>

            <li class="dropdown-link user-nav right fix-gap">
                <button class="dropbtn" style="margin-top:10px;"><?php echo htmlspecialchars($_SESSION["username"]); ?><br>&nbsp;<div class="sprite sprite--caret" style="float: right; top: 1px;"></div></button>
                <div class="dropdown-content">
                    <div class="user-balance">
                        <span class="shadow-text">Balances</span><br>
                        <span class="balance">$</span>4.73 <sup>0.00016300 BTC</sup><br>
                        <span class="balance">$</span>0.23 <sup>0.00141754 XMR</sup><br>
                    </div>
                    <a href="profile-page.php?id=60Agent">My Profile</a>
                    <a href="theme.php">Night Mode</a>
                    <a href="usercp.php">User CP</a>
                    <a href="logout.php">Logout</a>
                </div>
            </li>

            <li class="right shopping-cart-link">
                <a href="cart.php">
                    <img src="cart.png" alt="Cart" style="width: 20px; height: 25px;">
                    &nbsp;<span class="badge" style="background-color: <?php echo htmlspecialchars($badge_color); ?>; color: white; padding: 0.3em 0.4em; font-size: 75%; font-weight: 700; border-radius: 0.25rem;">
                    <?php echo htmlspecialchars($totalItemCount); ?>
                    </span>
                </a>
            </li>

            <li class="right shopping-cart-link">
                <a href="messages.php">
                    <img src="alert-bell.png" alt="Notifications" style="width: 20px; height: 25px;">
                    &nbsp;<span class="badge <?php echo ($unread_count > 0 ? 'badge-danger' : 'badge-grey'); ?>" style="background-color: <?php echo ($unread_count > 0 ? 'red' : 'grey'); ?>; color: white; padding: 0.3em 0.4em; font-size: 75%; font-weight: 700; border-radius: 0.25rem;">
                    <?php echo ($badge_text > 0 ? $badge_text : '0'); ?></span>
                </a>
            </li>
            
            <li class="right fix-gap" style="list-style:none;"><a href="become-a-merchant.php"><b>Become A Merchant</b></a></li>
        </ul>
    </div>
</div>

<div class="wrapper">
    <div class="container">
        <div class="row align-center">
            <div class="col-md-1">
                <img src="profile-page_files/image.png" alt="Profile Image">
            </div>
            <div class="col-md-9 profileHeader">
                <h1 style="display: inline-block; margin-left:5px;"><span style="color:grey;"><?php echo htmlspecialchars($_SESSION["username"]); ?><br>Status: <?php echo htmlspecialchars($_SESSION["account_role"]); ?></span></h1>
                <span class="badge badge-pill" style="display:inline-block; vertical-align: middle; margin-bottom: 8px; background-color: grey; color: white;"<button class="level-1 button3">&nbsp; Level <?= $vendorInfo['level'] ?>  &nbsp;</button></span><br>                        
                <span class="smalltext shadow-text">No feedback yet</span><br><br>
                Member Since: <?php echo $_SESSION["dateJoined"] . "<br>"; ?>                    
            </div>
            <div class="col-md-2 text-center">
                <div class="dropdown-link" style="width: 100%;">
                    <button class="dropbtn btn btn-blue btn-block">Quick Actions&nbsp;<div class="sprite sprite--caret" style="float: none; display: inline-block; margin-left:5px;"></div></button>
                    <div class="dropdown-content" style="width: 100%;">
                        <a href="compose-message.php">
                            <div class="sprite sprite--inbox" style="float: none; display: inline-block; margin-left:5px;"></div>&nbsp; Send Message
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

   <?php
//session_start(); // Ensure the session is started

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
$query = "SELECT positive_feedback_1_month, neutral_feedback_1_month, negative_feedback_1_month, 
                 positive_feedback_6_months, neutral_feedback_6_months, negative_feedback_6_months, 
                 positive_feedback_12_months, neutral_feedback_12_months, negative_feedback_12_months 
          FROM register WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    
    // Store values for use in the table
    $positive_1m = $row['positive_feedback_1_month'];
    $neutral_1m = $row['neutral_feedback_1_month'];
    $negative_1m = $row['negative_feedback_1_month'];
    
    $positive_6m = $row['positive_feedback_6_months'];
    $neutral_6m = $row['neutral_feedback_6_months'];
    $negative_6m = $row['negative_feedback_6_months'];
    
    $positive_12m = $row['positive_feedback_12_months'];
    $neutral_12m = $row['neutral_feedback_12_months'];
    $negative_12m = $row['negative_feedback_12_months'];
} else {
    // Default values if no ratings found
    $positive_1m = $neutral_1m = $negative_1m = 0;
    $positive_6m = $neutral_6m = $negative_6m = 0;
    $positive_12m = $neutral_12m = $negative_12m = 0;
}

$stmt->close();
$conn->close();
?>

<div class="row" style="margin-bottom: 1rem;">
    <div class="col-md-6" style="padding-left: 0;">
        <div class="container nopadding" style="min-height: 100%;">
            <div class="container-header" style="position: relative;">Feedback / Ratings</div>
            <table class="table" cellspacing="0" cellpadding="0">
                <tbody>
                    <tr>
                        <th></th>
                        <th class="text-center">1 Month</th>
                        <th class="text-center">6 Months</th>
                        <th class="text-center">12 Months</th>
                    </tr>    
                    <tr>
                        <td><strong>Positive</strong></td>
                        <td class="text-center"><?php echo $positive_1m; ?></td>
                        <td class="text-center"><?php echo $positive_6m; ?></td>
                        <td class="text-center"><?php echo $positive_12m; ?></td>
                    </tr>   
                    <tr>
                        <td><strong>Neutral</strong></td>
                        <td class="text-center"><?php echo $neutral_1m; ?></td>
                        <td class="text-center"><?php echo $neutral_6m; ?></td>
                        <td class="text-center"><?php echo $neutral_12m; ?></td>
                    </tr>  
                    <tr>
                        <td><strong>Negative</strong></td>
                        <td class="text-center"><?php echo $negative_1m; ?></td>
                        <td class="text-center"><?php echo $negative_6m; ?></td>
                        <td class="text-center"><?php echo $negative_12m; ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
//session_start(); // Ensure the session is started

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
$query = "SELECT total_spent_usd, bitcoin_balance, total_btc, total_xmr, dateJoined, account_role, total_orders, disputes_started FROM register WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $_SESSION["total_spent_usd"] = $row['total_spent_usd'];
    $_SESSION["total_orders"] = $row['total_orders']; // Store total orders in session
    $_SESSION["disputes_started"] = $row['disputes_started']; // Store disputes started in session
    $bitcoin_balance = $row['bitcoin_balance'];
    $total_btc = $row['total_btc'];
    $total_xmr = $row['total_xmr'];
    $dateJoined = $row['dateJoined'];
    $account_role = $row['account_role'];
} else {
    $_SESSION["total_spent_usd"] = 0; // Default to 0
    $_SESSION["total_orders"] = 0; // Default to 0
    $_SESSION["disputes_started"] = 0; // Default to 0
    $bitcoin_balance = $total_btc = $total_xmr = 0.0; // Default values
}

$stmt->close();
$conn->close();

// Function to fetch current BTC price from CoinGecko API
function getBitcoinPrice() {
    $url = 'https://api.coingecko.com/api/v3/simple/price?ids=bitcoin&vs_currencies=usd';
    $response = file_get_contents($url);
    $data = json_decode($response, true);
    return $data['bitcoin']['usd'] ?? null; // Return price or null if not available
}

// Get the current price of BTC
$market_share_price = getBitcoinPrice();

if ($market_share_price !== null) {
    // Calculate total BTC in USD
    $total_btc_usd = $bitcoin_balance * $market_share_price;
} else {
    $total_btc_usd = 0; // Default value if price couldn't be fetched
}

// Assuming you have the current price of XMR
$xmr_price = 150; // Example price; replace with dynamic value if needed
?>

<div class="col-md-6" style="padding-right: 0;">
    <div class="container nopadding" style="min-height: 100%;">
        <div class="container-header">Buyer Statistics</div>
        <table class="table" cellspacing="0" cellpadding="0">
            <tbody>
                <tr>
                    <td><strong>Total Spent</strong></td>
                    <td>$<?php echo number_format($_SESSION['total_spent_usd'], 2); ?></td> <!-- Display Total Spent -->
                </tr>
                <tr>
                    <td><strong>Total Orders</strong></td>
                    <td><?php echo $_SESSION['total_orders']; ?></td> <!-- Display Total Orders -->
                </tr>
                <tr>
                    <td><strong>Total Disputes Started</strong></td>
                    <td><?php echo $_SESSION['disputes_started']; ?></td> <!-- Display Disputes Started -->
                </tr>
            </tbody>
        </table>
    </div>
</div>

    <div class="container nopadding" style="width:100%;">
        <ul class="tabs" style="text-align:center;">
            <li><a href="profile.php?id=60Agent" class="tab-active">About</a></li>
            <li><a href="profile.php?id=60Agent&amp;action=feedbacks" class="">Feedback</a></li>
            <li><a href="profile.php?id=60Agent&amp;action=pgp" class="">PGP</a></li>
        </ul>
        <div class="container-content">User has not yet set a description about themselves.</div>
    </div>
</div>

<div id="recon-data" class="overlay">
    <div class="popup" style="overflow: auto; max-height: 90%; width: 40%;">
        <a class="close" href="#" style="background:transparent;color:black;height:65px;">×</a>
        <table class="table" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <th>Market</th>
                    <th>Feedback</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>    
    </div>
</div>

</body>
</html>

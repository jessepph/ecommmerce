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
$showControlPanel = ($user_role !== 'Buyer');

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
    <title>Asmodeus - Orders</title>
    <meta http-equiv="Content-type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="Listings_files/flexboxgrid.min.css">
    <link rel="stylesheet" type="text/css" href="Listings_files/fontawesome-all.min.css">
    <link rel="stylesheet" type="text/css" href="Listings_files/style.css">
    <link rel="stylesheet" type="text/css" href="Listings_files/main.css">
    <link rel="stylesheet" type="text/css" href="Listings_files/responsive.css">
    <link rel="stylesheet" type="text/css" href="product-view.css">
    <link rel="stylesheet" type="text/css" href="sprite.css">
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
                    <li class="active dropdown-link dropdown-large">
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
                            <a href="message-sent.php">Sent Items</a>
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
// Query to get the order count for the current user
$sql = "SELECT IFNULL(order_count, 0) AS order_count FROM cart WHERE username = '$safe_username' LIMIT 1";
$result = $conn->query($sql);

// Check if query was successful
if ($result) {
    // Check if there are any rows returned
    if ($row = $result->fetch_assoc()) {
        $order_count = $row['order_count'];
    } else {
        $order_count = 0; // No rows found, default to 0
    }
} else {
    // Handle query error
    error_log("Query error: " . $conn->error);
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
    
    <div class="wrapper">
        <div class="row">
            <div class="col-md-3 sidebar-navigation">
                <ul class="box">
    <li class="title"><h2>User Control Panel</h2></li>
    <li><a href="usercp.php"><div class="sprite sprite--home" style="top: 2px; margin-right: 15px;"></div>User CP Home</a></li> 
    <li><a href="usercp.php?action=following"><div class="sprite sprite--star" style="top: 2px; margin-right: 15px;"></div>Favorite Merchants</a></li>
   
    <li><a href="localhost/bohemia/usercp.php?action=orders" class=""><div class="active sprite sprite--clipboardlist" style="top: 2px; margin-right: 15px;"></div>Orders</a></li>
    <li>
	<a href="localhost/bohemia/usercp.php?action=wallet"><div class="sprite sprite--wallet" style="top: 2px; margin-right: 15px;"></div>Wallet</a>	
    </li>
    <li><a href="localhost/bohemia/usercp.php?action=exchange"><div class="sprite sprite--affiliate" style="top: 2px; margin-right: 15px;"></div>Exchange </a></li>
    <li><a href="localhost/bohemia/usercp.php?action=affiliate"><div class="sprite sprite--money" style="top: 2px; margin-right: 15px;"></div>Affiliate Programme</a></li>
    <li><a href="localhost/bohemia/usercp.php?action=editprofile"><div class="sprite sprite--card" style="top: 2px; margin-right: 15px;"></div>Edit Profile</a></li>
    <li><a href="localhost/bohemia/usercp.php?action=changepin"><div class="sprite sprite--qr" style="top: 2px; margin-right: 15px;"></div>Change PIN</a></li>
    <li><a href="localhost/bohemia/usercp.php?action=changepassword"><div class="sprite sprite--lock" style="top: 2px; margin-right: 15px;"></div>Change Password</a></li>
    <li><a href="localhost/bohemia/usercp.php?action=settings"><div class="sprite sprite--cog" style="top: 2px; margin-right: 15px"></div>Settings</a></li>
</ul>
            </div>
            <div class="col-md-9 sidebar-content-right">
                <form action="" method="GET" style="display: flex; min-height: 55px;">
                    <div style="flex-grow: 1;">
                        <input type="hidden" name="action" value="orders">
                        <input type="hidden" name="do" value="shipped">
                        <input type="text" name="query" class="form-control" style="width:100%;" placeholder="Search orders by username, order reference, or listing name...">
                    </div>
                    <div style="margin-left: 1em;">
                        <button type="submit" class="btn btn-blue" style="height: 43px; padding: 10px 30px;">Search</button>
                    </div>
                </form>
                <div class="container product-listing nopadding">
                    <ul class="tabs">
                        
                    <li><a href="localhost/bohemia/usercp.php?action=orders" class="tab-active">Processing&nbsp; <span class="badge badge-secondary">0</span></a></li>
                            <li><a href="dispatched.php" class="">Dispatched&nbsp; <span class="badge badge-danger">1</span></a></li>
                            <li><a href="localhost/bohemia/usercp.php?action=orders&amp;do=processed" class="">Completed&nbsp; <span class="badge badge-danger">1</span></a></li>
                            <li><a href="localhost/bohemia/usercp.php?action=orders&amp;do=disputed" class="">Disputed&nbsp; <span class="badge badge-secondary">0</span></a></li>
                            <li><a href="localhost/bohemia/usercp.php?action=orders&amp;do=canceled" class="">Canceled&nbsp; <span class="badge badge-secondary">0</span></a></li>

                                            </ul>
                   

<table class="cart-table" cellspacing="0" cellpadding="0">
    <thead>
        <tr>
                                                <th width="5%"></th>
            <th width="50%">Product</th>
            <th width="10%"></th>
            <th width="10%">Total</th>
            <th width="10%">Status</th>
            <th style="min-width: 100px; text-align: center;" width="15%"><div class="sprite sprite--cog" style="top:1px; left: 60px;"></div></th>
        </tr>
    </thead>
    <tbody>
        <tr><td colspan="6">There are no orders by this criteria.</td></tr>                            </tbody>
</table>

</form>
                                        </tr>
                                                            </tbody>
                        </table>
                        
                    </form>
                </div>
                            </div>

</body></html>
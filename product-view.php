<?php
include("myfunctions.php");
session_start();
require("db.php");

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
function getVendorInfo($conn, $vendorName) {
    $query = "SELECT username, vendor_rating, total_orders, time_seen, trust_level, level FROM register WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $vendorName);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc() ?? false;
}

// Function to retrieve average Bitcoin price in USD from multiple exchanges
function getAverageBitcoinPriceUSD() {
    $api_endpoints = [
        'https://api.coindesk.com/v1/bpi/currentprice.json',
        'https://api.blockchain.com/v3/exchange/tickers/BTC-USD',
        'https://api.coinbase.com/v2/prices/spot?currency=USD'
    ];

    $bitcoin_prices = [];

    foreach ($api_endpoints as $endpoint) {
        $response = @file_get_contents($endpoint);

        if ($response !== false) {
            $data = json_decode($response, true);
            $price = null;

            switch ($endpoint) {
                case 'https://api.coindesk.com/v1/bpi/currentprice.json':
                    $price = $data['bpi']['USD']['rate_float'] ?? null;
                    break;
                case 'https://api.blockchain.com/v3/exchange/tickers/BTC-USD':
                    $price = $data['last_trade_price'] ?? null;
                    break;
                case 'https://api.coinbase.com/v2/prices/spot?currency=USD':
                    $price = $data['data']['amount'] ?? null;
                    break;
            }

            if ($price !== null) {
                $bitcoin_prices[] = floatval($price);
            }
        }
    }

    return count($bitcoin_prices) > 0 ? array_sum($bitcoin_prices) / count($bitcoin_prices) : 0;
}

// Function to convert USD price to Bitcoin
function convertToBitcoin($usdPrice) {
    $bitcoinPriceUSD = getAverageBitcoinPriceUSD();

    return $bitcoinPriceUSD !== 0 ? $usdPrice / $bitcoinPriceUSD : 0;
}

// Function to format currency
function formatCurrency($amount) {
    return number_format($amount, 2);
}

// Check if Alertify box should be displayed
//$showAlertify = true; // You can set this dynamically if needed

// Function to generate Alertify script
//function getAlertifyScript() {
    //return <<<EOD
    //<script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
    //<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css" />
    //<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css" />
    //<script>
    //document.addEventListener('DOMContentLoaded', function() {
        //alertify.alert('Attention', 'This is a test site it is for research purposes at this time. And is not being used in a real scenario all data is fake //as is all products! Do not attempt to make changes or order without admin consent. I do not condone the use of illegal activities. And remove all //liability off me as the admin.', function(){ 
            //alertify.success('Understood'); 
        //}).set('background-color', '#ff0000').set('color', '#ffffff');
    //});
    //</script>
//EOD;
//}

// Output Alertify script if needed
//if ($showAlertify) {
    //echo getAlertifyScript();
//}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asmodeus - Products</title>
    <link rel="stylesheet" type="text/css" href="Listings_files/flexboxgrid.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Font Awesome CSS-->
    <link rel="stylesheet" type="text/css" href="Listings_files/style.css">
    <link rel="stylesheet" type="text/css" href="Listings_files/main.css">
    <link rel="stylesheet" type="text/css" href="Listings_files/responsive.css">
    <link rel="stylesheet" type="text/css" href="product-view.css">
    <link rel="stylesheet" type="text/css" href="product-view-stylesheet.css">
    <link rel="stylesheet" type="text/css" href="sprite.css">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/jpeg" href="img/pentagram.jpg">
    <script>

       
    document.addEventListener('DOMContentLoaded', function() {
        // Function to handle redirection based on screen width
        function handleRedirection() {
            if (window.innerWidth <= 390) {
                if (!localStorage.getItem('redirectedToMobile')) {
                    localStorage.setItem('redirectedToMobile', 'true');
                    window.location.href = 'product-view-mobile.php';
                }
            } else {
                if (localStorage.getItem('redirectedToMobile')) {
                    localStorage.removeItem('redirectedToMobile');
                    window.location.href = 'product-view.php';
                }
            }
        }

        // Handle redirection on page load
        handleRedirection();

        // Handle redirection on window resize
        window.addEventListener('resize', handleRedirection);
    });

</script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            height:500%;
        }
        .navigation {
            background-color: #333;
            color: #fff;
        }
        .navigation .wrapper {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
        }
        .navigation .logo img {
            height: 40px;
        }
        .navigation ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
        }
        .navigation ul li {
            margin: 0;
        }
        .navigation ul li a {
            color: #fff;
            text-decoration: none;
            padding: 15px;
            display: block;
        }
        .menu-links {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
        }
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #333;
            min-width: 160px;
            z-index: 1;
            right: 0;
        }
        .dropdown-content a {
            color: #fff;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }
        .dropdown-content a:hover {
            background-color: #575757;
        }
        .dropdown-link:hover .dropdown-content {
            display: block;
        }
        @media (max-width: 768px) {
            .navigation ul {
                flex-direction: column;
                width: 100%;
            }
            .navigation ul li {
                text-align: center;
                width: 100%;
            }
            .menu-links {
                flex-direction: column;
                width: 100%;
            }
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.innerWidth <= 390) {
                window.location.href = 'product-view-mobile.php';
            }
        });
    </script>
</head>
<body>
<?php
// Start session and include necessary files
//session_start();
// Assuming you're doing something like this on line 340


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

// Get the current user from the session
$current_user = isset($_SESSION['username']) ? $_SESSION['username'] : '';

// Sanitize user input
$safe_username = $conn->real_escape_string($current_user);

// Initialize badge-related variables
$badge_class = 'badge-grey';
$badge_text = '';
$badge_text2 = '';
$unread_count = 0;
$order_count = 0;
$total_order_count = 0;

// Query to get unread messages count for the current user
$query = "
    SELECT COUNT(*) AS unread_count
    FROM messages
    WHERE ToUser = ? AND is_read = 0
";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $current_user);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $unread_count = (int)$row['unread_count']; // Ensure it's treated as an integer
}

// Determine the background color based on unread count
$badge_class = $unread_count > 0 ? 'badge-danger' : 'badge-grey';
$badge_text2 = $unread_count > 0 ? $unread_count : '';

// Query to get the order count for the current user (single order count)
$sql = "SELECT IFNULL(order_count, 0) AS order_count FROM cart WHERE username = '$safe_username' LIMIT 1";
$result = $conn->query($sql);
if ($result) {
    $row = $result->fetch_assoc();
    if ($row) {
        $order_count = (int)$row['order_count']; // Ensure it's treated as an integer
    } else {
        $order_count = 0;
    }
} else {
    // Handle query error (if needed)
    $order_count = 0;
}

// Query to get the total order count for the user (sum of all order counts)
$sql = "SELECT SUM(order_count) AS total_order_count FROM cart WHERE username = '$safe_username'";
$result = $conn->query($sql);
if ($result) {
    $row = $result->fetch_assoc();
    if ($row) {
        $total_order_count = (int)$row['total_order_count']; // Ensure it's treated as an integer
    } else {
        $total_order_count = 0;
    }
} else {
    // Handle query error
    echo "Error fetching total order count: " . $conn->error;
}

// Close the connection
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
?>
            
        
    
    <div class="navigation">
        <div class="wrapper">
            <div class="logo">
                <a href="homepage.php"><img src="Listings_files/logo_small.png" alt="Logo"></a>
            </div>
            <ul class="menu-links">
                <li><a href="homepage.php">Home</a></li>
                <li class="dropdown-link">
                    <a href="orders.php?action=orders">Orders</a>
                    <div class="dropdown-content">
                        <a href="processing.php">Processing&nbsp; <span class="badge badge-secondary right">0</span></a>
                        <a href="dispatched.php">Dispatched&nbsp; <span class="badge badge-secondary right">0</span></a>
                        <a href="completed.php">Completed&nbsp; <span class="badge badge-danger right">1</span></a>
                        <a href="disputed.php">Disputed&nbsp; <span class="badge badge-secondary right">0</span></a>
                        <a href="canceled.php">Canceled</a>
                    </div>
                </li>
                <li><a href="listings.php">Listings</a></li>
                <li class="dropdown-link">
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
        <?php echo htmlspecialchars($badge_text2); ?>
    </span>
</a>
                    <div class="dropdown-content">
                        <a href="compose-message.php?action=compose">Compose Message</a>
                        <a href="pm_inbox.php">Inbox</a>
                        <a href="message-sent.php">Sent Items</a>
                    </div>
                </li>
                <li class="dropdown-link">
                    <a href="wallet.php?action=wallet">Wallet</a>
                    <div class="dropdown-content">
                        <a href="exchange.php?action=exchange">Exchange</a>
                    </div>
                </li>
                <li class="dropdown-link">
                    <a href="bug-report.php">Support</a>
                    <div class="dropdown-content">
                        <a href="faq.php">F.A.Q</a>
                        <a href="support-tickets-and-bug-reports.php">Support Tickets</a>
                        <a href="bug-report.php">Report Bug</a>
                    </div>
                </li>
                <?php if ($showControlPanel): ?>
                    <li class="dropdown-link">
                        <a href="control-panel.php">C Panel</a>
                        <div class="dropdown-content">
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
                <style>
                    /* Ensure the dropdown content is hidden by default */
.user-nav .dropdown-content {
    display: none;
    position: absolute;
    background-color: #333;
    min-width: 160px;
    z-index: 1;
    right: 0; /* Align to the right edge of the parent */
}

/* Show dropdown content when hovering over the parent list item */
.user-nav:hover .dropdown-content {
    display: block;
}

/* Style for dropdown links */
.user-nav .dropdown-content a {
    color: #fff;
    padding: 12px 16px;
    text-decoration: none;
    display: block;
}

/* Change background on hover for dropdown links */
.user-nav .dropdown-content a:hover {
    background-color: #575757;
}

/* Styling for the user-balance section */
.user-balance {
    padding: 10px;
    background-color: #444;
    color: #fff;
    border-bottom: 1px solid #555;
}
</style>
                </style>
                <li><a href="become-a-merchant.php"><b>Become A Merchant</b></a></li>
               <li class="user-nav dropdown-link">
    <a href="#" class="dropbtn">
        <?php echo $_SESSION["username"]; ?>&nbsp;
        <div class="sprite sprite--caret"></div>
    </a>
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
                        <?php //echo htmlspecialchars($totalItemCount); ?>
                        </span>               
                    </a>
                </li>
<li class="right shopping-cart-link">
    <a href="messages.php">
        <img src="alert-bell.png" style="width: 20px; height: 25px; display: inline-block; margin-top: 20px; float:none;">
        &nbsp;<span class="badge <?php echo $badge_class; ?>" style="padding: 0.3em 0.4em; font-size: 75%; font-weight: 700; top: 24px; line-height: 1; position: absolute; text-align: center; white-space: nowrap; vertical-align: baseline; color:white; border-radius: 0.25rem; background-color:<?php echo $unread_count > 0 ? 'red' : 'grey'; ?>;"><?php echo $badge_text2 > 0 ? $badge_text2 : '0'; ?></span>
    </a>
</li>
            </ul>
        </div>
    </div>

    
<div class="mywrapper2" style="width:100%;height:450%;">
    <div class="content profile">
      
         <div class="block">
   


<table border="0" style="margin-left:525px;width:50%;">
   <tbody><tr><td style="width:50%" valign="top">
<h6>
<?php
// Function to retrieve all products
function getAllProducts($con) {
    $query = "SELECT * FROM products";
    $result = mysqli_query($con, $query);
    return $result;
}

// Function to retrieve just the name of the product from the products table
function getProductName($con, $productName) {
    $query = "SELECT product_name FROM products WHERE product_name = '$productName'";
    $result = mysqli_query($con, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['name'];
}

// Function to retrieve product category name by ID
/*function getCategoryName($con, $categoryId) {
    $query = "SELECT name FROM categories WHERE id = '$categoryId'";
    $result = mysqli_query($con, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['name'];
}*/
function getCategoryName3($con, $category_id) {
  $query = "SELECT name FROM categories WHERE id = ?";
  $statement = $con->prepare($query);
  $statement->bind_param("i", $category_id);
  $statement->execute();
  $result = $statement->get_result();
  $category = $result->fetch_assoc();
  return $category ? $category['name'] : 'Unknown';
}

// Function to retrieve average Bitcoin price in USD from multiple exchanges
function getAverageBitcoinPriceUSD3() {
    // Array of API endpoints for Bitcoin prices from different exchanges
    $api_endpoints = [
        'https://api.coindesk.com/v1/bpi/currentprice.json',
        'https://api.blockchain.com/v3/exchange/tickers/BTC-USD',
        'https://api.coinbase.com/v2/prices/spot?currency=USD'
        // Add more API endpoints here for other exchanges if needed
    ];

    // Array to store Bitcoin prices
    $bitcoin_prices = [];

    // Fetch Bitcoin prices from each exchange API endpoint
    foreach ($api_endpoints as $endpoint) {
        $response = @file_get_contents($endpoint);

        if ($response !== false) {
            $data = json_decode($response, true);

            // Extract Bitcoin price from each API response
            $price = null;

            // Extracting price from each API response
            switch ($endpoint) {
                case 'https://api.coindesk.com/v1/bpi/currentprice.json':
                    if (isset($data['bpi']['USD']['rate_float'])) {
                        $price = $data['bpi']['USD']['rate_float'];
                    }
                    break;
                case 'https://api.blockchain.com/v3/exchange/tickers/BTC-USD':
                    if (isset($data['last_trade_price'])) {
                        $price = $data['last_trade_price'];
                    }
                    break;
                case 'https://api.coinbase.com/v2/prices/spot?currency=USD':
                    if (isset($data['data']['amount'])) {
                        $price = $data['data']['amount'];
                    }
                    break;
                // Add cases for other exchange endpoints here if needed
            }

            // Add the price to the array if it's valid
            if ($price !== null) {
                $bitcoin_prices[] = floatval($price);
            }
        }
    }

    // Calculate the average Bitcoin price
    $average_price = count($bitcoin_prices) > 0 ? array_sum($bitcoin_prices) / count($bitcoin_prices) : 0;

    return $average_price; // Return the average Bitcoin price
}

// Function to convert USD price to Bitcoin
function convertToBitcoin3($usdPrice) {
    // Get the average Bitcoin price in USD
    $bitcoinPriceUSD = getAverageBitcoinPriceUSD();

    // Avoid division by zero errors
    if ($bitcoinPriceUSD === 0) {
        return 0; // Return zero if Bitcoin price in USD is not available or zero
    }

    // Convert USD price to Bitcoin using the average Bitcoin price
    return $usdPrice / $bitcoinPriceUSD;
}

// Set the number of products per page
$products_per_page = 10;

// Determine the current page
$current_page = isset($_GET['page']) ? max(1, min($_GET['page'], $total_pages)) : 1;

/// Database connection parameters
$host = "localhost";
$username = "root";
$password = "";
$database = "market";
//$port = 888;

// Create a new database connection
$con = mysqli_connect($host, $username, $password, $database);

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit();
}

// Define trust level variable
$queryTrustLevel = "SELECT trust_level FROM register";
$resultTrustLevel = mysqli_query($con, $queryTrustLevel);
$rowTrustLevel = mysqli_fetch_assoc($resultTrustLevel);
$trustLevel = $rowTrustLevel['trust_level'];
$currentDateTime = date("Y-m-d H:i:s");
//$products = getAll("products");
$queryVendorNameRegister = "SELECT username FROM register";
$resultVendorNameRegister = mysqli_query($con, $queryVendorNameRegister);
$rowVendorNameRegister = mysqli_fetch_array($resultVendorNameRegister);
$queryVendorNameRegister = "SELECT time_seen FROM register";
$resultVendorNameRegister = mysqli_query($con, $queryVendorNameRegister);
$rowVendorNameRegister = mysqli_fetch_array($resultVendorNameRegister);
$query3 = "SELECT name FROM categories";
$result3 = mysqli_query($con, $query3);
$row3 = mysqli_fetch_array($result3);
$queryCatShoes = "SELECT * FROM categories WHERE id='10'";
//$queryCatNameShoes = "SELECT * FROM categories WHERE name='shoes'";
//$resultCatNameShoes = mysqli_query($con,$queryCatNameShoes);
//$rowCatNameShoes = mysqli_fetch_array($resultCatNameShoes);
$resultCatShoes = mysqli_query($con, $queryCatShoes);
$rowCatShoes = mysqli_fetch_array($resultCatShoes);
$queryVendorName = "SELECT vendor_name FROM products";
$resultVendorName = mysqli_query($con, $queryVendorName);
$rowVendorName = mysqli_fetch_array($resultVendorName);
$queryTotalOrders = "SELECT total_orders FROM register";
$resultTotalOrders = mysqli_query($con, $queryTotalOrders);
$rowTotalOrders = mysqli_fetch_array($resultTotalOrders);
$queryShipsTo = "SELECT ships_to FROM products";
$resultShipsTo = mysqli_query($con, $queryShipsTo);
$rowShipsTo = mysqli_fetch_array($resultShipsTo);
$queryShipsFrom = "SELECT ships_from FROM products";
$resultShipsFrom = mysqli_query($con, $queryShipsFrom);
$rowShipsFrom = mysqli_fetch_array($resultShipsFrom);
$queryVendorRating = "SELECT vendor_rating FROM products";
$resultVendorRating = mysqli_query($con, $queryVendorRating);
$rowVendorRating = mysqli_fetch_array($resultVendorRating);
$timesSold = "SELECT times_sold_last_48_hr FROM products";
$queryTimesSoldResult = (mysqli_query($con, $timesSold));
$rowTimesSold = mysqli_fetch_array($queryTimesSoldResult);
$available = "SELECT available FROM products";
$queryAvailable = mysqli_query($con, $available);
$rowAvailable = mysqli_fetch_array($queryAvailable);
$total_items_query = "SELECT total_sold FROM products";
$result_total_items = mysqli_query($con, $total_items_query);
$total_items_row = mysqli_fetch_array($result_total_items);
$queryVendorTrustLevel = "SELECT trust_level FROM register";
$resultTrustLevel = mysqli_query($con, $queryVendorTrustLevel);
$rowTrustLevel = mysqli_fetch_array($resultTrustLevel);
$queryShippingMethod = "SELECT shipping_method FROM products";
$resultShippingMethod = mysqli_query($con, $queryShippingMethod);
$rowShippingMethod = mysqli_fetch_array($resultShippingMethod);
$queryShippingPrice = "SELECT shipping_price FROM products";
$resultShippingPrice = mysqli_query($con, $queryShippingPrice);
$rowShippingPrice = mysqli_fetch_array($resultShippingPrice);
$querySellingPrice = "SELECT selling_price FROM products";
$resultSellingPrice = mysqli_query($con, $querySellingPrice);
$rowSellingPrice = mysqli_fetch_array($resultSellingPrice);
$Total_Price = $rowShippingPrice + $rowSellingPrice;

// Fetch product by name
$productName = isset($_GET['name']) ? $_GET['name'] : '';
$productName = mysqli_real_escape_string($con, $productName);
$query = "SELECT * FROM products WHERE product_name LIKE '%$productName%'";
$result = mysqli_query($con, $query);

// Function to retrieve vendor information by vendor ID
function getVendorInfo3($con, $vendorId) {
    $query = "SELECT username, vendor_rating, total_orders, time_seen FROM register WHERE username = '$vendorId'";
    $result = mysqli_query($con, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    return false; // Return false if vendor information is not found
}

   
// Define vendor level variable
$queryLevel = "SELECT level FROM register";
$resultLevel = mysqli_query($con, $queryLevel);
$rowLevel = mysqli_fetch_assoc($resultLevel);
$Level = $rowLevel['level'];

// Determine the appropriate button class based on the vendor level
switch ($Level) {
    case 1:
        $buttonClass = "level1";
        break;
    case 2:
        $buttonClass = "level2";
        break;
    case 3:
        $buttonClass = "level3";
        break;
    case 4:
        $buttonClass = "level4";
        break;
    case 5:
        $buttonClass = "level5";
        break;
    case 6:
        $buttonClass = "level6";
        break;
    case 7:
        $buttonClass = "level7";
        break;
    case 8:
        $buttonClass = "level8";
        break;
    case 9:
        $buttonClass = "level9";
        break;
    case 10:
        $buttonClass = "level10";
        break;
    default:
        $buttonClass = "level1"; // Default to level 1 if value is out of range
}



// Determine the appropriate button class based on the trust level
switch ($trustLevel) {
    case 1:
        $buttonClass = "level1";
        break;
    case 2:
        $buttonClass = "level2";
        break;
    case 3:
        $buttonClass = "level3";
        break;
    case 4:
        $buttonClass = "level4";
        break;
    case 5:
        $buttonClass = "level5";
        break;
    case 6:
        $buttonClass = "level6";
        break;
    case 7:
        $buttonClass = "level7";
        break;
    case 8:
        $buttonClass = "level8";
        break;
    case 9:
        $buttonClass = "level9";
        break;
    case 10:
        $buttonClass = "level10";
        break;
    default:
        $buttonClass = "level1"; // Default to level 1 if value is out of range

    }


// Fetch product by name
$productName = isset($_GET['name']) ? $_GET['name'] : '';
$productName = mysqli_real_escape_string($con, $productName);
$query = "SELECT * FROM products WHERE product_name LIKE '%$productName%'";
$result = mysqli_query($con, $query);


// Function to retrieve vendor information by vendor name (from products table)
function getVendorInfoFromProducts($con, $vendorName) {
    $query = "SELECT vendor_name, vendor_rating FROM products WHERE vendor_name = '$vendorName'";
    $result = mysqli_query($con, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    return false; // Return false if vendor information is not found
}

// Fetch product by name
$productName = isset($_GET['name']) ? $_GET['name'] : '';
$productName = mysqli_real_escape_string($con, $productName);
$query = "SELECT * FROM products WHERE product_name LIKE '%$productName%'";
$result = mysqli_query($con, $query);

// Check if products are found
if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);

    // Fetch vendor information from products table
    $vendorInfo = getVendorInfoFromProducts($con, $row['vendor_name']);

    // Function to retrieve vendor information by vendor ID
function getVendorInfo2($con, $vendorId) {
    $query = "SELECT username, vendor_rating, total_orders, time_seen, trust_level, level FROM register WHERE username = '$vendorId'";
    $result = mysqli_query($con, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    return false; // Return false if vendor information is not found
}


// Fetch product by name
$productName = isset($_GET['name']) ? $_GET['name'] : '';
$productName = mysqli_real_escape_string($con, $productName);
$query = "SELECT * FROM products WHERE product_name LIKE '%$productName%'";
$result = mysqli_query($con, $query);

// Check if products are found
if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);

    // Fetch vendor information
    $vendorInfo = getVendorInfo2($con, $row['vendor_name'])


    
        ?>
        
    <br>
    <hr>
    <center>
        <table style="width: 100%" border="0">
            <tbody>
                <tr>
                    <td style="width: 50%" valign="top" align="">
                        <table style="width: 100%" border="0">
                            <tbody>
                                <tr>
                                    <td style="border-radius: 20px 20px 0px 0px; background-color: #2f3947;">
                                        <center>
                                            <h6><font style="font-size:20px; color: white;">Vendor Information</font></h6>
                                        </center>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="border-radius: 0px 0px 20px 20px; background-color: #F2F2F2;">
                                        <br>
                                        <center>
                                            <img class="round" style="box-shadow: 2.5px 2.5px 5px #FAFAFA; height:80px;width:80px;" src="images/vendor-profile-pic.png" width="80" height="80"><br>
                                            <h6>
                                                <a href="userprofile.php?id=<?= $row['vendor_name']; ?>&rid=9331417512">
                                                    <font style="font-size:146%;"><?= htmlspecialchars($vendorInfo['username']); ?> (<?= $vendorInfo['total_orders']; ?>) (<?= $vendorInfo['vendor_rating']; ?> <font color="orange">★</font>) </font>
                                                </a>
                                            </h6>
                                            <h6> <font color="3f345f" style="font-size:180%">Last seen: <?= date("Y-m-d H:i:s", strtotime($vendorInfo['time_seen'])); ?> </font>
                                                <table style="margin-left:0px;">
                                                    <tr>
                                                        <td align="right">
                                                            <div class="my-wrapper" style="margin-left:30px;">
                                                                <button class="level-1 button3">&nbsp; Level <?= $vendorInfo['level'] ?>  &nbsp;</button>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <button class="level-6 button3">&nbsp; Trust Level <?= $vendorInfo['trust_level']; ?> &nbsp;</button>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </h6>
                                        </center>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
    </center>
        <?php
    } else {
        echo "Vendor information not found for vendor name: " . htmlspecialchars($row['vendor_name']);
    }
} else {
    echo "No product found with the provided name.";
}

// Close the connection
mysqli_close($con);
?>
<table style="width:100%" border="0">
    <tbody>
        
    </tbody>
</table>
<style>
.popup {
  display: none;
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background-color: #fff;
  z-index: 1;
  padding: 1em;
  border-radius: 5px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
}

#popup-trigger:checked + .popup {
  display: block;
}

.popup label {
  position: absolute;
  bottom: 0;
  right: 0;
  font-size: 1em;
  padding: 0.2em 0.4em;
  cursor: pointer;
}

.popup img {
  max-width: 100%;
  max-height: 100%;
}


  </style>
<?php
// Start the session
//session_start();

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
        die("Database query preparation failed: " . $conn->error);
    }
}

// Check user role and conditionally show Control Panel
$showControlPanel = ($user_role !== 'Buyer');

// Get the current product's name and ID from the URL
$productName = isset($_GET['product_name']) ? $_GET['product_name'] : '';
$productId = isset($_GET['id']) ? $_GET['id'] : 0;

// Function to retrieve product by name and ID
function getProductByNameAndId($conn, $productName, $productId) {
    $query = "SELECT * FROM products WHERE product_name = ? AND id = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Database prepare failed: " . $conn->error);
    }
    $stmt->bind_param("si", $productName, $productId); // "si" for string and integer
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();
    return $product;
}

// Function to retrieve category name by category ID
/*function getCategoryName($conn, $categoryId) {
    $query = "SELECT name FROM categories WHERE id = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Category query failed: " . $conn->error);
    }
    $stmt->bind_param("i", $categoryId); // "i" for integer
    $stmt->execute();
    $result = $stmt->get_result();
    $category = $result->fetch_assoc();
    $stmt->close();
    return $category ? $category['name'] : 'Unknown';
}*/

// Check if both product name and ID are provided
if (!empty($productName) && !empty($productId)) {
    // Get the product details by name and ID
    $product = getProductByNameAndId($conn, $productName, $productId);

    if ($product) {
        // Get the category name
        $categoryName = getCategoryName($conn, $product['category_id']);

        // Display product details HTML
        ?>
        <div class="my-new-container">
            <table border="0" style="width: 100%; margin-left:0px;">
                <tbody>
                    <tr>
                        <td style="border-radius: 20px 20px 0px 0px; background-color: #2f3947;">
                            <h6 style="font-size: 20px; color: white; text-align: center; padding: 10px 0;">Product Information</h6>
                        </td>
                    </tr>
                    <tr>
                        <td style="border-radius: 0px 0px 20px 20px; background-color: #F2F2F2;">
                            <table style="width: 100%;" border="0">
                                <tbody>
                                    <tr>
                                        <td colspan="2" style="padding: 10px;">
                                            <h2 style="text-align:center;"><?= htmlspecialchars($product['product_name']); ?></h2>
                                            <img style="max-width: 100%; width:50%; margin-left:200px; height:50%;" src="uploads/<?= $product['image'] ?? 'default.jpg'; ?>" alt="<?= htmlspecialchars($product['product_name']); ?>">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 50%; text-align: right;"><b>Price:</b></td>
                                        <td>USD <?= number_format($product['selling_price'] ?? '0.00', 2); ?></td>
                                    </tr>
                                    <tr>
                                        <td style="width: 50%; text-align: right;"><b>Sold By:</b></td>
                                        <td><?= htmlspecialchars($product['vendor_name']); ?></td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: right;"><b>Category:</b></td>
                                        <td><?= htmlspecialchars($categoryName); ?></td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: right;"><b>Shipping:</b></td>
                                        <td><?= htmlspecialchars($product['ships_from'] ?? 'Unknown'); ?> → <?= htmlspecialchars($product['ships_to'] ?? 'Unknown'); ?> </td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: right;"><b>Payment Method:</b></td>
                                        <td><button style="width:80px;" class="escrow2 button3">Escrow</button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php
    } else {
        // If no product found with the provided name and ID
        echo "No matching product found.";
    }
} else {
    // Product name or ID not provided in the URL
    echo "Product name or ID not provided.";
}

// Close the database connection
$conn->close();
?>
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

// Get the current product's name from the URL
$currentProductName = isset($_GET['name']) ? $_GET['name'] : '';

// Function to get the current product details
/*function getCurrentProductDetails6($conn, $productName) {
    $query = "SELECT vendor_name, category_id, product_name FROM products WHERE product_name = ?";
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("s", $productName);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();
    return $product;
}*/

// Function to get category name
/*function getCategoryName8($conn, $categoryId) {
    $query = "SELECT name FROM categories WHERE id = ?";
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("i", $categoryId);
    $stmt->execute();
    $result = $stmt->get_result();
    $category = $result->fetch_assoc();
    $stmt->close();
    return $category ? $category['name'] : 'Unknown';
}*/

// Function to get products by vendor and category
/*function getVendorProducts8($conn, $vendorName, $categoryId) {
    $query = "SELECT id, name, slug FROM products WHERE vendor_name = ? AND category_id = ?";
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("si", $vendorName, $categoryId);
    $stmt->execute();
    $result = $stmt->get_result();
    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    $stmt->close();
    return $products;
}*/
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

// Get the current product's name and ID from the URL
$currentProductName = isset($_GET['name']) ? $_GET['name'] : '';
$currentProductId = isset($_GET['id']) ? $_GET['id'] : '';

// Function to get the current product details by name
/*function getCurrentProductDetails6($conn, $productName, $productId) {
    $query = "SELECT vendor_name, category_id, name FROM products WHERE name = ? AND id = ?";
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("si", $productName, $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();
    return $product;
}*/

 //Function to get category name by category_id
 /*function getCategoryName6($conn, $categoryId) {
    $query = "SELECT name FROM categories WHERE id = ?";
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("i", $categoryId);
    $stmt->execute();
    $result = $stmt->get_result();
    $category = $result->fetch_assoc();
    $stmt->close();
    return $category ? $category['name'] : 'Unknown';
}*/

// Function to get products by vendor and category
/*function getVendorProducts6($conn, $vendorName, $categoryId) {
    $query = "SELECT id, name, slug FROM products WHERE vendor_name = ? AND category_id = ?";
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("si", $vendorName, $categoryId);
    $stmt->execute();
    $result = $stmt->get_result();
    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    $stmt->close();
    return $products;
}*/
// Function to retrieve product details by name
/*function getProductByName2($con, $productName) {
    $query = "SELECT product_name FROM products WHERE product_name = ?";
    $statement = $con->prepare($query);
    $statement->bind_param("s", $productName);
    $statement->execute();
    $result = $statement->get_result();
    $product = $result->fetch_assoc();
    $statement->close(); // Close the prepared statement
    return $product;
}*/





// Function to retrieve product by name
function getProductByName($conn, $productName) {
    $query = "SELECT product_name FROM products WHERE product_name = ?";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        die("Database prepare failed: " . $conn->error);
    }

    $stmt->bind_param("s", $productName); // "s" for string
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Fetch the result as an associative array
    $product = $result->fetch_assoc();
    $stmt->close();

    return $product;
}

// Function to retrieve category name by category ID
function getCategoryName($conn, $categoryId) {
    $query = "SELECT name FROM categories WHERE id = ?";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        die("Database prepare failed: " . $conn->error);
    }

    $stmt->bind_param("i", $categoryId); // "i" for integer
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Fetch the result as an associative array
    $category = $result->fetch_assoc();
    $stmt->close();

    return $category ? $category['name'] : 'Unknown';
}
?>






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

// Get the current product's name from the URL
$currentProductName = isset($_GET['name']) ? $_GET['name'] : '';

// Function to get the current product details
/*function getCurrentProductDetails6($conn, $productName) {
    $query = "SELECT vendor_name, category_id, name FROM products WHERE name = ?";
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("s", $productName);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();
    return $product;
}*/

// Function to get category name
/*function getCategoryName6($conn, $categoryId) {
    $query = "SELECT name FROM categories WHERE id = ?";
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("i", $categoryId);
    $stmt->execute();
    $result = $stmt->get_result();
    $category = $result->fetch_assoc();
    $stmt->close();
    return $category ? $category['name'] : 'Unknown';
}*/

// Function to get products by vendor and category
/*function getVendorProducts6($conn, $vendorName, $categoryId) {
    $query = "SELECT id, name, slug FROM products WHERE vendor_name = ? AND category_id = ?";
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("si", $vendorName, $categoryId);
    $stmt->execute();
    $result = $stmt->get_result();
    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    $stmt->close();
    return $products;
}*/
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

// Get the current product's name and ID from the URL
$currentProductName = isset($_GET['name']) ? $_GET['name'] : '';
$currentProductId = isset($_GET['id']) ? $_GET['id'] : '';

// Function to get the current product details by name
function getCurrentProductDetails6($conn, $productName, $productId) {
    $query = "SELECT vendor_name, category_id, product_name FROM products WHERE name = ? AND id = ?";
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("si", $productName, $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();
    return $product;
}



// Ensure the product details are fetched correctly
$productName = isset($_GET['product_name']) ? $_GET['product_name'] : '';
$productId = isset($_GET['id']) ? $_GET['id'] : 0;

// Function to retrieve product by name and ID
/*function getProductByNameAndId($conn, $productName, $productId) {
    $query = "SELECT * FROM products WHERE product_name = ? AND id = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Database prepare failed: " . $conn->error);
    }
    $stmt->bind_param("si", $productName, $productId); // "si" for string and integer
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();
    return $product;
}*/

// Function to retrieve category name by category ID
function getCategoryNames($conn, $categoryId) {
    $query = "SELECT name FROM categories WHERE id = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Category query failed: " . $conn->error);
    }
    $stmt->bind_param("i", $categoryId); // "i" for integer
    $stmt->execute();
    $result = $stmt->get_result();
    $category = $result->fetch_assoc();
    $stmt->close();
    return $category ? $category['name'] : 'Unknown';
}

// Fetch the product details by name and ID
$currentProduct = getProductByNameAndId($conn, $productName, $productId);

// Check if the product exists
if ($currentProduct) {
    $categoryId = $currentProduct['category_id'];
    $vendorName = $currentProduct['vendor_name'];
    $currentProductName = $currentProduct['product_name'];

    // Get the category name
    $categoryName = getCategoryName($conn, $categoryId);

    // Display the heading with category and vendor details
    echo '<table style="width:100%; margin-top:20px;font-size:20px;text-align:center;">
            <tbody>
                <tr>
                    <td style="border-radius: 20px 20px 0px 0px;" bgcolor="#2f3947">
                        <h6><font style="font-size:115%;" color="white">
                        <center>
                            <font style="font-size:115%;" color="white">
                            All ' . htmlspecialchars($categoryName) . ' products from ' . htmlspecialchars($vendorName) . '
                            <br>
                            </font>
                        </center>
                        </font></h6>
                    </td>
                </tr>
            </tbody>
        </table>';

    // Fetch products from the same vendor and category
    if ($categoryId >= 1 && $vendorName) {
        $query = "SELECT id, product_name, slug FROM products WHERE vendor_name = ? AND category_id = ?";
        $stmt = $conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param("si", $vendorName, $categoryId);
            $stmt->execute();
            $result = $stmt->get_result();
            $products = [];
            while ($product = $result->fetch_assoc()) {
                $products[] = $product;
            }
            $stmt->close();
        } else {
            die("Failed to fetch vendor products: " . $conn->error);
        }

        // Display products
        if (!empty($products)) {
            echo '<table style="width:100%; margin-top:20px;font-size:20px;text-align:center;"><tbody>';
            foreach ($products as $product) {
                $productId = htmlspecialchars($product['id']);
                $currentProductName= htmlspecialchars($product['product_name']);  // Correct field name
                $productSlug = htmlspecialchars($product['slug']);
                echo '<tr><td style="width:100%; border-radius: 0px 0px 20px 20px;" bgcolor="#FFF"><br>';
                // Product link with proper URL encoding
                echo '<h6><a href="product-view.php?product_name=' . urlencode($productName) . '&id=' . urlencode($productId) . '" class="product-link">' . htmlspecialchars($productName) . '</a></h6>';
                echo '</td></tr>';
            }
            echo '</tbody></table>';
        } else {
            echo 'There are no products found.';
        }
    } else {
        echo 'Invalid vendor or category.';
    }
} else {
    echo 'Product not found.';
}


?>




        <br>
       
        <font color="#3f345f"></font>
        <center style="background-color:white; width:100%; height:100%;">
            

</tbody>
</table>
</center>

<div class="outer-wrapper-menu-container" style="width: 50%; margin-left: 505px;">
<div class="menu-container">
  <div class="menu-box" style="width: 100%; margin-left: 10px; margin-top: 37px; background-color: white;">
    <a class="menu-link" href="#description">Product Description</a>
    <a class="menu-link" href="#terms">Terms and Refund Policy </a>
    <a class="menu-link" href="#feedback">Feedbacks (12)</a>
  
  </div>



  </div>




<style>
.menu-container {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100%;
}

.menu-box {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  align-items: center;
  background-color: #f1f1f1;
  border: 1px solid #ddd;
  border-radius: 10px;
  padding: 10px;
  width: 100%;
  margin-bottom: 20px;
}

.menu-link {
  text-decoration: none;
  color: #000;
  font-weight: bold;
  font-size: 14px;
  transition: all 0.3s ease;
  padding: 10px;
  margin: 5px;
  border: 1px solid #ccc;
  border-radius: 5px;
}

.menu-link:hover {
  background-color: #c0392b;
  color: #fff;
}

.menu-link.active {
  background-color: #c0392b;
  color: #fff;
}

.menu-content-wrapper {
  width: 100%;
  display: flex;
  justify-content: center;
}

.menu-content {
  display: none;
  width: 100%;
  text-align: center;
  margin-top: 20px;
}

.menu-content:target {
  display: block;
}


</style>
     
<?php
//Define your database connection details
$host = 'localhost';  // Database host IP or domain
               // Database port
$username = 'root';        // MySQL username
$password = ''; // MySQL password
$database = 'market';      // Database name

// Create a new mysqli connection
$con = mysqli_connect($host, $username, $password, $database);

// Check connection
if (!$con) {
    // If there is a connection error, display the error message
    echo '<p style="text-align:center; color:red;">You have failed to connect to the database. Error: ' . mysqli_connect_error() . '</p>';
} else {
    // Connection successful
    // Uncomment the line below if you want to confirm successful connection
    // echo '<p style="text-align:center; color:green;">You have connected to the database successfully.</p>';
}

//test variables for terms_refund_policy
$queryTermsRefundPolicy= "SELECT terms_refund_policy FROM products";
$resultTermsRefundPolicy= mysqli_query($con, $queryTermsRefundPolicy);
$rowTermsRefundPolicy= mysqli_fetch_array($resultTermsRefundPolicy);

// Retrieve the product name from the URL parameter 'name'
$productName = $_GET['name'] ?? '';

// Decode URL parameter to handle spaces and special characters
$productName = urldecode($productName);

// Check if the product name is provided and not empty
if (!empty($productName)) {
    // Prepare the SQL statement to fetch product_description based on product name
    $sql = "SELECT product_description FROM products WHERE product_name = ?";

    // Prepare and bind parameter
    $stmt = $con->prepare($sql);
    $stmt->bind_param("s", $productName);

    // Execute the statement
    $stmt->execute();

    // Bind result variables
    $stmt->bind_result($rowProductDescription);

    // Fetch result
    $stmt->fetch();

    // Check if terms_refund_policy is fetched and not empty
    if (!empty($rowProductDescription)) {
        // HTML structure with PHP embedding
        echo '
        <div class="shipping-container-description>
        <div class="menu-content" id="description" style=" width: 50%; position: absolute; margin-top: 10px;
}">
    <table style="width:100%; font-size:20px; border-radius: 20px; margin-top:0px;" bgcolor="#F2F2F2">
        <tbody>
            <tr>
                <td style="border-radius: 20px 20px 0px 0px;" bgcolor="#2f3947" style="margin-left:100px;padding:50px;background-color:white;margin-top:0px">
                    <font color="white">
                        <center><font style="font-size:25px;" color="white">Product Description</font></center>
                    </font>
                </td>
            </tr>
            <tr>
                <td style="text-align:center; font-size:25px; margin-left:-400px;">
                    <textarea rows="10" cols="80" style="background-color:#FFF; resize:none; padding:50px; width:100%;text-align:center;margin-">
                       '.htmlspecialchars($rowProductDescription).'
                    </textarea>
                </td>
            </tr>
            </div>
            </div>
            
        </tbody>
    </table>
';
    } else {
        echo "Small description is empty or not available for this product.";
    }

    // Close statement
    $stmt->close();
} else {
    echo "Product name not provided.";
}

// Close connection
$con->close();
?>





<style>
.menu-container {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100%;
}

.menu-box {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  align-items: center;
  background-color: #f1f1f1;
  border: 1px solid #ddd;
  border-radius: 10px;
  padding: 10px;
  width: 100%;
  margin-bottom: 20px;
}

.menu-link {
  text-decoration: none;
  color: #000;
  font-weight: bold;
  font-size: 14px;
  transition: all 0.3s ease;
  padding: 10px;
  margin: 5px;
  border: 1px solid #ccc;
  border-radius: 5px;
}

.menu-link:hover {
  background-color: #c0392b;
  color: #fff;
}

.menu-link.active {
  background-color: #c0392b;
  color: #fff;
}

.menu-content-wrapper {
  width: 100%;
  display: flex;
  justify-content: center;
}

.menu-content {
  display: none;
  width: 100%;
  text-align: center;
  margin-top: 20px;
}

.menu-content:target {
  display: block;
}


</style>
     
<?php
//Define your database connection details
$host = 'localhost';  // Database host IP or domain
//$port = 888;               // Database port
$username = 'root';        // MySQL username
$password = ''; // MySQL password
$database = 'market';      // Database name

// Create a new mysqli connection
$con = mysqli_connect($host, $username, $password, $database);

// Check connection
if (!$con) {
    // If there is a connection error, display the error message
    echo '<p style="text-align:center; color:red;">You have failed to connect to the database. Error: ' . mysqli_connect_error() . '</p>';
} else {
    // Connection successful
    // Uncomment the line below if you want to confirm successful connection
    // echo '<p style="text-align:center; color:green;">You have connected to the database successfully.</p>';
}

?>

<?php

// Ensure you have a valid product ID
$productId = $_GET['id'] ?? 0; // Get product ID from the URL or default to 0 if not set

// Check if the product ID is valid
if ($productId > 0) {
    // Query to fetch product details including shipping price and method
    $query = "SELECT product_name, selling_price, shipping_price, shipping_method 
              FROM products 
              WHERE id = ?";
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Database prepare failed: " . $conn->error);
    }
    $stmt->bind_param("i", $productId); // Bind the product ID as integer
    $stmt->execute();
    $stmt->bind_result($productName, $sellingPrice, $shippingPrice, $shippingMethod);
    $stmt->fetch(); // Fetch the result
    $stmt->close();
    
    // Check if product data is available
    if (!$productName) {
        die("Product not found.");
    }
} else {
    die("Invalid product ID.");
}

?>

<div class="container" style="margin-top:1000px;">
    <h1>Add Item To Cart</h1>
    <div class="item">
        <h6 class="label">Shipping Price:</h6>
        <h6 class="value">USD <?php echo number_format($shippingPrice, 2); ?></h6>
    </div>
    <div class="item">
        <h6 class="label">Shipping Method:</h6>
        <h6 class="value"><?php echo htmlspecialchars($shippingMethod); ?></h6>
    </div>
    <div class="item">
        <form action="add-to-cart.php" method="POST">
            <!-- Hidden fields to pass product details -->
            <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($productId); ?>">
            <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($currentProductName); ?>">
            <input type="hidden" name="selling_price" id="selling_price" value="<?php echo htmlspecialchars($sellingPrice); ?>">
            <input type="hidden" name="shipping_price" id="shipping_price" value="<?php echo htmlspecialchars($shippingPrice); ?>">
            <input type="hidden" name="total_price" id="total_price" value="">

            <!-- Regular input field for Quantity -->
            <label class="label" for="quantity">Quantity:</label>
            <input type="text" name="quantity" id="quantity" value="1" class="input-field" oninput="validateQuantity(); updateTotalPrice()">

            <!-- Total Price label -->
            <label class="label" for="display_total_price">Total Price: $<span id="display_total_price">0.00</span></label>

            <!-- Add to Cart button -->
            <input type="submit" value="Add To Cart" class="submit-button">
        </form>
    </div>
</div>

<script>
   // Validate quantity input to allow only positive integers and handle the case where no input is provided
   function validateQuantity() {
        var quantityInput = document.getElementById('quantity');
        var value = quantityInput.value;

        // Remove non-numeric characters (only keep digits)
        value = value.replace(/\D/g, ''); 

        // Ensure the quantity is at least 1
        if (parseInt(value) < 0 || value === '') {
            quantityInput.value = 0;
        } else {
            quantityInput.value = value; 
        }
   }

    // Function to update the total price when quantity changes
    function updateTotalPrice() {
        var sellingPrice = parseFloat(document.getElementById('selling_price').value);
        var shippingPrice = parseFloat(document.getElementById('shipping_price').value);

        // Get the quantity value and calculate total price
        var quantity = parseInt(document.getElementById('quantity').value);

        // If quantity is invalid (NaN or empty), set it to 1
        if (isNaN(quantity) || quantity < 1) {
            quantity = '';
        }

        // Calculate the total price: (selling price + shipping price) * quantity
        var totalPrice = (sellingPrice + shippingPrice) * quantity;

        // Display total price on the page
        document.getElementById('display_total_price').textContent = totalPrice.toFixed(2);

        // Set the hidden total_price input field value to the calculated total
        document.getElementById('total_price').value = totalPrice.toFixed(2);
    }

    // Initialize the total price when the page loads
    window.onload = function() {
        updateTotalPrice();
    }
</script>
<!-- Terms And Refund Policy Section -->
<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "market";


try {
    // Establish PDO connection
    $dsn = "mysql:host=$servername;dbname=$dbname;";
    $conn = new PDO($dsn, $username, $password);
    
    // Set PDO to throw exceptions on errors
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Retrieve the product name from the URL parameter 'name'
    $productName2 = $_GET['name'] ?? '';
    
    // Decode URL parameter to handle spaces and special characters
    $productName = urldecode($productName);
    
    // Check if the product name is provided and not empty
    if (!empty($productName)) {
        // Prepare the SQL statement to fetch terms_refund_policy based on product name
        $stmt = $conn->prepare("SELECT terms_refund_policy FROM products WHERE product_name = :name");
        $stmt->bindParam(':name', $productName);
        
        // Execute the statement
        $stmt->execute();
        
        // Fetch terms_refund_policy
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Check if terms_refund_policy is fetched and not empty
        if ($row && !empty($row['terms_refund_policy'])) {
            // Display the terms_refund_policy value
            echo '
            <div class="shipping-proudcts" style="width: 50%;
position: absolute;
margin-top: 463px;
margin-left: 50px;">
            <div class="new-wrapper" style="width:100%;">
                    <div class="menu-content" style="width:50%;margin-left:0px;" id="terms">
                        <table style="width:100%; border-radius: 20px;" border="0" bgcolor="#F2F2F2">
                            <tbody>
                                <tr>
                                    <td style=" border-radius: 20px 20px 0px 0px;" bgcolor="#2f3947" style=" margin-left:100px; padding:50px;">
                                        <font color="white">
                                            <center><font style="font-size: 25px; margin-left: 0px;" color="white">Terms and Refund Policy</font></center>
                                        </font>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <textarea rows="20" style="text-align:center; fonts-size:25px; padding:50px; background-color:#FFF;" cols="80" readonly>'
                                            . htmlspecialchars($row['terms_refund_policy']) .
                                        '</textarea>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                </div>';
        } else {
            echo "Terms and Refund Policy is not available.";
        }
    } else {
        //echo "Product name not provided.";
    }
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Close connection
$conn = null;
?>
<!-- End Terms And Policy Refund Section -->
<div class="my-wrapper3" style="position: absolute; margin-top: 422px; width: 200%; margin-left: -507px; z-index: 2;">
  <div class="menu-content" id="feedback" style="margin-left: 20px; width: 50%; margin-top: -5px;">

  	
<table style="width:50%; margin-left:492px; border-radius: 20px;" border="0" bgcolor="#FFF">
	<tbody><tr><td colspan="5" style=" border-radius: 20px 20px 0px 0px;" bgcolor="#2f3947"><font color="white"></font><center><font color="white">

<font style="font-size:110%;" color="white">Product Rating -  5 <font color="orange">★</font></font>
</font></center></td></tr>


	<tr><td style="width:14%">
	<h6>	5 days 	

</h6></td><td style="width:17%"><h6><font color="orange">★</font><font color="orange">★</font><font color="orange">★</font><font color="orange">★</font><font color="orange">★</font></h6></td><td><h6>Very fast delivery. Good product.</h6></td></tr> 

			

	

			
</tbody></table>
</div>
  </div>
</div>
<br><br>

</form>

    </div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</body>
</html>
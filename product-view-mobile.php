<?php
include("myfunctions.php");
session_start();
require("db.php");

// Database connection parameters
$servername = "localhost";
$dbusername = "root";
$password = "CoheedAndCambria666!";
$dbname = "market";
$port = 888;

// Create a new database connection
$conn = new mysqli($servername, $dbusername, $password, $dbname, $port);

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
function getCategoryName($conn, $category_id) {
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asmodeus - Listings</title>
    <link rel="stylesheet" type="text/css" href="Listings_files/flexboxgrid.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Font Awesome CSS -->
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
            overflow-x:hidden;
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
</head>
<body>
    <div class="navigation">
        <div class="wrapper">
            <div class="logo">
                <a href="homepage.php"><img src="Listings_files/logo_small.png" alt="Logo"></a>
            </div>
            <ul class="menu-links">
                <li style="background-color:black;"><a href="homepage.php">Home</a></li>
                <li style="background-color:black;" class="dropdown-link">
                    <a href="orders.php?action=orders">Orders</a>
                    <div class="dropdown-content">
                        <a href="processing.php">Processing&nbsp; <span class="badge badge-secondary right">0</span></a>
                        <a href="dispatched.php">Dispatched&nbsp; <span class="badge badge-secondary right">0</span></a>
                        <a href="completed.php">Completed&nbsp; <span class="badge badge-danger right">1</span></a>
                        <a href="disputed.php">Disputed&nbsp; <span class="badge badge-secondary right">0</span></a>
                        <a href="canceled.php">Canceled</a>
                    </div>
                </li>
                <li style="background-color:black;"><a href="listings.php">Listings</a></li>
                <li style="background-color:black;" class="dropdown-link">
                    <a href="messages.php">Messages&nbsp; <span class="badge badge-secondary">0</span></a>
                    <div class="dropdown-content">
                        <a href="compose-message.php?action=compose">Compose Message</a>
                        <a href="pm_inbox.php">Inbox</a>
                        <a href="message-sent.php">Sent Items</a>
                    </div>
                </li>
                <li style="background-color:black;" class="dropdown-link">
                    <a href="wallet.php?action=wallet">Wallet</a>
                    <div class="dropdown-content">
                        <a href="exchange.php?action=exchange">Exchange</a>
                    </div>
                </li>
                <li style="background-color:black;" class="dropdown-link">
                    <a href="bug-report.php">Support</a>
                    <div class="dropdown-content">
                        <a href="faq.php">F.A.Q</a>
                        <a href="support-tickets-and-bug-reports.php">Support Tickets</a>
                        <a href="bug-report.php">Report Bug</a>
                    </div>
                </li>
                <?php if ($showControlPanel): ?>
                    <li style="background-color:black;" class="dropdown-link">
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
                <li style="background-color:black;"><a href="become-a-merchant.php"><b>Become A Merchant</b></a></li>
                <li style="background-color:black;" class="user-nav dropdown-link">
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
                <li style="background-color:black;" class="shopping-cart-link">
                    <a href="cart.php">
                        <img src="cart.png" alt="Cart" style="width: 20px; height: 25px;">
                        &nbsp;<span class="badge badge-danger">0</span>
                    </a>
                </li>
                <li style="background-color:black;" class="shopping-cart-link">
                    <a href="cart.php">
                        <img src="alert-bell.png" alt="Alerts" style="width: 20px; height: 25px;">
                        &nbsp;<span class="badge badge-danger">0</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    
<div class="mywrapper2" style="width:100%;height:450%;">
    <div class="content profile">
      
         <div class="block">
   


<table border="0" style="margin-left:0px;width:50%;">
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
    $query = "SELECT name FROM products WHERE name = '$productName'";
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
$password = "CoheedAndCambria666!";
$database = "market";
$port = 888;

// Create a new database connection
$con = mysqli_connect($host, $username, $password, $database, $port);

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
$products = getAll("products");
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

// Fetch product by name
$productName = isset($_GET['name']) ? $_GET['name'] : '';
$productName = mysqli_real_escape_string($con, $productName);
$query = "SELECT * FROM products WHERE name LIKE '%$productName%'";
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
$query = "SELECT * FROM products WHERE name LIKE '%$productName%'";
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
$query = "SELECT * FROM products WHERE name LIKE '%$productName%'";
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
$query = "SELECT * FROM products WHERE name LIKE '%$productName%'";
$result = mysqli_query($con, $query);

// Check if products are found
if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);

    // Fetch vendor information
    $vendorInfo = getVendorInfo2($con, $row['vendor_name'])


    
        ?>
         <div class="my-container2" style="height:100%;width:175%;border:1px solid black; -685px; margin-top: 720px;overflow-x:hidden;">
        <div class="my-container" style="width:100%;background-color:white;text-align:center;padding:5%">
        <br>
        <h2 style="font-size:20px;"><?= htmlspecialchars($row['name']); ?></h2>
    </div>
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
// Function to retrieve product details by name
function getProductByName3($con, $productName) {
    $query = "SELECT * FROM products WHERE name = ?";
    $statement = $con->prepare($query);
    $statement->bind_param("s", $productName);
    $statement->execute();
    $result = $statement->get_result();
    $product = $result->fetch_assoc();
    $statement->close(); // Close the prepared statement
    return $product;
}



// Establish database connection
$con = mysqli_connect($host, $username, $password, $database, $port);

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit();
}

// Retrieve the product name from the URL parameter
$productName = $_GET['name'] ?? '';

// Function to get product details (assuming it's defined elsewhere)
function getProductByName2($con, $name) {
    // Example query - adjust as needed
    $stmt = $con->prepare("SELECT * FROM products WHERE name = ?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Function to get category name (assuming it's defined elsewhere)
function getCategoryName2($con, $categoryId) {
    $stmt = $con->prepare("SELECT name FROM categories WHERE id = ?");
    $stmt->bind_param("i", $categoryId);
    $stmt->execute();
    $result = $stmt->get_result();
    $category = $result->fetch_assoc();
    return $category['name'] ?? 'Unknown';
}

// Check if the product name is provided in the URL
if (!empty($productName)) {
    // Fetch the product details from the database
    $product = getProductByName2($con, $productName);

    // Check if the product exists
    if ($product) {
        // Fetch category name
        $categoryName = getCategoryName2($con, $product['category_id']);

        // Display product details HTML
        ?>
        <div class="my-new-container">
            <table border="0" style="width: 100%;margin-left:0px;">
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
                                            <h2 style="text-align:center;"><?= htmlspecialchars($product['name']); ?></h2>
                                            <img style="max-width: 100%;width:50%;margin-left:100px;height:50%;" src="uploads/<?= $product['image'] ?? 'default.jpg'; ?>" alt="<?= htmlspecialchars($product['name']); ?>">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 50%; text-align: right;"><b>Price:</b></td>
                                        <td>USD <?= $product['selling_price'] ?? '0.00'; ?></td>
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
                                        <td><?= htmlspecialchars($product['ships_from'] ?? 'Unknown'); ?> --> <?= htmlspecialchars($product['ships_to'] ?? 'Unknown'); ?></td>
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
        // No matching product found
        echo "No matching product found.";
    }
} else {
    // Product name not provided in the URL
    echo "Product name not provided.";
}

// Close the database connection after use
mysqli_close($con);
?>




<table style="width:100%; margin-top:20px;font-size:20px;"><tbody><tr><td style=" border-radius: 20px 20px 0px 0px;" bgcolor="#2f3947">	<h6>
<font style="font-size:115%;" color="white"></font><center><font style="font-size:115%;" color="white">All Cocaine products from Vendor<br></font></center></h6></td></tr>
<tr><td style="width:100%; border-radius: 0px 0px 20px 20px;" bgcolor="#FFF"><br>
<center style=""><a href="products.php?action=view&amp;id=217409&amp;pid=27680&amp;fid=538278"><h6>1GM UNCUT BOLIVIAN COCAINE 98% PURE UK TO UK</h6></a></center><center><a href="products.php?action=view&amp;id=651964&amp;pid=27681&amp;fid=848008"><h6>2GM UNCUT BOLIVIAN COCAINE 98% PURE UK TO UK</h6></a></center><center><a href="products.php?action=view&amp;id=818702&amp;pid=27682&amp;fid=907789"><h6>0.5G UNCUT BOLIVIAN COCAINE 98% PURE UK TO UK</h6></a></center><center><a href="products.php?action=view&amp;id=835853&amp;pid=27683&amp;fid=216446"><h6>3.5GM UNCUT BOLIVIAN COCAINE 98% PURE UK TO UK </h6></a></center><center><a href="products.php?action=view&amp;id=585374&amp;pid=27684&amp;fid=187737"><h6>7GM UNCUT BOLIVIAN COCAINE 98% PURE UK TO UK</h6></a></center><center><a href="products.php?action=view&amp;id=161779&amp;pid=27686&amp;fid=983552"><h6>0.5GM BOLIVIAN COCAINE 90% PURE UK TO UK </h6></a></center>
<style type="text/css">

#show2,#content2{display:none;}
    #show2:checked~#content2{display:block;}
</style>

 <font color="#3f345f"></font><center><font color="#3f345f">  <label for="show2"><h6><font color="#007bff">[ Show all <b>19</b> products ]</font></h6></label></font></center>        
       <input id="show2" type="checkbox">
<div id="content2">

<center><a href="products.php?action=view&amp;id=157373&amp;pid=27688&amp;fid=722657"><h6>14GM UNCUT BOLIVIAN COCAINE 98% PURE UK TO UK</h6></a></center><center><a href="products.php?action=view&amp;id=435829&amp;pid=27718&amp;fid=173036"><h6>2GM BOLIVIAN COCAINE 90% PURE UK TO UK</h6></a></center><center><a href="products.php?action=view&amp;id=390699&amp;pid=27719&amp;fid=126266"><h6>3.5GM BOLIVIAN COCAINE 90% PURE UK TO UK</h6></a></center><center><a href="products.php?action=view&amp;id=311303&amp;pid=27720&amp;fid=412913"><h6>7GM BOLIVIAN COCAINE 90% PURE UK TO UK</h6></a></center><center><a href="products.php?action=view&amp;id=197517&amp;pid=27721&amp;fid=828316"><h6>14GM BOLIVIAN COCAINE 90% PURE UK TO UK</h6></a></center><center><a href="products.php?action=view&amp;id=390142&amp;pid=27722&amp;fid=708134"><h6>28GM BOLIVIAN COCAINE 90% PURE UK TO UK</h6></a></center><center><a href="products.php?action=view&amp;id=345951&amp;pid=27723&amp;fid=952086"><h6>1GM BOLIVIAN COCAINE 75% PURE UK TO UK</h6></a></center><center><a href="products.php?action=view&amp;id=481081&amp;pid=27724&amp;fid=468835"><h6>2GM BOLIVIAN COCAINE 75% PURE UK TO UK</h6></a></center><center><a href="products.php?action=view&amp;id=783433&amp;pid=27725&amp;fid=450441"><h6>0.5GM BOLIVIAN COCAINE 75% PURE UK TO UK</h6></a></center><center><a href="products.php?action=view&amp;id=119162&amp;pid=27726&amp;fid=879019"><h6>3.5GM BOLIVIAN COCAINE 75% PURE UK TO UK</h6></a></center><center><a href="products.php?action=view&amp;id=885197&amp;pid=27727&amp;fid=599786"><h6>7GM BOLIVIAN COCAINE 75% PURE UK TO UK</h6></a></center><center><a href="products.php?action=view&amp;id=919792&amp;pid=27728&amp;fid=274407"><h6>14GM BOLIVIAN COCAINE 75% PURE UK TO UK</h6></a></center><center><a href="products.php?action=view&amp;id=478721&amp;pid=27729&amp;fid=162795"><h6>28GM BOLIVIAN COCAINE 75% PURE UK TO UK</h6></a></center>
 </div>
 
	</td></tr></tbody></table>



</td></tr></tbody></table>


</center>
<br>



<div class="outer-wrapper-menu-container" style="width:100%;">
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
$port = 888;               // Database port
$username = 'root';        // MySQL username
$password = 'CoheedAndCambria666!'; // MySQL password
$database = 'market';      // Database name

// Create a new mysqli connection
$con = mysqli_connect($host, $username, $password, $database, $port);

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
    $sql = "SELECT product_description FROM products WHERE name = ?";

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
        <div class="menu-content" id="description" style="width: 100%; position: absolute; margin-top: 0px;">
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



		
	

</div>
<!-- Terms And Refund Policy Section -->
<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "CoheedAndCambria666!";
$dbname = "market";
$port = 888;

try {
    // Establish PDO connection
    $dsn = "mysql:host=$servername;dbname=$dbname;port=$port";
    $conn = new PDO($dsn, $username, $password);
    
    // Set PDO to throw exceptions on errors
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Retrieve the product name from the URL parameter 'name'
    $productName = $_GET['name'] ?? '';
    
    // Decode URL parameter to handle spaces and special characters
    $productName = urldecode($productName);
    
    // Check if the product name is provided and not empty
    if (!empty($productName)) {
        // Prepare the SQL statement to fetch terms_refund_policy based on product name
        $stmt = $conn->prepare("SELECT terms_refund_policy FROM products WHERE name = :name");
        $stmt->bindParam(':name', $productName);
        
        // Execute the statement
        $stmt->execute();
        
        // Fetch terms_refund_policy
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Check if terms_refund_policy is fetched and not empty
        if ($row && !empty($row['terms_refund_policy'])) {
            // Display the terms_refund_policy value
            echo '
            <div class="shipping-proudcts" style="width: 50%; position: absolute; margin-top: 0px; margin-left:0px;">
            <div class="new-wrapper" style="width:50%;">
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
        echo "Product name not provided.";
    }
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Close connection
$conn = null;
?>
<!-- End Terms And Policy Refund Section -->
<div class="my-wrapper3" style="position: absolute;
margin-top: 600px;
width: 180%;
margin-left: 0px;
z-index: 2;;">
  <div class="menu-content" id="feedback" style="margin-left:20px;width:100%;margin-top:0px;">

  	
<table style="width:50%; margin-left:0px; border-radius: 20px;" border="0" bgcolor="#FFF">
	<tbody><tr><td colspan="5" style=" border-radius: 20px 20px 0px 0px;" bgcolor="#2f3947"><font color="white"></font><center><font color="white">

<font style="font-size:110%;" color="white">Product Rating -  5 <font color="orange">★</font></font>
</font></center></td></tr>


	<tr><td style="width:14%">
	<h6>	5 days 	

</h6></td><td style="width:17%"><h6><font color="orange">★</font><font color="orange">★</font><font color="orange">★</font><font color="orange">★</font><font color="orange">★</font></h6></td><td><h6>Very fast delivery. Good product.</h6></td></tr> 

			

	<!--<tr><td style="width:14%">
	<h6>	14 days 	

</h6></td>

<td style="width:17%"><h6><font color="orange">★</font><font color="orange">★</font><font color="orange">★</font><font color="orange">★</font><font color="orange">★</font></h6></td><td><h6>Great service, as always, fast delivery, good product, good stealth. Thanks </h6></td></tr> 

			

	<tr><td style="width:14%">
	<h6>	17 days 	

</h6></td><td style="width:17%"><h6><font color="orange">★</font><font color="orange">★</font><font color="orange">★</font><font color="orange">★</font><font color="orange">★</font></h6></td><td><h6>Enter your comment here..</h6></td></tr> 

			

	<tr><td style="width:14%">
	<h6>	29 days 	

</h6></td><td style="width:17%"><h6><font color="orange">★</font><font color="orange">★</font><font color="orange">★</font><font color="orange">★</font><font color="orange">★</font></h6></td><td><h6>Speedy Delivery and good product. Thank you</h6></td></tr> 

			

	<tr><td style="width:14%">
	<h6>	1 month 	

</h6></td><td style="width:17%"><h6><font color="orange">★</font><font color="orange">★</font><font color="orange">★</font><font color="orange">★</font><font color="orange">★</font></h6></td><td><h6>Always a quality product. Fast delivery and very generous with the weight. Reliable guys and always look after their customers.</h6></td></tr> 

			

	<tr><td style="width:14%">
	<h6>	2 months 	

</h6></td><td style="width:17%"><h6><font color="orange">★</font><font color="orange">★</font><font color="orange">★</font><font color="orange">★</font><font color="orange">★</font></h6></td><td><h6>Great service, speedy delivery and quality product</h6></td></tr> 

			

	<tr><td style="width:14%">
	<h6>	2 months 	

</h6></td><td style="width:17%"><h6><font color="orange">★</font><font color="orange">★</font><font color="orange">★</font><font color="orange">★</font><font color="orange">★</font></h6></td><td><h6>Enter your comment here..</h6></td></tr> 

			

	<tr><td style="width:14%">
	<h6>	2 months 	

</h6></td><td style="width:17%"><h6><font color="orange">★</font><font color="orange">★</font><font color="orange">★</font><font color="orange">★</font><font color="orange">★</font></h6></td><td><h6>Fast delivery  good product</h6></td></tr> 

			

	<tr><td style="width:14%">
	<h6>	2 months 	

</h6></td><td style="width:17%"><h6><font color="orange">★</font><font color="orange">★</font><font color="orange">★</font><font color="orange">★</font><font color="orange">★</font></h6></td><td><h6>Enter your comment here..</h6></td></tr> 

			

	<tr><td style="width:14%">
	<h6>	2 months 	

</h6></td><td style="width:17%"><h6><font color="orange">★</font><font color="orange">★</font><font color="orange">★</font><font color="orange">★</font><font color="orange">★</font></h6></td><td><h6>Enter your comment here..</h6></td></tr> 

			

	<tr><td style="width:14%">
	<h6>	2 months 	

</h6></td><td style="width:17%"><h6><font color="orange">★</font><font color="orange">★</font><font color="orange">★</font><font color="orange">★</font><font color="orange">★</font></h6></td><td><h6>Nice bit, tasty and overweight this time, thank you my brother</h6></td></tr> 

			

	<tr><td style="width:14%">
	<h6>	4 months 	

</h6></td><td style="width:17%"><h6><font color="orange">★</font><font color="orange">★</font><font color="orange">★</font><font color="orange">★</font><font color="orange">★</font></h6></td><td><h6>Enter your comment here..</h6></td></tr> -->

			
</tbody></table>
</div>
  </div>
</div>
<br><br>
<div class="shipping-proudcts" style="width:100%; margin-top:880px;">
<div class="my-outer-wrapper" style="width:100%; margin-left:0px; border:1px solid black; padding:30px;background-color:white;margin-top:-0px;">
<div class="shipping-options-wrapper" style="width:100%;">
<h6 style="font-size:30px;"><i><b><i class="fas fa-shipping-fast" style="font-size:30px;"></i> *Shipping options</b></i></h6> <br>


<form action="products.php?action=cart" method="POST">

<center>
<table style="width:100%" border="0">

<tbody><tr>
	<td style="width:15%">
		<h6><font color="black"> 

<b><font color="#007bff" style="font-size:20px;">USD 0</font></b>



		  </font></h6></td><td><h6><font color="black"><font color="3f345f" style="font-size:20px;"> FREE ROYALMAIL SPECIAL NDD</font></font></h6><font color="black"><font color="3f345f">
	</font></font></td>

<td>
    <input type="radio" id="vi" name="method1" value="method1" checked="">

</td>
</tr>

  
</tbody></table>
</center>
<br>
<br>
<br>
<h6 style="font-size:30px;"><i><b><i style="font-size:30px;" class="fas fa-money-check"></i> *Payment Method (

Escrow
)</b></i></h6> <br>

</div>

<table border="0"><tbody><tr><td style="width:75%;"><center>

     <select style="width: 40%;" id="btcxmr" style="width:20%;" name="btcxmr" class="form-control">
                	<option value="9342ewf651hztz09437tjzffd1569">Bitcoin (BTC)</option>
  <option value="9432761fj84j9fjAoiu938476f4f3435">Monero (XMR)</option>

</select>

       </center></td><td>
       	

<div class="quantity-wrapper" style="">
<b><font color="#3f345f">Quantity: </font></b>

<input type="number" value="1" name="Quantity" style="" placeholder="1" step="1" min="1" max="10" id="number">
</div>



       </td></tr></tbody></table><br><br><br>
<div class="my-wrapper2" style="width: 100%; height: 450%;">
<table border="0">
<tbody><tr><td></td><td style=" border-radius: 20px 20px 0px 0px;" bgcolor="#2f3947"><font color="white"><center><b>*Pay from Balance</b></center></font></td><td></td><td style=" border-radius: 20px 20px 0px 0px;" bgcolor="#2f3947"><font color="white"><center><b>*Direct Payment</b></center></font></td></tr>
	<tr><td></td><td style="width:48%;border-radius: 0px 0px 20px 20px;" valign="top" bgcolor="#F2F2F2">


<center>The wallet balance needs to be topped up<br> Top up your wallet <a target="_blank" href="balance.php">here</a><br><br>
<table border="0">

<tbody><tr><td colspan="3"><center>
 <label>
<input name="prid" type="hidden" value="27683">
<input style="padding: 10px 25px 8px;
  color: #fff;
  background-color: #0067ab;
  text-shadow: rgba(0,0,0,0.24) 0 1px 0;
  font-size: 16px;
  box-shadow: rgba(255,255,255,0.24) 0 2px 0 0 inset,#fff 0 1px 0 0;
  border: 1px solid #0164a5;
  border-radius: 30px;
  margin-top: 10px;
  cursor: pointer;
}" class="addtocart2 button3" type="submit" value="&nbsp; Continue &nbsp;"></label>

</center></td></tr></tbody></table>



<form action="products.php" method="POST">



</form></center></td><td>OR</td><td style="border-radius: 0px 0px 20px 20px;" valign="top" bgcolor="#F2F2F2">
<center>
Pay directly without having to top up your wallet balance<br>
	<br>
<center>

		<center>









<label for="my-popup-trigger"><font class="purchase2 button3" style="margin-top: 40px;
  height: 40px;
  text-align: center;
  padding: 6px;
  border-radius:30px;" href="#">&nbsp; Continue  &nbsp;</font></label>
<input type="checkbox" id="my-popup-trigger" hidden="">
<div class="my-popup">





<br>
<table border="0" bgcolor="#F2F2F2">
<tbody><tr><td colspan="2"><center>
<h3>Direct Payment</h3><hr>
</center></td></tr>













  <label for="my-popup-trigger"><b><u>Close</u></b>&nbsp;&nbsp;</label> <br>
</div>
</div>

<style>
  .my-popup {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
      width: 1000px; 
    height: 700px;
    transform: translate(-50%, -50%);
    background-color: #fff;
    z-index: 1;
    padding: 1em;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
    overflow: auto; 
  }

  #my-popup-trigger:checked + .my-popup {
    display: block;
  box-shadow: 0 0 2000px rgba(0, 0, 0, 0.5);
  }

  .my-popup label {
    position: absolute;
    top: 0;
    right: 0;
    font-size: 1em;
    padding: 0.2em 0.4em;
    cursor: pointer;
  }
</style>






</center></center></center></td></tr></tbody></table>
</form></center></td></tr></tbody></table>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</body>
</html>
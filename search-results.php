<?php
include("myfunctions.php");
session_start();
require_once("db.php");

// Get the category_id from the URL
$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;

// Function to get category name
function getCategoryName2($conn, $category_id) {
    $query = "SELECT name FROM categories WHERE id = ?";
    $stmt = $con->prepare($query);
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
?>

<!DOCTYPE html>
<html><head>
    <title>Asmodeus - Listings</title>        <meta http-equiv="Content-type" content="text/html; charset=UTF-8">
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
                    <li class="active"><a href="listings.php">Listings</a></li>
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
$password = "CoheedAndCambria666!";
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
    $row = $result->fetch_assoc();
    $order_count = $row['order_count'];
} else {
    // Handle query error
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
$query = "SELECT product_id, name, quantity, total_price FROM cart WHERE username = ?";
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
$password = "CoheedAndCambria666!";
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
$password = "CoheedAndCambria666!";
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
        <div class="row" style="">
            <div class="col-md-3 sidebar-navigation">
                <div class="container listing-sorting detail-container" style="height: auto;">
        <div class="container-header">
            <div class="sprite sprite--diagram"></div>&nbsp; Browse Categories
        </div>
        <div style="overflow:visible;">
            <ul>
                <?php
                // SQL query to count products
$sql = "SELECT COUNT(*) AS product_count FROM products";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output data of each row
    while($row = $result->fetch_assoc()) {
        // Display the product count inside the span with class "amount"
        echo '<a href="listings.php"><input type="checkbox" name="catid" value="">
                    <b>Drugs and Chemicals</b>
                    <span class="amount" style="display: block;
  float: right;
  padding: 5px; 9px;
  font-weight: 600;
  min-width: 30px;
  text-align: center;
  margin-top: -25px;
  margin-left:250px;
  position:absolute;
  color: #4B7AA2;
  background: rgba(75, 122, 162, 0.3);
  border-radius: 15px;">' . $row["product_count"] . '</span></a>';
    }
} else {
    echo "0 results";
}
                // SQL query to get categories and their product counts
                $query = "SELECT c.id AS category_id, c.name AS category_name, COUNT(p.id) AS product_count
                          FROM categories c
                          LEFT JOIN products p ON c.id = p.category_id
                          GROUP BY c.id, c.name";
                $result = $conn->query($query);

                if ($result->num_rows > 0) {
                    // Output data for each category
                    while ($row = $result->fetch_assoc()) {
                        echo '<li>
                            <a href="http://wbxnudwhsfpta4ljzwm7mhkzph4ntpsvidubwr2gptfqyg4fohyrv7ad.onion/bohemia/listings.php?category_id=' . $row['category_id'] . '">
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
                    <!-- Populate options here -->
                </select>
            </div>
            <div class="form-group">
                <div><label>Ships to</label></div>
                <select class="form-control" name="shipTo">
                    <option selected="selected"></option>
                    <!-- Populate options here -->
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
	    	
                <div class="container nopadding" style="margin-top:15px;">
                    <table class="table exchange-table" cellspacing="0" cellpadding="0">
                        <tbody><tr>
                            <th></th>
                                                            <th><strong>
                                    <div class="sprite sprite--bitcoin" style="top:2px;"></div>&nbsp;Bitcoin 
                                </strong></th>
                                                            <th><strong>
                                    <div class="sprite sprite--monero" style="top:2px;"></div>&nbsp;Monero 
                                </strong></th>
                                                    </tr>
                                                    <tr>
                                <td><strong>
                                    AUD                                </strong></td>
                                    <td class="text-center"><?php require_once("aud-current-price-btc.php") ?></td>
                               
                                    <td class="text-center"><?php include("aud-current-price-monero.php") ?></td>
                            </tr>
                                                    <tr>
                                <td><strong>
                                    CAD                                </strong></td>
                                <td class="text-center"><?php require_once("cad-current-price-btc.php") ?></td>
                                <td class="text-center"><?php include("cad-current-price-monero.php") ?></td>
                            </tr>
                                                    <tr>
                                <td><strong>
                                    EUR                                </strong></td>
                                <td class="text-center"><?php require_once("euro-current-price-btc.php") ?></td>
                                <td class="text-center"><?php include("euro-current-price-monero.php") ?></td>
                            </tr>
                                                    <tr>
                                <td><strong>
                                    GBP                                </strong></td>
                                <td class="text-center"><?php require_once("GBP-current-price-btc.php") ?></td>
                                <td class="text-center"><?php include("british-pound-current-price-monero.php") ?></td>
                            </tr>
                                                    <tr>
                                <td><strong>
                                    USD                                </strong></td>
                                <td class="text-center"><?php require_once("usd-current-price-btc.php") ?></td>
                                <td class="text-center"><?php include("usd-current-price-monero.php") ?></td>
                            </tr>
                                            </tbody></table>
                </div>
            </div>
          <div class="col-md-9 sidebar-content-right listing-content">
    <div class="container">
        <?php
        // Database connection
        $conn = new mysqli('localhost', 'root', 'CoheedAndCambria666!', 'market', 888);
        
        if ($conn->connect_error) {
            die('Connection failed: ' . $conn->connect_error);
        }

        // Handling the search when the form is submitted
        if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['query'])) {
            $query = trim($_GET['query']);
            $shipFrom = $_GET['shipFrom'] ?? '';
            $shipTo = $_GET['shipTo'] ?? '';
            $sortby = $_GET['sortby'] ?? 'mosthighest';

            // Prepare the SQL query
            $sql = "SELECT * FROM products WHERE LOWER(name) LIKE LOWER(?) OR LOWER(vendor_name) LIKE LOWER(?)";
            $params = ["%$query%", "%$query%"];
            $types = 'ss';

            // Shipping filters
            if (!empty($shipFrom)) {
                $sql .= " AND ships_from = ?";
                $params[] = $shipFrom;
                $types .= 's';
            }
            if (!empty($shipTo)) {
                $sql .= " AND ships_to = ?";
                $params[] = $shipTo;
                $types .= 's';
            }

            // Sorting
            switch ($sortby) {
                case 'cheapest':
                    $sql .= " ORDER BY selling_price ASC";
                    break;
                case 'oldest':
                    $sql .= " ORDER BY created_at ASC";
                    break;
                case 'newest':
                    $sql .= " ORDER BY created_at DESC";
                    break;
                case 'mostrated':
                    $sql .= " ORDER BY vendor_rating DESC";
                    break;
                case 'mosthighest':
                default:
                    $sql .= " ORDER BY selling_price DESC";
                    break;
            }

            // Prepare the statement
            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                die('Prepare failed: ' . htmlspecialchars($conn->error));
            }

            // Bind parameters
            $stmt->bind_param($types, ...$params);

            // Execute the statement
            if (!$stmt->execute()) {
                die('Execute failed: ' . htmlspecialchars($stmt->error));
            }

            // Get the result
            $result = $stmt->get_result();
            if ($result === false) {
                die('Get result failed: ' . htmlspecialchars($stmt->error));
            }

            $results = $result->fetch_all(MYSQLI_ASSOC);
        }

        // Display products
        if (!empty($results)) {
            foreach ($results as $row) {
                $productName = $row['name'];
                $productId = $row['id'];
                $categoryName = $row['category'] ?? 'General';
                $vendorName = $row['vendor_name'] ?? 'Unknown';
                $vendorRating = $row['vendor_rating'] ?? 0;
                $vendorLevel = $row['vendor_level'] ?? 1;
                $totalOrders = $row['total_orders'] ?? 0;
                $shipsFrom = $row['ships_from'] ?? 'Unknown';
                $shipsTo = $row['ships_to'] ?? 'Unknown';
                $timesSold = $row['times_sold'] ?? 0;
                $totalSold = $row['total_sold'] ?? 0;
                $formatted_price = number_format($row['selling_price'], 2);
                $bitcoinPrice = $row['bitcoin_price'] ?? 0;

                // Display product details
                echo '<div class="product-listing">
                        <div class="product-link">
                            <div class="product">
                                <div class="container">
                                    <div class="product-photo">
                                        <img src="uploads/' . ($row['image'] ?? 'default.jpg') . '" alt="' . htmlspecialchars($productName) . '">
                                    </div>
                                    <div class="product-details">
                                        <div class="product-heading">
                                            <h2>
                                                <a href="product-view.php?name=' . urlencode($productName) . '&id=' . urlencode($productId) . '" class="product-link">' . htmlspecialchars($productName) . '</a>
                                            </h2>
                                            <span class="shadow-text smalltext">In <strong>' . htmlspecialchars($categoryName) . '</strong></span><br>
                                            <span>
                                                <b>Sold By <a href="#">' . htmlspecialchars($vendorName) . '</a> 
                                                (<img src="images/icons8-star-48.png" style="height: 13.2px; width:13.2px;" alt="Rating">' . htmlspecialchars($vendorRating) . ')</b>
                                                <span class="badge badge-pill-level-1" style="display:inline-block;vertical-align: middle;margin-bottom: 1px; margin-left:5px;">Level ' . htmlspecialchars($vendorLevel) . '</span>
                                            </span>
                                            <div class="sprite sprite--shopping-cart" title="Total Sales" style="float: none; display: inline-flex; margin-left:5px;">
                                                <img src="images/shopping-cart.png" style="width:18px;height:18px; margin-top:2px;" alt="Total Sales">' . htmlspecialchars($totalOrders) . '
                                            </div>
                                            <br>
                                            <span><b>Shipped From</b> ' . htmlspecialchars($shipsFrom) . '</span><br>
                                            <span><b>Shipped To</b> ' . htmlspecialchars($shipsTo) . '</span><br>
                                        </div>
                                        <div class="product-details-bottom">
                                            <div class="sold-amount smalltext">Sold ' . htmlspecialchars($timesSold) . ' in the last 48 hours</div>
                                            <span class="smalltext">Sold ' . htmlspecialchars($totalSold) . ' in total</span>
                                        </div>
                                    </div>
                                    <div class="product-price">
                                        <span class="badge badge-primary">Unlimited Available</span>
                                        <h2>USD ' . $formatted_price . '</h2>
                                        <span class="shadow-text smalltext boldtext">' . number_format($bitcoinPrice, 8) . ' BTC</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>';
            }
        }
        ?>
    </div>
</div>

                           
                         

                          <?php
$rows_per_page = 20;

// Database connection parameters
$servername = "localhost";
$dbusername = "root";
$password = "CoheedAndCambria666!";
$dbname = "market";

// Create a new database connection
$conn = new mysqli($servername, $dbusername, $password, $dbname, 888);

// Check connection
if ($conn->connect_error) {
    die("Failed to connect to MySQL: " . $conn->connect_error);
}

// Calculate total records
$records_query = "SELECT COUNT(*) AS total FROM products";
$records_result = mysqli_query($conn, $records_query);
$total_records_row = mysqli_fetch_assoc($records_result);
$total_records = $total_records_row['total'] ?? 0;

// Calculate total pages
$total_pages = ceil($total_records / $rows_per_page);

// Initialize current page variable
$current_page = isset($_GET['page']) ? max(1, min(intval($_GET['page']), $total_pages)) : 1;

// Calculate start index for the current page
$start = ($current_page - 1) * $rows_per_page;

// Query to retrieve products for the current page
$products_query = "SELECT * FROM products LIMIT ?, ?";
$stmt = $conn->prepare($products_query);
$stmt->bind_param("ii", $start, $rows_per_page);
$stmt->execute();
$result = $stmt->get_result();

// Function to format price as currency with 2 decimal places
function formatCurrency($price) {
    return number_format($price, 2);
}

// Function to retrieve products based on parameters
function getProducts($conn, $categoryId = null, $categoryName = null) {
    if ($categoryId !== null && $categoryName !== null) {
        $query = "SELECT * FROM products WHERE category_id = ? AND category_name = ? ORDER BY id";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("is", $categoryId, $categoryName);
    } else {
        $query = "SELECT * FROM products ORDER BY id";
        $stmt = $conn->prepare($query);
    }
    $stmt->execute();
    return $stmt->get_result();
}

// Function to retrieve product category name by ID
function getCategoryName($conn, $categoryId) {
    $query = "SELECT name FROM categories WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $categoryId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0 ? $result->fetch_assoc()['name'] : "Unknown";
}

// Function to retrieve vendor information by vendor name
function getVendorInfo($conn, $vendorName) {
    $query = "SELECT username, vendor_rating, total_orders, level FROM register WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $vendorName);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0 ? $result->fetch_assoc() : false;
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
    return $bitcoinPriceUSD > 0 ? $usdPrice / $bitcoinPriceUSD : 0;
}

// Get category ID from request or default to 0
$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;

// Check if category_id is 0
if ($category_id == 0) {
    $products_query = "SELECT * FROM products ORDER BY id LIMIT ?, ?";
    $stmt = $conn->prepare($products_query);
    $stmt->bind_param("ii", $start, $rows_per_page);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Fetch total records
    $total_records_query = "SELECT COUNT(*) AS total FROM products";
    $total_records_result = mysqli_query($conn, $total_records_query);
    $total_records_row = mysqli_fetch_assoc($total_records_result);
    $total_records = $total_records_row['total'] ?? 0;

    // Calculate total pages
    $total_pages = $total_records > 0 ? ceil($total_records / $rows_per_page) : 1;

    // Display products
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $categoryName = getCategoryName($conn, $row['category_id']);
            $vendorName = $row['vendor_name'] ?? "Unknown";
            $vendorInfo = getVendorInfo($conn, $vendorName) ?: ['vendor_rating' => 0, 'total_orders' => 0, 'level' => 0];

            $timesSold = $row['times_sold_last_48_hr'] ?? 0;
            $totalSold = $row['total_sold'] ?? 0;
            $shipsFrom = $row['ships_from'] ?? "Unknown";
            $shipsTo = $row['ships_to'] ?? "Unknown";
            $sellingPrice = $row['selling_price'] ?? 0;

            $bitcoinPrice = convertToBitcoin($sellingPrice);
            $formatted_price = formatCurrency($sellingPrice);

            $productName = htmlspecialchars($row['name']);
            $productUrlName = rawurlencode($productName);

            // Output product details (display logic goes here)
        }
    }
} else {
    // Query to fetch products by category ID
    $products_query = "SELECT * FROM products WHERE category_id = ? ORDER BY id LIMIT ?, ?";
    $stmt = $conn->prepare($products_query);
    $stmt->bind_param("iii", $category_id, $start, $rows_per_page);
    $stmt->execute();
    $result = $stmt->get_result();

    // Query to count total records for the category
    $total_records_query = "SELECT COUNT(*) AS total FROM products WHERE category_id = ?";
    $stmt = $conn->prepare($total_records_query);
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $total_records_result = $stmt->get_result();
    $total_records_row = $total_records_result->fetch_assoc();
    $total_records = $total_records_row['total'] ?? 0;

    // Calculate total pages
    $total_pages = $total_records > 0 ? ceil($total_records / $rows_per_page) : 1;

    // Display products
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $categoryName = getCategoryName($conn, $row['category_id']);
            $vendorName = $row['vendor_name'] ?? "Unknown";
            $vendorInfo = getVendorInfo($conn, $vendorName) ?: ['vendor_rating' => 0, 'total_orders' => 0, 'level' => 0];

            $timesSold = $row['times_sold_last_48_hr'] ?? 0;
            $totalSold = $row['total_sold'] ?? 0;
            $shipsFrom = $row['ships_from'] ?? "Unknown";
            $shipsTo = $row['ships_to'] ?? "Unknown";
            $sellingPrice = $row['selling_price'] ?? 0;

            $bitcoinPrice = convertToBitcoin($sellingPrice);
            $formatted_price = formatCurrency($sellingPrice);

            $productName = htmlspecialchars($row['name']);
            $productUrlName = rawurlencode($productName);

            // Output product details (display logic goes here)
        }
    }
}

mysqli_close($conn);
?>

<ul class="pagination justify-content-end">
    <li class="page-item <?php echo ($current_page == 1) ? 'disabled' : ''; ?>">
        <a class="page-link" href="listings.php?page=1" tabindex="-1" aria-disabled="true">First</a>
    </li>
    <li class="page-item <?php echo ($current_page == 1) ? 'disabled' : ''; ?>">
        <a class="page-link" href="listings.php?page=<?php echo $current_page - 1; ?>" tabindex="-1" aria-disabled="true">Previous</a>
    </li>
    <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
        <li class="page-item <?php echo ($i == $current_page) ? 'active' : ''; ?>">
            <a class="page-link" href="listings.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
        </li>
    <?php endfor; ?>
    <li class="page-item <?php echo ($current_page == $total_pages || $total_pages == 0) ? 'disabled' : ''; ?>">
        <a class="page-link" href="listings.php?page=<?php echo $current_page + 1; ?>">Next</a>
    </li>
    <li class="page-item <?php echo ($current_page == $total_pages || $total_pages == 0) ? 'disabled' : ''; ?>">
        <a class="page-link" href="listings.php?page=<?php echo $total_pages; ?>">Last</a>
    </li>
</ul>

<div style="margin-right: auto; padding: .5rem .75rem;">
    Showing products <?php echo $start + 1; ?> to <?php echo min($start + $rows_per_page, $total_records); ?> of <?php echo $total_records; ?> Total items
</div>



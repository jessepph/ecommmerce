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
    <title>Asmodeus - Listings</title>
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
$password = "";
$dbname = "market";

// Create a new database connection
$conn = new mysqli($servername, $dbusername, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Prepare the SQL query
$sql = "SELECT SUM(order_count) AS total_order_count FROM cart WHERE username = ?";

// Prepare the statement
$stmt = $conn->prepare($sql);

// Check if the statement was prepared successfully
if ($stmt === false) {
    echo "Error preparing statement: " . $conn->error;
    exit;
}

// Bind the parameter
$stmt->bind_param("s", $safe_username);

// Execute the query
$stmt->execute();

// Get the result
$result = $stmt->get_result();

// Initialize $order_count to avoid undefined variable warning
$order_count = 0;

// Initialize total order count
$total_order_count = 0;

// Check if the query was successful and fetch the data
if ($result) {
    $row = $result->fetch_assoc();
    $total_order_count = $row['total_order_count'] ?: 0;  // Default to 0 if null or empty
} else {
    // Handle query error
    echo "Error fetching total order count: " . $conn->error;
}

// Close the statement
$stmt->close();

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

// Example: Logic to define $badge_class and $badge_text2
$unread_count = 0;  // Just for illustration
$badge_class = isset($badge_class) ? $badge_class : 'badge-default';  // Fallback to a default class
$badge_text2 = isset($badge_text2) ? $badge_text2 : 0;  // Fallback to 0 if not set

// You can change $badge_class depending on the unread count
if ($unread_count > 0) {
    $badge_class = 'badge-danger';  // Red for unread messages
    $badge_text2 = $unread_count;  // Show the actual unread count
} else {
    $badge_class = 'badge-secondary';  // Grey when no unread messages
    $badge_text2 = 0;  // Show 0 when there are no unread messages
}
?>

<!-- The HTML and PHP code where you use the badge class and text -->
<span class="badge <?php echo $badge_class; ?>"
      style="padding: 0.3em 0.4em; font-size: 75%; font-weight: 700; top: 24px; line-height: 1; position: absolute; text-align: center; white-space: nowrap; vertical-align: baseline; color:white; border-radius: 0.25rem; background-color:<?php echo $unread_count > 0 ? 'red' : 'grey'; ?>;">
      <?php echo $badge_text2 > 0 ? $badge_text2 : '0'; ?>
</span>

            
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
                            <a href="listings.php?category_id=' . $row['category_id'] . '">
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
$conn = new mysqli('localhost', 'root', '', 'market');
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
    $sql = "SELECT * FROM products WHERE LOWER(product_name) LIKE LOWER(?) OR LOWER(vendor_name) LIKE LOWER(?)";
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
        $productName = $row['product_name'];
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
                                <img src="uploads/' . ($row['image'] ?? 'default.jpg') . '" alt="' . $productName . '">
                            </div>
                            <div class="product-details">
                                <div class="product-heading">
                                    <h2><a href="product-view.php?product_name=' . urlencode($productName) . '&id=' . urlencode($productId) . '" class="product-link">' . htmlspecialchars($productName) . '</a></h2>
                                    <span class="shadow-text smalltext">In <strong>' . htmlspecialchars($categoryName) . '</strong></span><br>
                                    <span><b>Sold By <a href="#">' . htmlspecialchars($vendorName) . '</a> (<img src="images/icons8-star-48.png" style="height: 13.2px; width:13.2px;" alt="Rating">' . htmlspecialchars($vendorRating) . ')</b><span class="badge badge-pill-level-1" style="display:inline-block;vertical-align: middle;margin-bottom: 1px; margin-left:5px;">Level ' . htmlspecialchars($vendorLevel) . '</span></span>
                                    <div class="sprite sprite--shopping-cart" title="Total Sales" style="float: none; display: inline-flex; margin-left:5px;"><img src="images/shopping-cart.png" style="width:18px;height:18px; margin-top:2px;" alt="Total Sales">' . htmlspecialchars($totalOrders) . '</div>
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
} else {
    //echo '<p>Advanced search queries will be placed at top.</p>';
}
?>
                           

                           
                         

                            <?php
$rows_per_page = 20;

// Calculate total records
$records_query = "SELECT COUNT(*) AS total FROM products";
$records_result = mysqli_query($conn, $records_query);
$total_records_row = mysqli_fetch_assoc($records_result);
$total_records = $total_records_row['total'];

// Calculate total pages
$pages = ceil($total_records / $rows_per_page);

// Initialize current page variable
$current_page = isset($_GET['page']) ? max(1, min($_GET['page'], $pages)) : 1;

// Calculate start index for the current page
$start = 0;

// Query to retrieve products for the current page
$products_query = "SELECT * FROM products LIMIT $start, $rows_per_page";
$result = mysqli_query($conn, $products_query);
?>

                                    <?php
// Function to format price as currency with 2 decimal places
function formatCurrency($price) {
    return number_format($price, 2);
}

// Database connection parameters
$servername = "localhost";
$dbusername = "root";
$password = "";
$dbname = "market";

// Create a new database connection
$conn = new mysqli($servername, $dbusername, $password, $dbname);


// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit();
}

// Function to retrieve products based on parameters
function getProducts($conn, $categoryId = null, $categoryName = null) {
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

// Function to retrieve product category name by ID
function getCategoryName($con, $categoryId) {
    $query = "SELECT name FROM categories WHERE id = '$categoryId'";
    $result = mysqli_query($con, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return $row['name'];
    }
    return "Unknown";
}

// Function to retrieve vendor information by vendor name
function getVendorInfo($con, $vendorName) {
    $query = "SELECT username, vendor_rating, total_orders, level FROM register WHERE username = '$vendorName'";
    $result = mysqli_query($con, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    return false; // Return false if vendor information is not found
}

// Function to retrieve average Bitcoin price in USD from multiple exchanges
function getAverageBitcoinPriceUSD() {
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
function convertToBitcoin($usdPrice) {
    // Get the average Bitcoin price in USD
    $bitcoinPriceUSD = getAverageBitcoinPriceUSD();
    
    // Avoid division by zero errors
    if ($bitcoinPriceUSD === 0) {
        return 0; // Return zero if Bitcoin price in USD is not available or zero
    }

    // Convert USD price to Bitcoin using the average Bitcoin price
    return $usdPrice / $bitcoinPriceUSD;
}

/// Pagination variables
$rows_per_page = 10;
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$start = ($current_page - 1) * $rows_per_page;

// Database connection
$con = mysqli_connect("localhost", "root", "", "market");
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit();
}

// Get category ID from request or default to 0
$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;

// Check if category_id is 0
if ($category_id == 0) {
    // Query to fetch all products
    $products_query = "SELECT * FROM products ORDER BY id LIMIT $start, $rows_per_page";
    $result = mysqli_query($con, $products_query);

    // Query to count total records
    $total_records_query = "SELECT COUNT(*) AS total FROM products";
    $total_records_result = mysqli_query($con, $total_records_query);
    $total_records_row = mysqli_fetch_assoc($total_records_result);
    $total_records = isset($total_records_row['total']) ? $total_records_row['total'] : 0;

    // Calculate total pages
    $total_pages = $total_records > 0 ? ceil($total_records / $rows_per_page) : 1;

    // Display products
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            // Fetching and processing data
            $categoryName = getCategoryName($con, $row['category_id']);
            $vendorName = isset($row['vendor_name']) ? $row['vendor_name'] : "Unknown";

            // Fetch vendor information
            $vendorInfo = getVendorInfo($con, $vendorName);
            if ($vendorInfo) {
                $vendorRating = $vendorInfo['vendor_rating'];
                $totalOrders = $vendorInfo['total_orders'];
                $vendorLevel = $vendorInfo['level'];
            } else {
                $vendorRating = 0;
                $totalOrders = 0;
                $vendorLevel = 0;
            }

            $timesSold = isset($row['times_sold_last_48_hr']) ? $row['times_sold_last_48_hr'] : 0;
            $totalSold = isset($row['total_sold']) ? $row['total_sold'] : 0;
            $shipsFrom = isset($row['ships_from']) ? $row['ships_from'] : "Unknown";
            $shipsTo = isset($row['ships_to']) ? $row['ships_to'] : "Unknown";
            $sellingPrice = isset($row['selling_price']) ? $row['selling_price'] : 0;

            // Convert USD price to Bitcoin
            $bitcoinPrice = convertToBitcoin($sellingPrice);

            // Format the selling price as currency
            $formatted_price = formatCurrency($sellingPrice);

            // Get product name and encode it properly
            $productName = htmlspecialchars($row['product_name']);
            $productUrlName = rawurlencode($productName);
            $productId = htmlspecialchars($row['id']);
            $productId2 = rawurlencode($productId); // Use rawurlencode for URL encoding


            // Display product details
            echo '<div class="product-listing">
                <div class="product-link">
                    <div class="product">
                        <div class="container">
                            <div class="product-photo">
                                <img src="uploads/' . ($row['image'] ?? 'default.jpg') . '" alt="' . $productName . '">
                            </div>
                            <div class="product-details">
                                <div class="product-heading">
                                    <h2><a href="product-view.php?product_name=' . urlencode($productName) . '&id=' . urlencode($productId2) . '" class="product-link">' . htmlspecialchars($productName) . '</a></h2>
                                    <span class="shadow-text smalltext">In <strong>' . htmlspecialchars($categoryName) . '</strong></span><br>
                                    <span><b>Sold By <a href="#">' . htmlspecialchars($vendorName) . '</a> (<img src="images/icons8-star-48.png" style="height: 13.2px; width:13.2px;" alt="Rating">' . htmlspecialchars($vendorRating) . ')</b><span class="badge badge-pill-level-1" style="display:inline-block;vertical-align: middle;margin-bottom: 1px; margin-left:5px;">Level ' . htmlspecialchars($vendorLevel) . '</span></span>
                                    <div class="sprite sprite--shopping-cart" title="Total Sales" style="float: none; display: inline-flex; margin-left:5px;"><img src="images/shopping-cart.png" style="width:18px;height:18px; margin-top:2px;" alt="Total Sales">' . htmlspecialchars($totalOrders) . '</div>
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
    } else {
        echo '<p>No products found.</p>';
    }
} else {
    // Query to fetch products by category ID
    $products_query = "SELECT * FROM products WHERE category_id = $category_id ORDER BY id LIMIT $start, $rows_per_page";
    $result = mysqli_query($con, $products_query);

    // Query to count total records for the category
    $total_records_query = "SELECT COUNT(*) AS total FROM products WHERE category_id = $category_id";
    $total_records_result = mysqli_query($con, $total_records_query);
    $total_records_row = mysqli_fetch_assoc($total_records_result);
    $total_records = isset($total_records_row['total']) ? $total_records_row['total'] : 0;

    // Calculate total pages
    $total_pages = $total_records > 0 ? ceil($total_records / $rows_per_page) : 1;

    // Display products
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            // Fetching and processing data
            $categoryName = getCategoryName($con, $row['category_id']);
            $vendorName = isset($row['vendor_name']) ? $row['vendor_name'] : "Unknown";

            // Fetch vendor information
            $vendorInfo = getVendorInfo($con, $vendorName);
            if ($vendorInfo) {
                $vendorRating = $vendorInfo['vendor_rating'];
                $totalOrders = $vendorInfo['total_orders'];
                $vendorLevel = $vendorInfo['level'];
            } else {
                $vendorRating = 0;
                $totalOrders = 0;
                $vendorLevel = 0;
            }

            $timesSold = isset($row['times_sold_last_48_hr']) ? $row['times_sold_last_48_hr'] : 0;
            $totalSold = isset($row['total_sold']) ? $row['total_sold'] : 0;
            $shipsFrom = isset($row['ships_from']) ? $row['ships_from'] : "Unknown";
            $shipsTo = isset($row['ships_to']) ? $row['ships_to'] : "Unknown";
            $sellingPrice = isset($row['selling_price']) ? $row['selling_price'] : 0;

            // Convert USD price to Bitcoin
            $bitcoinPrice = convertToBitcoin($sellingPrice);

            // Format the selling price as currency
            $formatted_price = formatCurrency($sellingPrice);
               
               
            // Example row data for demonstration

            // Get product name and encode it properly
            $productName = htmlspecialchars($row['product_name']);
            $productUrlName = rawurlencode($productName);
            $productId = htmlspecialchars($row['id']);
            $productId2 = rawurlencode($productId); // Use rawurlencode for URL encoding


            // Display product details
            echo '<div class="product-listing">
                <div class="product-link">
                    <div class="product">
                        <div class="container">
                            <div class="product-photo">
                                <img src="uploads/' . ($row['image'] ?? 'default.jpg') . '" alt="' . $productName . '">
                            </div>
                            <div class="product-details">
                                <div class="product-heading">
                                    <h2><a href="product-view.php?product_name=' . urlencode($productName) . '&id=' . urlencode($productId) . '" class="product-link">' . htmlspecialchars($productName) . '</a></h2>
                                    <span class="shadow-text smalltext">In <strong>' . htmlspecialchars($categoryName) . '</strong></span><br>
                                    <span><b>Sold By <a href="#">' . htmlspecialchars($vendorName) . '</a> (<img src="images/icons8-star-48.png" style="height: 13.2px; width:13.2px;" alt="Rating">' . htmlspecialchars($vendorRating) . ')</b><span class="badge badge-pill-level-1" style="display:inline-block;vertical-align: middle;margin-bottom: 1px; margin-left:5px;">Level ' . htmlspecialchars($vendorLevel) . '</span></span>
                                    <div class="sprite sprite--shopping-cart" title="Total Sales" style="float: none; display: inline-flex; margin-left:5px;"><img src="images/shopping-cart.png" style="width:18px;height:18px; margin-top:2px;" alt="Total Sales">' . htmlspecialchars($totalOrders) . '</div>
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
    } else {
        echo '<p>No products found for this category.</p>';
    }
}

mysqli_close($con);
?>

    </div>
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

<?php

?>


</div>
</div>
</div>
</body>
</html>




<?php 

include('userfunctions.php');


?>
<head>
<link rel="stylesheet" href="style.css" />
  <link rel="stylesheet" href="password-strength-indicator.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <link rel="icon" type="image/jpeg" href="img/pentagram.jpg">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <title>Cpanel</title>
</head>
<body style="width: 100%;">

<!DOCTYPE html>
<html><head>
    <title>Bohemia - Homepage</title>
            <meta http-equiv="Content-type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="stylesheet" type="text/css" href="Bohemia%20-%20Homepage_files/flexboxgrid.min.css">
        <link rel="stylesheet" type="text/css" href="Bohemia%20-%20Homepage_files/fontawesome-all.min.css">
        <link rel="stylesheet" type="text/css" href="Bohemia%20-%20Homepage_files/style.css">
        <link rel="stylesheet" type="text/css" href="Bohemia%20-%20Homepage_files/main.css">
        <link rel="stylesheet" type="text/css" href="Bohemia%20-%20Homepage_files/responsive.css">        
        <link rel="stylesheet" type="text/css" href="Bohemia%20-%20Homepage_files/sprite.css">
        <link rel="stylesheet" href="style.css">
        
</head>

<body>
  
<!DOCTYPE html>
<html>
    <head>
    <title>Asmodeus - Homepage</title>
            <meta http-equiv="Content-type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="stylesheet" type="text/css" href="Bohemia%20-%20Homepage_files/flexboxgrid.min.css">
        <link rel="stylesheet" type="text/css" href="Bohemia%20-%20Homepage_files/fontawesome-all.min.css">
        <link rel="stylesheet" type="text/css" href="Bohemia%20-%20Homepage_files/style.css">
        <link rel="stylesheet" type="text/css" href="Bohemia%20-%20Homepage_files/main.css">
        <link rel="stylesheet" type="text/css" href="Bohemia%20-%20Homepage_files/responsive.css">        
        <link rel="stylesheet" type="text/css" href="Bohemia%20-%20Homepage_files/sprite.css">
        <link rel="stylesheet" href="style.css">
    
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
    <script src="https://kit.fontawesome.com/da20915f29.js" crossorigin="anonymous"></script>
        
</head>

<body>
     <div class="navigation">
        <div class="wrapper">
            <ul>
                <li class="nav-logo"><a href="homepage.php"><img src="Listings_files/logo_small.png" style="height: 100%;"></a></li>
                <div class="responsive-menu">
                    <li class="menu-toggler"><a href="homepage.php">Navigation&nbsp; <div class="sprite sprite--caret" style="float: none; display: inline-block; margin-left:5px;"></div></a></li>
                    <div class="menu-links">
                        <li><a href="homepage.php">Home</a></li>
                        
                        <li class="dropdown-link dropdown-large ">
                            <a href="orders.php?action=orders" class="dropbtn">
                                Orders
                                                            </a>
                            <div class="dropdown-content right-dropdown">
                                <a href="processing.php">Processing&nbsp; <span class="badge badge-secondary right">0</span></a>
                                <a href="dispatched.php">Dispatched&nbsp; <span class="badge badge-secondary right">0</span></a>
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
                        <li class="dropdown-link dropdown-large ">
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
                        <li class="dropdown-link dropdown-large ">
                            <a href="bug-report.php" class="dropbtn">
                                Support
                                                            </a>
                            <div class="dropdown-content right-dropdown">
                                <a href="faq.php">F.A.Q</a>
                                <a href="support-tickets-and-bug-reports.php">
                                    Support Tickets
                                                                    </a>
                                <a href="bug-report.php">
                                    Report Bug
                                </a>
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

// Check if query was successful and if it returned any rows
if ($result) {
    $row = $result->fetch_assoc();
    // Check if $row is not null before accessing order_count
    $order_count = isset($row['order_count']) ? $row['order_count'] : 0;
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
        <?php
// Assuming $conn is your database connection
$current_user = $_SESSION['username'];

// Sanitize user input
$safe_current_user = $conn->real_escape_string($current_user);

// Query to count unread messages for the current user
$sql = "SELECT COUNT(*) AS unread_count FROM messages WHERE ToUser = '$safe_current_user' AND is_read = 0";
$result = $conn->query($sql);

// Initialize variables for badge
$badge_color = 'grey'; // Default color
$badge_text = '0';     // Default text
$badge_class = '';     // Default class

if ($result) {
    $row = $result->fetch_assoc();
    $unread_count = $row['unread_count'];
    
    if ($unread_count > 0) {
        $badge_color = 'red';   // Set badge color to red if there are unread messages
        $badge_text = $unread_count; // Display the number of unread messages
        $badge_class = 'badge-danger';
    } else {
        $badge_color = 'grey';  // Set badge color to grey if no unread messages
        $badge_class = '';      // Remove badge class
    }
} else {
    // Handle query error
    echo "Error fetching unread messages count: " . $conn->error;
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
        &nbsp;<span class="badge <?php echo $badge_class; ?>" style="
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
        background-color: <?php echo $badge_color; ?>;">
        <?php echo htmlspecialchars($totalItemCount); ?>
        </span>               
    </a>
</li>
<li class="right shopping-cart-link">
    <a href="messages.php">
        <img src="alert-bell.png" style="width: 20px; height: 25px; display: inline-block; margin-top: 20px; float: none;">
        &nbsp;<span class="badge <?php echo $badge_class; ?>" style="
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
        background-color: <?php echo $badge_color; ?>;">
        <?php echo htmlspecialchars($badge_text); ?>
        </span>
    </a>
</li>
                <li class="dropdown-link dropdown-large " style="margin-left:260px; position:absolute; width:210px; margin-top:-15px;">
                            <a href="control-panel.php" class="dropbtn">
                               <p>C Panel</p>
                                                            </a>
                            <div class="dropdown-content right-dropdown">
                            <a href="products.php">Products</a>
                                <a href="category.php">
                                    All Categories
                                                                    </a>
                                                                    <a href="add-category.php">
                                    Add Category
                                                                    </a>
                                <a href="add-product.php">
                                    Add Products
                                                                    </a>
                            <a href="category.php">
                                List Of Categories
                                                                    </a>
                                                                    <a href="categories.php">
                                View Categories
                                                                    </a>
                            <a href="add-category.php">
                                Categories
                                                                    </a>
                        <a href="edit-category.php">
                                Edit Category
                                                                    </a>
                    </div>
                </div>
              
                
                <li class="right fix-gap" style="list-style:none;"><a href="become-a-merchant.php"><b>Become A Merchant</b></a></li>
                
        </div>
    </div>
<div class="py-3 bg-primary" style="color: #fff;
    background-color:black;">
    <div class="container">
        <h6 class="text-white"> Home / Categories</h6>
    </div>
</div>

<div class="py-5" style="">
    <div class="container" style="height:150%;">
        <div class="row">
            <div class="col-md-12" style="">
                <h3>Control Panel</h3>
                <hr>
                <div class="row" style="display:inline-block;">
                    <?php 
                      
                                ?>
                                    <div class="col-md-3 mb-2">
                                        <a href="categories.php">
                                            <div class="card shadow">
                                                <div class="card-body">
                                                    <img src="images/search-512.jpg" alt="Category Image" class="w-100" width="30%" height="10%">
                                                    <h4>Search By Category Name </h4>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                <?php
                        
                        
                    
                    ?>
                     <div class="col-md-3 mb-2">
                                        <a href="products.php">
                                            <div class="card shadow">
                                                <div class="card-body">
                                                    <img src="images/HP250G9Notebook.jpg" alt="Category Image" class="w-100" width="30%" height="10%">
                                                    <h4>View/Edit/Del All Products </h4>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                       <div class="col-md-3 mb-2">
                                        <a href="category.php">
                                            <div class="card shadow">
                                                <div class="card-body">
                                                    <img src="images/money-fraud-service.jpg" alt="Category Image" class="w-100" width="30%" height="10%">
                                                    <h4>View/Edit/Del All Categories</h4>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                       <div class="col-md-3 mb-2">
                                        <a href="add-category.php">
                                            <div class="card shadow">
                                                <div class="card-body">
                                                    <img src="images/add-product-btn.png" alt="Category Image" class="w-100" width="30%" height="10%">
                                                    <h4>Add Category </h4>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                       <div class="col-md-3 mb-2">
                                        <a href="add-product.php">
                                            <div class="card shadow">
                                                <div class="card-body">
                                                    <img src="images/add-btn.jpg" alt="Category Image" class="w-100" width="30%" height="10%">
                                                    <h4>Add Products </h4>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                       <div class="col-md-3 mb-2">
                                        <a href="category.php">
                                            <div class="card shadow">
                                                <div class="card-body">
                                                    <img src="images/search-512.jpg" alt="Category Image" class="w-100" width="30%" height="10%">
                                                    <h4>View/Edit/Del List Of Categories </h4>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                        <div class="col-md-3 mb-2">
                                        <a href="orders.php">
                                            <div class="card shadow">
                                                <div class="card-body">
                                                    <img src="images/invoice.png" alt="Category Image" class="w-100" width="30%" height="10%">
                                                    <h4>Orders</h4>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                       <div class="col-md-3 mb-2">
                                        <a href="messages.php">
                                            <div class="card shadow">
                                                <div class="card-body">
                                                    <img src="images/email.jpg" alt="Category Image" class="w-100" width="30%" height="10%">
                                                    <h4>Messages</h4>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    
                                        
                                       <div class="col-md-3 mb-2">
                                        <a href="listings.php">
                                            <div class="card shadow">
                                                <div class="card-body">
                                                    <img src="images/other-drugs.jpg" alt="Category Image" class="w-100" width="30%" height="10%">
                                                    <h4>View All Product Listings </h4>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                       <div class="col-md-3 mb-2">
                                        <a href="wallet.php">
                                            <div class="card shadow">
                                                <div class="card-body">
                                                    <img src="images/btc-wallet.png" alt="Category Image" class="w-100" width="30%" height="10%">
                                                    <h4>Wallet</h4>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                       <div class="col-md-3 mb-2">
                                        <a href="bug-report.php">
                                            <div class="card shadow">
                                                <div class="card-body">
                                                    <img src="images/tech-support.png" alt="Category Image" class="w-100" width="30%" height="10%">
                                                    <h4>Support </h4>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                      
                </div>

            </div>
        </div>
    </div>
</div>
                    </div>
                    </body>
                    </html>


   
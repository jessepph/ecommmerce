
<?php
include("myfunctions.php");
include('alertify.php');
include('db.php'); // Database connection code

// Start the session
session_start();

// Ensure user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Retrieve the user's role from the database (only once)
$user_role = 'Buyer'; // Default value
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

// Check user role and conditionally show Control Panel
$showControlPanel = ($user_role !== 'Buyer');

// Get the category_id from the URL (if set)
$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;

// Function to get category name by category ID
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

// Query to get cart items for the user
$query = "SELECT product_id, name, quantity, total_price FROM cart WHERE username = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("s", $current_user); // Use "s" for string binding
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

// Handle product update on POST request
if (isset($_POST['update_product_btn'])) {
    // Escape all incoming POST data for security
    $product_id = mysqli_real_escape_string($conn, $_POST['id']);
    $category_id = mysqli_real_escape_string($conn, $_POST['category_id']);
    $category_name = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['product_name']));
    $vendor_name = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['vendor_name']));
    $shipping_price = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['shipping_price']));
    $slug = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['slug']));
    $product_description = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['product_description']));
    $termsRefundPolicy = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['terms_refund_policy']));
    $original_price = mysqli_real_escape_string($conn, $_POST['original_price']);
    $selling_price = mysqli_real_escape_string($conn, $_POST['selling_price']);
    $qty = mysqli_real_escape_string($conn, $_POST['qty']);
    $meta_title = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['meta_title']));
    $meta_keywords = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['meta_keywords']));
    $status = isset($_POST['status']) ? 1 : 0;
    $trending = isset($_POST['trending']) ? 1 : 0;

    // Handle image upload
    if (!empty($_FILES['image']['name'])) {
        $image = mysqli_real_escape_string($conn, $_FILES['image']['name']);
        $image_ext = pathinfo($image, PATHINFO_EXTENSION);
        $update_filename = time() . '.' . $image_ext;
        $image_uploaded = true;
    } else {
        $update_filename = mysqli_real_escape_string($conn, $_POST['old_image']);
        $image_uploaded = false;
    }

    // Prepare the SQL statement
    $update_product_query = "UPDATE products SET 
        category_id='$category_id', 
        name='$category_name', 
        vendor_name='$vendor_name',
        shipping_price='$shipping_price',
        slug='$slug', 
        product_description='$product_description', 
        terms_refund_policy='$termsRefundPolicy', 
        original_price='$original_price', 
        selling_price='$selling_price', 
        qty='$qty', 
        meta_title='$meta_title', 
        meta_keywords='$meta_keywords', 
        status='$status', 
        trending='$trending'";

    if ($image_uploaded) {
        $update_product_query .= ", image='$update_filename'";
    }

    $update_product_query .= " WHERE id='$product_id'";

    // Execute the query
    $update_product_query_run = mysqli_query($conn, $update_product_query);

    // Check if execution was successful
    if ($update_product_query_run) {
        // Handle file upload if a new image was provided
        if ($image_uploaded) {
            move_uploaded_file($_FILES['image']['tmp_name'], "uploads/" . $update_filename);
            if (!empty($_POST['old_image']) && file_exists("uploads/" . $_POST['old_image'])) {
                unlink("uploads/" . $_POST['old_image']);
            }
        }

        // Successful update message using JavaScript
        ?>
        <script>
            $(document).ready(function () {
                alertify.set('notifier', 'position', 'top-right');
                alertify.success('Product Updated Successfully');
            });
        </script>
        <?php
    } else {
        // Handle update failure
        ?>
        <script>
            $(document).ready(function () {
                alertify.set('notifier', 'position', 'top-right');
                alertify.error('Error updating product');
            });
        </script>
        <?php
    }
}




?>
<?php
//session_start();
//include("myfunctions.php");
include("db.php"); // Make sure this file initializes $con
include('alertify.php');

// Database connection parameters
$servername = "localhost";
$dbusername = "root";
$password = "";
$dbname = "market";
//$port = 888;

// Create a new database connection
$conn = new mysqli($servername, $dbusername, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<!DOCTYPE html>
<html>
<head>
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
    <link rel="icon" type="image/jpeg" href="img/pentagram.jpg">
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.innerWidth <= 390) {
                window.location.href = 'product-view-mobile.php';
            }
        });
    </script>
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

// Check if the query was successful and if it returned any rows
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
    <?php
// Database connection parameters
$servername = "localhost";
$dbusername = "root";
$password = "";
$dbname = "market";



// Create a new database connection
$conn = new mysqli($servername, $dbusername, $password, $dbname);
if (isset($_GET['id'])) {
    // Safely fetch the ID from GET parameters
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    // Write the query directly here
    $sql = "SELECT * FROM products WHERE id = '$id' LIMIT 1";
    $product = mysqli_query($conn, $sql);

    if ($product && mysqli_num_rows($product) > 0) {
        $data = mysqli_fetch_array($product);
        // ... (your code to display the product data)
    } else {
        echo "No product found.";
    }
} else {
    echo "Invalid ID parameter.";
}
?>
<div class="container" style="top: 2%; width: 100%; height: 410%; border-radius: 5px; box-shadow: 0 0 15px rgb(0 0 0 / 20%);">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <?php 
                if (isset($_GET['id'])) {
                    $id = mysqli_real_escape_string($conn, $_GET['id']);
                    $product = getByID("products", $id);

                    if ($product && mysqli_num_rows($product) > 0) {
                        $data = mysqli_fetch_array($product);
                ?>
                <div class="card">
                    <div class="card-header bg-primary">
                        <h4 class="text-white">Edit Product
                            <a href="products.php" class="btn btn-primary float-end">Back</a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="mb-0">Select Category</label>
                                    <select name="category_id" class="form-select mb-2">
                                        <option value="" disabled selected>Select Category</option>
                                        <?php
                                        // Fetch all categories from the database
                                        $categories = getAll("categories");

                                        if ($categories && mysqli_num_rows($categories) > 0) {
                                            while ($item = mysqli_fetch_assoc($categories)) {
                                                // Set selected attribute based on current product's category_id
                                                echo '<option value="' . $item['id'] . '" ' . ($data['category_id'] == $item['id'] ? 'selected' : '') . '>' . $item['name'] . '</option>';
                                            }
                                        } else {
                                            echo "<option>No category available</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <input type="hidden" name="id" value="<?= $data['id']; ?>">
                                <div class="col-md-6">
                                    <label class="mb-0">Product Name</label>
                                    <input type="text" name="product_name" value="<?= $data['product_name']; ?>" placeholder="Enter Product Name" class="form-control mb-2" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="">Sold By</label>
                                    <input type="text" name="vendor_name" value="<?= $data['vendor_name'] ?>" placeholder="Enter vendor name" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="mb-0">Slug</label>
                                    <input type="text" name="slug" value="<?= $data['slug']; ?>" placeholder="Enter slug" class="form-control mb-2" required>
                                </div>
                                <div class="col-md-12">
                                    <label class="mb-0">Product Description</label>
                                    <textarea rows="3" name="product_description" placeholder="Enter product description" class="form-control mb-2" required><?= $data['product_description']; ?></textarea>
                                </div>
                                <div class="col-md-12">
                                    <label class="mb-0">Terms & Refund Policy</label>
                                    <textarea rows="3" name="terms_refund_policy" placeholder="Enter terms and refund policy" class="form-control mb-2" required><?= $data['terms_refund_policy']; ?></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="mb-0">Original Price</label>
                                    <input type="text" name="original_price" value="<?= $data['original_price']; ?>" placeholder="Enter Original Price" class="form-control mb-2" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="mb-0">Selling Price</label>
                                    <input type="text" name="selling_price" value="<?= $data['selling_price']; ?>" placeholder="Selling Price" class="form-control mb-2" required>
                                </div>
                                <div class="col-md-12">
                                    <label class="mb-0">Upload Image</label>
                                    <input type="hidden" name="old_image" value="<?= $data['image']; ?>">
                                    <input type="file" name="image" class="form-control mb-2">
                                    <label class="mb-0">Current Image</label>
                                    <img src="uploads/<?= $data['image']; ?>" alt="Product Image" height="50px" width="50px" style="width:50%;height:30%;">
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="mb-0">Quantity</label>
                                        <input type="number" name="qty" value="<?= $data['qty']; ?>" placeholder="Enter Quantity" class="form-control mb-2" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="mb-0">Status</label> <br>
                                        <input type="checkbox" name="status" <?= $data['status'] ? 'checked' : '' ?>>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="mb-0">Trending</label> <br>
                                        <input type="checkbox" name="trending" <?= $data['trending'] ? 'checked' : '' ?>>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="mb-0">Shipping Price</label>
                                    <input type="text" name="shipping_price" placeholder="Enter Shipping Price" class="form-control mb-2" required>
                                </div>
                                  <div class="col-md-6">
                                    <label class="mb-0">Shipping Method</label>
                                    <input type="text" name="shipping_method" placeholder="Enter Shipping Method" class="form-control mb-2" required>
                                </div>
                                <div class="col-md-12">
                                    <label class="mb-0">Meta Title</label>
                                    <input type="text" name="meta_title" value="<?= $data['meta_title']; ?>" placeholder="Enter meta title" class="form-control mb-2" required>
                                </div>
                                <div class="col-md-12">
                                    <label class="mb-0">Meta Keywords</label>
                                    <textarea rows="3" name="meta_keywords" placeholder="Enter meta keywords" class="form-control mb-2" required><?= $data['meta_keywords']; ?></textarea>
                                </div>
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary" name="update_product_btn">Update</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <?php 
                    } else {
                        echo "Product Not found for given id";
                    }
                } else {
                    echo "Id missing from URL";
                }
                ?>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        alertify.set('notifier', 'position', 'top-right');
    });
</script>

</body>
</html>

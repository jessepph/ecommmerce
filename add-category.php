<?php
include('alertify.php');
session_start();
require_once("db.php");

if(isset($_POST['add_category_btn'])) {
    // Sanitize input data
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $slug = mysqli_real_escape_string($con, $_POST['slug']);
    $description = mysqli_real_escape_string($con, $_POST['description']);
    $meta_title = mysqli_real_escape_string($con, $_POST['meta_title']);
    $meta_description = mysqli_real_escape_string($con, $_POST['meta_description']);
    $meta_keywords = mysqli_real_escape_string($con, $_POST['meta_keywords']);
    $status = isset($_POST['status']) ? 1 : 0;
    $popular = isset($_POST['popular']) ? 1 : 0;

    // File upload handling
    $path = "uploads/";
    $image = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];

    $image_ext = pathinfo($image, PATHINFO_EXTENSION);
    $filename = time() . '.' . $image_ext;

    // Move uploaded file to destination
    move_uploaded_file($image_tmp, $path . $filename);

    // Prepare and bind SQL statement using prepared statement
    $stmt = $con->prepare("INSERT INTO categories (name, slug, description, meta_title, meta_description, meta_keywords, status, popular, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssds", $name, $slug, $description, $meta_title, $meta_description, $meta_keywords, $status, $popular, $filename);

    // Execute statement
    if ($stmt->execute()) {
        ?>
        <script>
            $(document).ready(function () {
                alertify.set('notifier', 'position', 'top-right');
                alertify.success('Category Added Successfully');
            });
        </script>
        <?php
    } else {
        ?>
        <script>
            $(document).ready(function () {
                alertify.set('notifier', 'position', 'top-right');
                alertify.error('Something Went Wrong');
            });
        </script>
        <?php
    }

    // Close statement and connection
    $stmt->close();
    $con->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bohemia - Add Category</title>
    <link rel="stylesheet" type="text/css" href="Bohemia%20-%20Homepage_files/flexboxgrid.min.css">
    <link rel="stylesheet" type="text/css" href="Bohemia%20-%20Homepage_files/fontawesome-all.min.css">
    <link rel="stylesheet" type="text/css" href="Bohemia%20-%20Homepage_files/style.css">
    <link rel="stylesheet" type="text/css" href="Bohemia%20-%20Homepage_files/main.css">
    <link rel="stylesheet" type="text/css" href="Bohemia%20-%20Homepage_files/responsive.css">
    <link rel="stylesheet" type="text/css" href="Bohemia%20-%20Homepage_files/sprite.css">
    <link rel="icon" type="image/jpeg" href="img/pentagram.jpg">
    <link rel="stylesheet" href="day.css">
    
    <!-- jQuery and Alertify.js -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="alertify.min.js"></script>
    <link rel="stylesheet" href="alertify.min.css"/>
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
<header></header>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary">
                    <h4 class="text-white" style="text-align:center; font-size:larger;">Add Category</h4>
                </div>
                <div class="card-body">
                    <form action="" autocomplete="off" method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="">Name</label>
                                <input type="text" autocomplete="off" required name="name" placeholder="Enter Category Name" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="">Slug</label>
                                <input type="text" autocomplete="off" required name="slug" placeholder="Enter slug" class="form-control">
                            </div>
                            <div class="col-md-12">
                                <label for="">Description</label>
                                <textarea rows="3" autocomplete="off" required name="description" placeholder="Enter description" class="form-control"></textarea>
                            </div>
                            <div class="col-md-12">
                                <label for="">Upload Image</label>
                                <input type="file" autocomplete="off" required name="image" class="form-control">
                            </div>
                            <div class="col-md-12">
                                <label for="">Meta Title</label>
                                <input type="text" autocomplete="off" required name="meta_title" placeholder="Enter meta title" class="form-control">
                            </div>
                            <div class="col-md-12">
                                <label for="">Meta Description</label>
                                <textarea rows="3" autocomplete="off" required name="meta_description" placeholder="Enter meta description" class="form-control"></textarea>
                            </div>
                            <div class="col-md-12">
                                <label for="">Meta Keywords</label>
                                <textarea rows="3" autocomplete="off" required name="meta_keywords" placeholder="Enter meta keywords" class="form-control"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="">Status</label>
                                <input type="checkbox" autocomplete="offt" name="status">
                            </div>
                            <div class="col-md-6">
                                <label for="">Popular</label>
                                <input type="checkbox" autocomplete="false" name="popular">
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary" name="add_category_btn">Save</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>

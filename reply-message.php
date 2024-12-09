<?php
session_start();
require('db.php');

// Ensure user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$loggedInUser = $_SESSION['username'];
$toUser = isset($_GET['to']) ? htmlspecialchars($_GET['to']) : '';
$subject = isset($_GET['subject']) ? htmlspecialchars($_GET['subject']) : '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    // Create database connection
    $conn = new mysqli("localhost", "root", "", "market");

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get user IDs for the logged-in user and the recipient
    $stmt = $conn->prepare("SELECT id FROM register WHERE username = ?");
    $stmt->bind_param("s", $loggedInUser);
    $stmt->execute();
    $stmt->bind_result($loggedInUserId);
    $stmt->fetch();
    $stmt->close();

    $stmt = $conn->prepare("SELECT id FROM register WHERE username = ?");
    $stmt->bind_param("s", $toUser);
    $stmt->execute();
    $stmt->bind_result($toUserId);
    $stmt->fetch();
    $stmt->close();

    if (!$loggedInUserId || !$toUserId) {
        die("Error: User IDs not found.");
    }

    // Insert the message into the messages table
    $sql = "INSERT INTO messages (FromUser, ToUser, subject, message, receive_date) VALUES (?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Prepare failed: " . htmlspecialchars($conn->error));
    }
    $stmt->bind_param("ssss", $loggedInUser, $toUser, $subject, $message);
    if (!$stmt->execute()) {
        die("Execute failed: " . htmlspecialchars($stmt->error));
    }
    $stmt->close();

    // Insert the message into the pm_inbox table
    $sql_inbox = "INSERT INTO pm_inbox (userid, username, from_id, from_username, title, content, recieve_date) VALUES (?, ?, ?, ?, ?, ?, NOW())";
    $stmt_inbox = $conn->prepare($sql_inbox);
    if ($stmt_inbox === false) {
        die("Prepare failed: " . htmlspecialchars($conn->error));
    }
    $stmt_inbox->bind_param("iissss", $toUserId, $toUser, $loggedInUserId, $loggedInUser, $subject, $message);
    if (!$stmt_inbox->execute()) {
        die("Execute failed: " . htmlspecialchars($stmt_inbox->error));
    }
    $stmt_inbox->close();

    // Close the connection
    $conn->close();

    // Redirect to the inbox
    header("Location: pm_inbox.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reply to Message</title>
    <link rel="stylesheet" href="Listings_files/style.css">
    <link rel="stylesheet" href="Listings_files/main.css">
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
    <style>
        .container {
            padding: 20px;
            margin: 0 auto;
            max-width: 1200px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
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
 
  
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $do_action = $_POST['do_action'];
    $message_ids = $_POST['message_ids'] ?? [];

    if ($do_action && !empty($message_ids)) {
        foreach ($message_ids as $id) {
            if ($do_action === 'markread') {
                // Mark the message as read
                $update_query = "UPDATE messages SET is_read = 1 WHERE id = ?";
                
                // Update statement
                $update_stmt = $conn->prepare($update_query);
                $update_stmt->bind_param("i", $id);
                $update_stmt->execute();
                
                // Decrement unread count
                $decrement_query = "UPDATE messages SET unread_count = unread_count - 1 WHERE id = ?";
                $decrement_stmt = $conn->prepare($decrement_query);
                $decrement_stmt->bind_param("i", $id); // Use $id to bind the correct message ID
                $decrement_stmt->execute();
            }
            // Handle other actions such as 'markunread' and 'delete' if necessary
        }
        //echo "<p>Action completed successfully!</p>";
    }
}

// This part runs when a user views a message
if (isset($_GET['view_id'])) {
    $view_id = $_GET['view_id'];

    // Increment unread count
    $increment_query = "UPDATE messages SET unread_count = unread_count + 1 WHERE id = ?";
    $increment_stmt = $conn->prepare($increment_query);
    $increment_stmt->bind_param("i", $view_id);
    $increment_stmt->execute();
}

// Query to count unread messages for the current user
$current_user = $_SESSION['username'];
$safe_current_user = $conn->real_escape_string($current_user);

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
        $badge_text = '0';      // Show '0' when there are no unread messages
        $badge_class = '';
    }
} else {
    echo "Error fetching unread messages count: " . $conn->error;
}
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $do_action = $_POST['do_action'];
    $message_ids = $_POST['message_ids'] ?? [];

    if ($do_action && !empty($message_ids)) {
        foreach ($message_ids as $id) {
            if ($do_action === 'markread') {
                // Mark the message as read
                $update_query = "UPDATE messages SET is_read = 1 WHERE id = ?";
                
                // Decrement unread count
                $update_stmt = $conn->prepare($update_query);
                $update_stmt->bind_param("i", $id);
                $update_stmt->execute();
                
                // After marking as read, decrement the unread count
                $decrement_query = "UPDATE messages SET unread_count = unread_count - 1 WHERE id = ?";
                $decrement_stmt = $conn->prepare($decrement_query);
                $decrement_stmt->bind_param("s", $username);
                $decrement_stmt->execute();
            }
            // Additional actions for 'markunread' and 'delete' can be handled similarly
        }
        //echo "<p>Action completed successfully!</p>";
    }
}

// Decrement unread count
$decrement_query = "UPDATE messages SET unread_count = unread_count - 1 WHERE id = ?";
$decrement_stmt = $conn->prepare($decrement_query);

if (!$decrement_stmt) {
    die("Prepare failed: " . $conn->error);
}

$decrement_stmt->bind_param("s", $username);

if (!$decrement_stmt->execute()) {
    die("Execute failed: " . $decrement_stmt->error);
}


// Query to count unread messages for the current user
$sql = "SELECT unread_count FROM messages WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $safe_current_user);
$stmt->execute();
$result = $stmt->get_result();
// Execute your query here (example: $result = $conn->query($sql);)

// Check if the query was successful and if it returned any rows
if ($result) {
    // Check if there's at least one row
    if ($row = $result->fetch_assoc()) {
        $unread_count = $row['unread_count'];

        // Update badge color and text based on the unread count
        if ($unread_count > 0) {
            $badge_color = 'red';
            $badge_text = $unread_count;
            $badge_class = 'badge-danger';
        } else {
            $badge_color = 'grey';
            $badge_text = '0';
            $badge_class = '';
        }
    } else {
        // No rows returned, set defaults
        $unread_count = 0;
        $badge_color = 'grey';
        $badge_text = '0';
        $badge_class = '';
    }
} else {
    echo "Error fetching unread messages count: " . $conn->error;
}

        
        
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
                        <li class="active dropdown-link dropdown-large ">
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


$cart_badge_color = $totalItemCount > 0 ? 'red' : 'grey'; // Set cart badge color
$cart_badge_text = $totalItemCount > 0 ? $totalItemCount : '0'; // Set cart badge text
$cart_badge_class = $totalItemCount > 0 ? 'badge-danger' : ''; // Set cart badge class
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

// Check if the query was successful and contains at least one row
if ($result) {
    // Fetch the result
    if ($row = $result->fetch_assoc()) {
        $order_count = $row['order_count'];
    } else {
        // No rows returned, set default order count
        $order_count = 0;
    }
} else {
    // Handle query error
    echo "Error fetching order count: " . $conn->error;
    $order_count = 0; // Default to 0 on error
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

// Assuming $conn is your database connection
$current_user = $_SESSION['username'];

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
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $do_action = $_POST['do_action'];
    $message_ids = $_POST['message_ids'] ?? [];

    if ($do_action && !empty($message_ids)) {
        foreach ($message_ids as $id) {
            if ($do_action === 'markread') {
                // Mark the message as read
                $update_query = "UPDATE messages SET is_read = 1 WHERE id = ?";
                
                // Update statement
                $update_stmt = $conn->prepare($update_query);
                $update_stmt->bind_param("i", $id);
                $update_stmt->execute();
                
                // Decrement unread count
                $decrement_query = "UPDATE messages SET unread_count = unread_count - 1 WHERE id = ?";
                $decrement_stmt = $conn->prepare($decrement_query);
                $decrement_stmt->bind_param("i", $id); // Use $id to bind the correct message ID
                $decrement_stmt->execute();
            }
            // Handle other actions such as 'markunread' and 'delete' if necessary
        }
        //echo "<p>Action completed successfully!</p>";
    }
}

// This part runs when a user views a message
if (isset($_GET['view_id'])) {
    $view_id = $_GET['view_id'];

    // Increment unread count
    $increment_query = "UPDATE messages SET unread_count = unread_count + 1 WHERE id = ?";
    $increment_stmt = $conn->prepare($increment_query);
    $increment_stmt->bind_param("i", $view_id);
    $increment_stmt->execute();
}

// Query to count unread messages for the current user
$current_user = $_SESSION['username'];
$safe_current_user = $conn->real_escape_string($current_user);

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
        $badge_text = '0';      // Show '0' when there are no unread messages
        $badge_class = '';
    }
} else {
    echo "Error fetching unread messages count: " . $conn->error;
}
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $do_action = $_POST['do_action'];
    $message_ids = $_POST['message_ids'] ?? [];

    if ($do_action && !empty($message_ids)) {
        foreach ($message_ids as $id) {
            if ($do_action === 'markread') {
                // Mark the message as read
                $update_query = "UPDATE messages SET is_read = 1 WHERE id = ?";
                
                // Decrement unread count
                $update_stmt = $conn->prepare($update_query);
                $update_stmt->bind_param("i", $id);
                $update_stmt->execute();
                
                // After marking as read, decrement the unread count
                $decrement_query = "UPDATE messages SET unread_count = unread_count - 1 WHERE id = ?";
                $decrement_stmt = $conn->prepare($decrement_query);
                $decrement_stmt->bind_param("s", $username);
                $decrement_stmt->execute();
            }
            // Additional actions for 'markunread' and 'delete' can be handled similarly
        }
        //echo "<p>Action completed successfully!</p>";
    }
}

// Decrement unread count
$decrement_query = "UPDATE messages SET unread_count = unread_count - 1 WHERE id = ?";
$decrement_stmt = $conn->prepare($decrement_query);

if (!$decrement_stmt) {
    die("Prepare failed: " . $conn->error);
}

$decrement_stmt->bind_param("s", $username);

if (!$decrement_stmt->execute()) {
    die("Execute failed: " . $decrement_stmt->error);
}

// Query to count unread messages for the current user
$sql = "SELECT unread_count FROM messages WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $safe_current_user);
$stmt->execute();
$result = $stmt->get_result();

if ($result) {
    // Check if there's at least one row
    if ($row = $result->fetch_assoc()) {
        $unread_count = $row['unread_count'];
        
        // Update badge color and text based on the unread count
        if ($unread_count > 0) {
            $badge_color = 'red';
            $badge_text = $unread_count;
            $badge_class = 'badge-danger';
        } else {
            $badge_color = 'grey';
            $badge_text = '0';
            $badge_class = '';
        }
    } else {
        // No rows returned, set defaults
        $unread_count = 0;
        $badge_color = 'grey';
        $badge_text = '0';
        $badge_class = '';
    }
} else {
    // Handle query error
    echo "Error fetching unread messages count: " . $stmt->error;
}
        
        
        
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
        <img src="cart.png" style="width: 20px; height: 25px; display: inline-block; margin-top: 20px; float: none;">
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
    
    <div class="container">
        <h2>Reply to Message</h2>
        <form method="post" action="">
            <div class="form-group">
                <label for="ToUser">To:</label>
                <input type="text" id="ToUser" name="ToUser" value="<?php echo htmlspecialchars($toUser); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="subject">Subject:</label>
                <input type="text" id="subject" name="subject" value="<?php echo htmlspecialchars($subject); ?>" required>
            </div>
            <div class="form-group">
                <label for="message">Message:</label>
                <textarea id="message" name="message" rows="10" required></textarea>
            </div>
            <input type="submit" value="Send Reply">
        </form>
    </div>
</body>
</html>

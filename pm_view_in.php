<?php
// session_start(); // Uncomment if session is used in this script
include('connect_i.php');

// Database connection parameters
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'market';

// Establish the database connection
$connect = mysqli_connect($host, $user, $password, $database);
if (!$connect) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Initialize variables with default values
$pid = $username = $message_id = $fromUser = $toUser = $subject = $message = $receive_date = $viewed = '';

// Initialize the control panel visibility variable
$showControlPanel = false;

// Fetch the message ID from the URL
$message_id = isset($_GET['id']) ? mysqli_real_escape_string($connect, $_GET['id']) : '';

// Ensure session username is set
if (!isset($_SESSION['username'])) {
    die("User is not logged in.");
}

// Fetch user details
$query = "SELECT username FROM register WHERE username = '" . mysqli_real_escape_string($connect, $_SESSION['username']) . "'";
$result = mysqli_query($connect, $query);

if ($result && $row = mysqli_fetch_assoc($result)) {
    $username = $row['username'];
}

// Fetch message details
$query = "SELECT FromUser, ToUser, subject, message, receive_date, viewed FROM messages WHERE id = '" . mysqli_real_escape_string($connect, $message_id) . "'";
$result = mysqli_query($connect, $query);

if ($result && $row = mysqli_fetch_assoc($result)) {
    $fromUser = $row['FromUser'];
    $toUser = $row['ToUser'];
    $subject = $row['subject'];
    $message = $row['message'];
    $receive_date = $row['receive_date'];
    $viewed = $row['viewed'];
}

// Mark message as viewed if not already viewed
if ($viewed == '0') {
    $update_query = "UPDATE messages SET viewed = '1' WHERE id = '" . mysqli_real_escape_string($connect, $message_id) . "'";
    mysqli_query($connect, $update_query);
}

// Check if the user has access to the control panel (e.g., based on user role)
/*$query = "SELECT role FROM register WHERE username = '" . mysqli_real_escape_string($connect, $_SESSION['username']) . "'";
$result = mysqli_query($connect, $query);

if ($result && $row = mysqli_fetch_assoc($result)) {
    $user_role = $row['role'];
    if ($user_role == 'admin') { // or any other condition to show the control panel
        $showControlPanel = true;
    }
}*/

// Close the database connection
mysqli_close($connect);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Message Details</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="icon" type="image/jpeg" href="img/pentagram.jpg">
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <style>
        table {
            width: 100%;
            margin: 20px auto;
            border-collapse: collapse;
        }
        td, th {
            padding: 10px;
            border: 1px solid #ddd;
        }
        textarea {
            width: 100%;
            height: 118px;
            resize: none;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
        }
        .message-actions a {
            margin-right: 10px;
            color: #007bff;
            text-decoration: none;
        }
        .message-actions a.reply {
            color: white;
        }
        .message-actions a.delete {
            color: #dc3545;
        }
    </style>
</head>
<body>

<body>
<div class="navigation">
    <div class="wrapper">
        <ul>
            <li class="nav-logo"><a href="homepage.php"><img src="Listings_files/logo_small.png" style="height: 100%;"></a></li>
            <div class="responsive-menu">
                <li class="menu-toggler"><a href="homepage.php">Navigation&nbsp; <div class="sprite sprite--caret" style="float: none; display: inline-block; margin-left:5px;"></div></a></li>
                <div class="menu-links">
                    <li class="active"><a href="homepage.php">Home</a></li>
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
                    <li class=""><a href="listings.php">Listings</a></li>
                    <li class="dropdown-link dropdown-large">
                        <a href="messages.php" class="dropbtn">
                            Messages&nbsp;
                            <span class="badge badge-secondary">0</span>
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

            <li class="right shopping-cart-link">
                <a href="cart.php">
                    <img src="cart.png" style="width: 20px; height: 25px; display: inline-block; margin-top: 20px; float:none;">
                    &nbsp;<span class="badge badge-danger" style="padding: 0.3em 0.4em; font-size: 75%; font-weight: 700; top: 24px; line-height: 1; position: absolute; text-align: center; white-space: nowrap; vertical-align: baseline; border-radius: 0.25rem; background-color:grey;">0</span>
                </a>
            </li>
            <li class="right shopping-cart-link">
                <a href="cart.php">
                    <img src="alert-bell.png" style="width: 20px; height: 25px; display: inline-block; margin-top: 20px; float:none;">
                    &nbsp;<span class="badge badge-danger" style="padding: 0.3em 0.4em; font-size: 75%; font-weight: 700; top: 24px; line-height: 1; position: absolute; text-align: center; white-space: nowrap; vertical-align: baseline; border-radius: 0.25rem; background-color:grey;">0</span>
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
    <div class="container">
        <h3>Message Details</h3>
        <p>You are currently logged in as <?= htmlspecialchars($username); ?></p>
        <p><a href="pm_inbox.php">Back to Inbox</a> | <a href="pm_send.php">Send New Message</a> | <a href="outlog.php">Log Out</a></p>

        <table>
            <tr>
                <td><strong>Subject:</strong></td>
                <td><?= htmlspecialchars($subject); ?></td>
            </tr>
            <tr>
                <td><strong>From:</strong></td>
                <td><?= htmlspecialchars($fromUser); ?></td>
            </tr>
            <tr>
                <td><strong>To:</strong></td>
                <td><?= htmlspecialchars($toUser); ?></td>
            </tr>
            <tr>
                <td><strong>Date Received:</strong></td>
                <td><?= htmlspecialchars($receive_date); ?></td>
            </tr>
            <tr>
                <td><strong>Message Content:</strong></td>
                <td><textarea readonly><?= htmlspecialchars($message); ?></textarea></td>
            </tr>
        </table>

        <div class="message-actions">
            <a href="pm_send.php?to=<?= urlencode($fromUser); ?>" class="reply btn btn-success">Reply</a>
            <!-- Add delete functionality as needed -->
        </div>
    </div>
</body>
</html>

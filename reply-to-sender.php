<?php
session_start();
include("db.php");

if (isset($_POST['send_message'])) {
    $recipient = $_POST['recipient'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];
    $fromUser = $_SESSION['username'];
    $receive_date = date('Y-m-d H:i:s'); // current date and time in Y-m-d H:i:s format

    if (!empty($recipient) && !empty($message)) {
        // Prepare the SQL statement to insert the message into the database
        $stmt = $con->prepare("INSERT INTO messages (FromUser, ToUser, message, receive_date) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $fromUser, $recipient, $message, $receive_date);
        
        if ($stmt->execute()) {
            echo "<p>Message sent successfully!</p>";
        } else {
            echo "<p>Error sending message: " . $con->error . "</p>";
        }

        $stmt->close();
    } else {
        echo "<p>Please fill in all required fields.</p>";
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Bohemia - Compose a Message</title>
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
    <link rel="stylesheet" href="password-strength-indicator.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/jpeg" href="img/pentagram.jpg">
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
        .message-row {
            text-align: center;
        }
        .message-title {
            font-weight: bold;
        }
        .message-title a {
            text-decoration: none;
        }
        .message-title a:hover {
            text-decoration: underline;
        }
        .message-actions {
            margin-top: 10px;
        }
        .message-actions a {
            margin-right: 10px;
            color: #007bff;
            text-decoration: none;
        }
        .message-actions a.delete {
            color: #dc3545;
        }
        .message-actions a i {
            margin-right: 5px;
        }
    </style>
</head>
<body>
<div class="navigation">
    <div class="wrapper">
        <ul>
            <li class="nav-logo"><a href="homepage.php"><img src="Listings_files/logo_small.png" style="height: 100%;"></a></li>
            <div class="responsive-menu">
                <li class="menu-toggler"><a href="#">Navigation&nbsp; <div class="sprite sprite--caret" style="float: none; display: inline-block; margin-left:5px;"></div></a></li>
                <div class="menu-links">
                    <li><a href="homepage.php">Home</a></li>
                    <li class="dropdown-link dropdown-large">
                        <a href="orders.php?action=orders" class="dropbtn">Orders</a>
                        <div class="dropdown-content right-dropdown">
                            <a href="processing.php?action=orders">Processing <span class="badge badge-secondary right">0</span></a>
                            <a href="dispatched.php?action=orders&amp;do=shipped">Dispatched <span class="badge badge-secondary right">0</span></a>
                            <a href="completed.php?action=orders&amp;do=processed">Completed <span class="badge badge-danger right">1</span></a>
                            <a href="disputed.php?action=orders&amp;do=disputed">Disputed <span class="badge badge-secondary right">0</span></a>
                            <a href="canceled.php?action=orders&amp;do=canceled">Canceled</a>
                        </div>
                    </li>
                    <li><a href="listings.php">Listings</a></li>
                    <li class="dropdown-link dropdown-large">
                        <a href="messages.php" class="dropbtn">Messages&nbsp;<span class="badge badge-secondary">0</span></a>
                        <div class="dropdown-content right-dropdown">
                            <a href="compose.php?action=compose">Compose Message</a>
                            <a href="messages.php">Inbox</a>
                            <a href="messages.php?action=sent">Sent Items</a>
                        </div>
                    </li>
                    <li class="dropdown-link dropdown-large">
                        <a href="wallet.php?action=wallet" class="dropbtn">Wallet</a>
                        <div class="dropdown-content right-dropdown">
                            <a href="exchange.php?action=exchange">Exchange</a>
                        </div>
                    </li>
                    <li class="dropdown-link dropdown-large">
                        <a href="bug-report.php" class="dropbtn">Support</a>
                        <div class="dropdown-content right-dropdown">
                            <a href="faq.php">F.A.Q</a>
                            <a href="support-tickets-and-bug-reports.php">Support Tickets</a>
                            <a href="bug-report.php">Report Bug</a>
                        </div>
                    </li>
                </div>
            </div>
            <li class="dropdown-link user-nav right fix-gap">
                <button class="dropbtn" style="margin-top:5px;"><?php echo htmlspecialchars($_SESSION["username"]); ?>&nbsp;<div class="sprite sprite--caret" style="float: right; top: 1px;"></div></button>
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
                    <img src="cart.png" style="width: 20px; height: 25px; display: inline-block; margin-top: 20px;">
                    &nbsp;<span class="badge badge-danger" style="padding: 0.3em 0.4em; font-size: 75%; font-weight: 700; top: 24px; line-height: 1; position: absolute; text-align: center; white-space: nowrap; vertical-align: baseline; border-radius: 0.25rem; background-color:grey;">0</span>
                </a>
            </li>
            <li class="right shopping-cart-link">
                <a href="cart.php">
                    <img src="alert-bell.png" style="width: 20px; height: 25px; display: inline-block; margin-top: 20px;">
                    &nbsp;<span class="badge badge-danger" style="padding: 0.3em 0.4em; font-size: 75%; font-weight: 700; top: 24px; line-height: 1; position: absolute; text-align: center; white-space: nowrap; vertical-align: baseline; border-radius: 0.25rem; background-color:grey;">0</span>
                </a>
            </li>
            <li class="dropdown-link dropdown-large" style="margin-left:260px; position:absolute; width:210px; margin-top:-15px;">
                <a href="control-panel.php" class="dropbtn"><p>C Panel</p></a>
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
            <li class="right fix-gap" style="list-style:none;"><a href="become-a-merchant.php"><b>Become A Merchant</b></a></li>
        </ul>
    </div>
</div>
            <div class="col-md-9 sidebar-content-right">
                <form action="compose-message.php" method="POST">
                    <div class="container"> 
                        <div class="row form-row align-center">
                            <div class="col-md-3">
                                <label>Recipient</label>
                            </div>
                            <div class="col-md-6">
                                <input style="width:50%;" type="text" name="recipient" class="form-control" placeholder="Type the recipient's username here." value="">
                            </div>
                        </div>
                        <hr>
                        <div class="row form-row align-center">
                            <div class="col-md-3">
                                <label>Subject</label>
                            </div>
                            <div class="col-md-6">
                                <input style="width:50%;" type="text" name="subject" class="form-control" placeholder="Enter a subject for your message." value="">
                            </div>
                        </div>
                        <div class="row form-row">
                            <div class="col-md-3">
                                <label>Message</label>
                            </div>
                            <div class="col-md-6">
                                <textarea class="form-control" name="message" placeholder="Type in your message here." rows="15"><?php  ?></textarea>
                            </div>
                        </div>
                        <hr>
                    </div>
                    <button type="submit" class="btn btn-blue">Send Message</button>
                    <input type="hidden" name="send_message" value="1">
                </form>
            </div>
        </div>
    </div>

</body>
</html>

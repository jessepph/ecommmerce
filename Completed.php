<?php
session_start();
include("db.php");
?>
<!DOCTYPE html>
<html><head>
    <title>Asmodeus - Completed</title>
            <meta http-equiv="Content-type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" type="text/css" href="Bohemia%20-%20Orders-Completed_files/flexboxgrid.min.css">
        <link rel="stylesheet" type="text/css" href="Bohemia%20-%20Orders-Completed_files/fontawesome-all.min.css">
        <link rel="stylesheet" type="text/css" href="Bohemia%20-%20Orders-Completed_files/style.css"><link rel="stylesheet" type="text/css" href="Bohemia%20-%20Orders-Completed_files/main.css"><link rel="stylesheet" type="text/css" href="Bohemia%20-%20Orders-Completed_files/responsive.css">        <link rel="stylesheet" type="text/css" href="Bohemia%20-%20Orders-Completed_files/sprite.css">
    <style type="text/css">.sprite sprite--cog:hover {pointer-events: none;}</style>
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
        <?php echo htmlspecialchars($badge_text); ?>
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
            
        <div class="wrapper">
                    




        
        

       
                               
        <div class="wrapper">
        <div class="row">
            <div class="col-md-3 sidebar-navigation">
                <ul class="box">
    <li class="title"><h2>User Control Panel</h2></li>
    <li><a href="localhost/bohemia/usercp.php"><div class="sprite sprite--home" style="top: 2px; margin-right: 15px;"></div>User CP Home</a></li> 
    <li><a href="localhost/bohemia/usercp.php?action=following"><div class="sprite sprite--star" style="top: 2px; margin-right: 15px;"></div>Favorite Merchants</a></li>
   
    <li><a href="localhost/bohemia/usercp.php?action=orders" class="active"><div class="sprite sprite--clipboardlist" style="top: 2px; margin-right: 15px;"></div>Orders</a></li>
    <li>
	<a href="localhost/bohemia/usercp.php?action=wallet"><div class="sprite sprite--wallet" style="top: 2px; margin-right: 15px;"></div>Wallet</a>	
    </li>
    <li><a href="localhost/bohemia/usercp.php?action=exchange"><div class="sprite sprite--affiliate" style="top: 2px; margin-right: 15px;"></div>Exchange </a></li>
    <li><a href="localhost/bohemia/usercp.php?action=affiliate"><div class="sprite sprite--money" style="top: 2px; margin-right: 15px;"></div>Affiliate Programme</a></li>
    <li><a href="localhost/bohemia/usercp.php?action=editprofile"><div class="sprite sprite--card" style="top: 2px; margin-right: 15px;"></div>Edit Profile</a></li>
    <li><a href="localhost/bohemia/usercp.php?action=changepin"><div class="sprite sprite--qr" style="top: 2px; margin-right: 15px;"></div>Change PIN</a></li>
    <li><a href="localhost/bohemia/usercp.php?action=changepassword"><div class="sprite sprite--lock" style="top: 2px; margin-right: 15px;"></div>Change Password</a></li>
    <li><a href="localhost/bohemia/usercp.php?action=settings"><div class="sprite sprite--cog" style="top: 2px; margin-right: 15px"></div>Settings</a></li>
</ul>
            </div>
            <div class="col-md-9 sidebar-content-right">
                <form action="" method="GET" style="display: flex; min-height: 55px;">
                    <div style="flex-grow: 1;">
                        <input type="hidden" name="action" value="orders">
                        <input type="hidden" name="do" value="processed">
                        <input type="text" name="query" class="form-control" placeholder="Search orders by username, order reference, or listing name...">
                    </div>
                    <div style="margin-left: 1em;">
                        <button type="submit" class="btn btn-blue" style="height: 43px; padding: 10px 30px;">Search</button>
                    </div>
                </form>
                <div class="container product-listing nopadding">
                    <ul class="tabs">
                        
                            <li><a href="processing.php" class="">Processing&nbsp; <span class="badge badge-secondary">0</span></a></li>
                            <li><a href="dispatched.php">Dispatched&nbsp; <span class="badge badge-danger">1</span></a></li>
                            <li><a href="completed.php" class="tab-active">Completed&nbsp; <span class="badge badge-danger">1</span></a></li>
                            <li><a href="disputed.php" class="">Disputed&nbsp; <span class="badge badge-secondary">0</span></a></li>
                            <li><a href="canceled.php" class="">Canceled&nbsp; <span class="badge badge-secondary">0</span></a></li>

                                            </ul>
                    <form action="" method="post">

                        <table class="cart-table" cellspacing="0" cellpadding="0">
                            <thead>
                                <tr>
                                                                        <th width="5%"></th>
                                    <th width="50%">Product</th>
                                    <th width="10%"></th>
                                    <th width="10%">Total</th>
                                    <th width="10%">Status</th>
                                    <th style="min-width: 100px; text-align: center;" width="15%"><div class="sprite sprite--cog" style="top:1px; left: 60px;"></div></th>
                                </tr>
                            </thead>
                            <tbody>
                                                                        <tr>
                                                                                        <td><img src="Bohemia%20-%20Orders-Completed_files/image_002.jpeg" style="width: 60px;"></td>
                                            <td>
                                                <b>1x <a href="http://phfbc3whrbtij36skevox76eqckcwrpyzvegbtpx3afmhdrsrrzjj6id.onion/listing.php?pid=925f76f9-d034-11ec-b7d8-0025909102ac">1g Cocaine - Pure Uncut Sociable Cocaine; Grade A</a>&nbsp; <span class="smalltext">In Cocaine</span></b><br>
                                                <b><a href="http://phfbc3whrbtij36skevox76eqckcwrpyzvegbtpx3afmhdrsrrzjj6id.onion/profile.php?id=JanesAddiction">JanesAddiction</a></b>
                                                <span class="smalltext shadow-text"> | <span class="user-percent" style="color: #29b474;">94%</span> Positive Feedback&nbsp; |&nbsp; <div class="sprite sprite--shopping-cart" style="float: none;display: inline-block; margin-left:5px;;"></div>&nbsp; 1311</span>
                                            </td>
                                            <td><div class="sprite sprite--monero" style="top:2px;"></div>&nbsp;                                        
                                        </td>

                                            <td>$75 <br><span class="smalltext shadow-text">Total cost</span></td>
                                            <td>
                                                <span class="badge badge-pill badge-success">Finalized</span>                                            </td>
                                            <td>
                                                                                                        <div class="dropdown-link">
                                                            <button class="dropbtn btn btn-blue">Options&nbsp; <div class="sprite sprite--caret" style="float: right; top: 1px;"></div></button>
                                                            <div class="dropdown-content">
                                                                <a href="http://phfbc3whrbtij36skevox76eqckcwrpyzvegbtpx3afmhdrsrrzjj6id.onion/usercp.php?action=orders&amp;ref=PTTBOUM20230727C4EF">View Order</a>
                                                                <a href="http://phfbc3whrbtij36skevox76eqckcwrpyzvegbtpx3afmhdrsrrzjj6id.onion/usercp.php?action=orders&amp;ref=PTTBOUM20230727C4EF&amp;do=feedback">Leave Feedback</a>                                                                <a href="http://phfbc3whrbtij36skevox76eqckcwrpyzvegbtpx3afmhdrsrrzjj6id.onion/usercp.php?action=orders&amp;ref=PTTBOUM20230727C4EF&amp;do=dispute">Open Dispute</a>                                                                

                                                                
                                                            </div>
                                                        </div>
                                                                                            </td>
                                        </tr>
                                                                        <tr>
                                                                                        <td><img src="Bohemia%20-%20Orders-Completed_files/image.jpeg" style="width: 60px;"></td>
                                            <td>
                                                <b>1x <a href="http://phfbc3whrbtij36skevox76eqckcwrpyzvegbtpx3afmhdrsrrzjj6id.onion/listing.php?pid=7cbd401d-bf52-11ec-b7b1-0025909102ac">Blue Cookies - Outdoor - 1/2 lb (8 oz)</a>&nbsp; <span class="smalltext">In Buds</span></b><br>
                                                <b><a href="http://phfbc3whrbtij36skevox76eqckcwrpyzvegbtpx3afmhdrsrrzjj6id.onion/profile.php?id=growerdirect">growerdirect</a></b>
                                                <span class="smalltext shadow-text"> | <span class="user-percent" style="color: #29b474;">95%</span> Positive Feedback&nbsp; |&nbsp; <div class="sprite sprite--shopping-cart" style="float: none;display: inline-block; margin-left:5px;;"></div>&nbsp; 2349</span>
                                            </td>
                                            <td><div class="sprite sprite--bitcoin" style="top:2px;"></div>&nbsp;                                        
                                        </td>

                                            <td>$210 <br><span class="smalltext shadow-text">Total cost</span></td>
                                            <td>
                                                <span class="badge badge-pill badge-success">Finalized</span>                                            </td>
                                            <td>
                                                                                                        <div class="dropdown-link">
                                                            <button class="dropbtn btn btn-blue">Options&nbsp; <div class="sprite sprite--caret" style="float: right; top: 1px;"></div></button>
                                                            <div class="dropdown-content">
                                                                <a href="http://phfbc3whrbtij36skevox76eqckcwrpyzvegbtpx3afmhdrsrrzjj6id.onion/usercp.php?action=orders&amp;ref=EZPZRV20230626A573">View Order</a>
                                                                <a href="http://phfbc3whrbtij36skevox76eqckcwrpyzvegbtpx3afmhdrsrrzjj6id.onion/usercp.php?action=orders&amp;ref=EZPZRV20230626A573&amp;do=feedback">Leave Feedback</a>                                                                <a href="http://phfbc3whrbtij36skevox76eqckcwrpyzvegbtpx3afmhdrsrrzjj6id.onion/usercp.php?action=orders&amp;ref=EZPZRV20230626A573&amp;do=dispute">Open Dispute</a>                                                                

                                                                
                                                            </div>
                                                        </div>
                                                                                            </td>
                                        </tr>
                                                            </tbody>
                        </table>
                        
                    </form>
                </div>
                            </div>
        </div>
    </div>



</body></html>
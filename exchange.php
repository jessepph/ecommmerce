<!DOCTYPE html>
<html><head>
        <title>Bohemia - Exchange</title>
                <meta http-equiv="Content-type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" type="text/css" href="exchange_files/flexboxgrid.min.css">
        <link rel="icon" type="image/jpeg" href="img/pentagram.jpg">
        <link rel="stylesheet" type="text/css" href="exchange_files/fontawesome-all.min.css">
        <link rel="stylesheet" type="text/css" href="exchange_files/style.css"><link rel="stylesheet" type="text/css" href="exchange_files/main.css"><link rel="stylesheet" type="text/css" href="exchange_files/responsive.css">        <link rel="stylesheet" type="text/css" href="exchange_files/sprite.css">
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
                                <a href="completed.php">Completed&nbsp; <span class="badge badge-danger right">2</span></a>
                                <a href="disputed.php">Disputed&nbsp; <span class="badge badge-secondary right">0</span></a>
                                <a href="canceled.php">Canceled</a>
                            </div>
                        </li>
 <?php 
 include("myfunctions.php");
session_start();
require("db.php");
//require("create-wallet.php");
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
            <div class="row">
                <div class="col-md-3 sidebar-navigation">
                    <ul class="box">
    <li class="title"><h2>User Control Panel</h2></li>
    <li><a href="http://7debhlajdzetpsdnlt2wur2mk3m52q2uuu6mxfak67nyk3sge7nv5oid.onion/usercp.php"><div class="sprite sprite--home" style="top: 2px; margin-right: 15px;"></div>User CP Home</a></li> 
    <li><a href="http://7debhlajdzetpsdnlt2wur2mk3m52q2uuu6mxfak67nyk3sge7nv5oid.onion/usercp.php?action=following"><div class="sprite sprite--star" style="top: 2px; margin-right: 15px;"></div>Favorite Merchants</a></li>
   
    <li><a href="http://7debhlajdzetpsdnlt2wur2mk3m52q2uuu6mxfak67nyk3sge7nv5oid.onion/usercp.php?action=orders"><div class="sprite sprite--clipboardlist" style="top: 2px; margin-right: 15px;"></div>Orders</a></li>
    <li>
	<a href="http://7debhlajdzetpsdnlt2wur2mk3m52q2uuu6mxfak67nyk3sge7nv5oid.onion/usercp.php?action=wallet"><div class="sprite sprite--wallet" style="top: 2px; margin-right: 15px;"></div>Wallet</a>	
    </li>
    <li><a href="http://7debhlajdzetpsdnlt2wur2mk3m52q2uuu6mxfak67nyk3sge7nv5oid.onion/usercp.php?action=exchange" class="active"><div class="sprite sprite--affiliate" style="top: 2px; margin-right: 15px;"></div>Exchange </a></li>
    <li><a href="http://7debhlajdzetpsdnlt2wur2mk3m52q2uuu6mxfak67nyk3sge7nv5oid.onion/usercp.php?action=affiliate"><div class="sprite sprite--money" style="top: 2px; margin-right: 15px;"></div>Affiliate Programme</a></li>
    <li><a href="http://7debhlajdzetpsdnlt2wur2mk3m52q2uuu6mxfak67nyk3sge7nv5oid.onion/usercp.php?action=editprofile"><div class="sprite sprite--card" style="top: 2px; margin-right: 15px;"></div>Edit Profile</a></li>
    <li><a href="http://7debhlajdzetpsdnlt2wur2mk3m52q2uuu6mxfak67nyk3sge7nv5oid.onion/usercp.php?action=changepin"><div class="sprite sprite--qr" style="top: 2px; margin-right: 15px;"></div>Change PIN</a></li>
    <li><a href="http://7debhlajdzetpsdnlt2wur2mk3m52q2uuu6mxfak67nyk3sge7nv5oid.onion/usercp.php?action=changepassword"><div class="sprite sprite--lock" style="top: 2px; margin-right: 15px;"></div>Change Password</a></li>
    <li><a href="http://7debhlajdzetpsdnlt2wur2mk3m52q2uuu6mxfak67nyk3sge7nv5oid.onion/usercp.php?action=settings"><div class="sprite sprite--cog" style="top: 2px; margin-right: 15px"></div>Settings</a></li>
</ul>
                </div>
                <div class="col-md-9 sidebar-content-right">

                    		    <h1>Exchange</h1>
			<div class="alert alert-primary">Exchange BTCXMR &amp; XMRBTC for only a 1% fee!<br>
			How it works:
			<br>
			<br>
			1. Place a trade offer below.<br>
			2. A user who wishes to trade, will fulfil your order.<br>
			3. You coins are exchanged!<br>
			<br>
			<b>Note:</b> This is a 'peer-to-peer' exchange facility, your exchange will not be instant.</div>
		    <div class="row" style="margin-bottom: 1em;">
			<div class="col-md-6 no-padding-left">
			    <form action="" method="POST" style="height:100%;">
				<div class="container nopadding text-left" style="height:100%;">
				    <ul class="tabs">
				    	<li><a href="http://7debhlajdzetpsdnlt2wur2mk3m52q2uuu6mxfak67nyk3sge7nv5oid.onion/usercp.php?action=exchange" class="tab-active">BTC 🡆&nbsp; XMR</a></li>
					<li><a href="http://7debhlajdzetpsdnlt2wur2mk3m52q2uuu6mxfak67nyk3sge7nv5oid.onion/usercp.php?action=exchange&amp;trade=XMRBTC">XMR 🡆&nbsp; BTC</a></li>
				    </ul>
				    <div class="container-content">
					    <div class="text-center">
						<h3>1 BTC ~= 181.74512236 XMR</h3>					    </div>
					    <div class="row form-row align-center" style="justify-content: center;">
						<div class="col-md-6">	
						    <label>Amount</label>
						    <input type="text" name="amount" class="form-control" placeholder="">
						    Total Available: 0.00016300						</div>
					    </div>
					    <div class="form-group text-center">
						<button type="submit" name="open_trade" class="btn btn-blue">Open Trade</button>
					    </div>
				    </div>
				</div>
			    </form>
			</div>
			<div class="col-md-6 no-padding-right">
			    <div class="container nopadding" style="height: 100%;">
				<div class="container-header"><div class="sprite sprite--exchange"></div>&nbsp; Exchange Rates</div>
				    <table class="table exchange-table" cellspacing="0" cellpadding="0">
					<tbody><tr>
					    <th></th>
					    <th class="text-center">USD</th>
					    <th class="text-center">EUR</th>
					    <th class="text-center">GBP</th>
					    <th class="text-center">CAD</th>
					    <th class="text-center">AUD</th>				    
					</tr>  
					
					<tr>
					    <td><strong><div class="sprite sprite--bitcoin" style="top:2px;"></div>&nbsp;Bitcoin</strong></td>
					    <td class="text-center">28972</td>
					    <td class="text-center">26276</td>
					    <td class="text-center">22727</td>
					    <td class="text-center">38777</td>
					    <td class="text-center">43997</td>
											
					</tr> 

					
					<tr>
					    <td><strong><div class="sprite sprite--monero" style="top:2px;"></div>&nbsp;Monero</strong></td>
					    <td class="text-center">159.41</td>
					    <td class="text-center">144.58</td>
					    <td class="text-center">125.05</td>
					    <td class="text-center">213.36</td>
					    <td class="text-center">242.08</td>
											
					</tr> 

					        
				    </tbody></table>
			    </div>
			</div>
		    </div>
		    <div class="container nopadding">
			<ul class="tabs">
			<li><a href="http://7debhlajdzetpsdnlt2wur2mk3m52q2uuu6mxfak67nyk3sge7nv5oid.onion/usercp.php?action=exchange" class="tab-active">Open Trades</a></li>
			<li><a href="http://7debhlajdzetpsdnlt2wur2mk3m52q2uuu6mxfak67nyk3sge7nv5oid.onion/usercp.php?action=exchange&amp;do=my_trades">My Trades</a></li>
			</ul>
                        <table class="table" cellspacing="0" cellpadding="0">
                            <thead>
                                <tr>
                                    <th width="30%">Pair</th>
				    <th width="30%">You pay</th>
				    <th width="30%">You get</th>
				    				    <th width="10%"></th>
                                </tr>
                            </thead>
                            <tbody>

                            				 <tr>	
				 	<td>BTC&nbsp;<i class="fa fa-arrow-right"></i>&nbsp;XMR</td>
                                        <td>0.79195668 XMR</td>
					<td>0.00435718 BTC</td>
					 
					<td>
						<form action="" method="post">
											
						<button type="submit" class="btn btn-blue" name="trade" value="43234fea-342e-11ee-aa85-d05099fcf7bb">Trade</button>
											</form>
					</td>
				    </tr>
                                    				 <tr>	
				 	<td>BTC&nbsp;<i class="fa fa-arrow-right"></i>&nbsp;XMR</td>
                                        <td>0.73846501 XMR</td>
					<td>0.00406288 BTC</td>
					 
					<td>
						<form action="" method="post">
											
						<button type="submit" class="btn btn-blue" name="trade" value="6ffd8e63-3439-11ee-aa85-d05099fcf7bb">Trade</button>
											</form>
					</td>
				    </tr>
                                    				 <tr>	
				 	<td>BTC&nbsp;<i class="fa fa-arrow-right"></i>&nbsp;XMR</td>
                                        <td>0.38987851 XMR</td>
					<td>0.00214503 BTC</td>
					 
					<td>
						<form action="" method="post">
											
						<button type="submit" class="btn btn-blue" name="trade" value="75ec5f19-343c-11ee-aa85-d05099fcf7bb">Trade</button>
											</form>
					</td>
				    </tr>
                                    				 <tr>	
				 	<td>BTC&nbsp;<i class="fa fa-arrow-right"></i>&nbsp;XMR</td>
                                        <td>7.19765640 XMR</td>
					<td>0.03960000 BTC</td>
					 
					<td>
						<form action="" method="post">
											
						<button type="submit" class="btn btn-blue" name="trade" value="d07f94e8-345b-11ee-aa85-d05099fcf7bb">Trade</button>
											</form>
					</td>
				    </tr>
                                    				 <tr>	
				 	<td>BTC&nbsp;<i class="fa fa-arrow-right"></i>&nbsp;XMR</td>
                                        <td>0.47313140 XMR</td>
					<td>0.00260307 BTC</td>
					 
					<td>
						<form action="" method="post">
											
						<button type="submit" class="btn btn-blue" name="trade" value="0e35fd17-3461-11ee-aa85-d05099fcf7bb">Trade</button>
											</form>
					</td>
				    </tr>
                                    
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    


</body></html>
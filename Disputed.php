<?php
session_start();
include("db.php");

?>
<!DOCTYPE html>
<html><head>
    <title>Bohemia - Orders</title>
            <meta http-equiv="Content-type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" type="text/css" href="Bohemia%20-%20Orders-Disputed_files/flexboxgrid.min.css">
        <link rel="icon" type="image/jpeg" href="img/pentagram.jpg">
        <link rel="stylesheet" type="text/css" href="Bohemia%20-%20Orders-Disputed_files/fontawesome-all.min.css">
        <link rel="stylesheet" type="text/css" href="Bohemia%20-%20Orders-Disputed_files/style.css"><link rel="stylesheet" type="text/css" href="Bohemia%20-%20Orders-Disputed_files/main.css"><link rel="stylesheet" type="text/css" href="Bohemia%20-%20Orders-Disputed_files/responsive.css">        <link rel="stylesheet" type="text/css" href="Bohemia%20-%20Orders-Disputed_files/sprite.css">
    <style type="text/css">.sprite sprite--cog:hover {pointer-events: none;}</style>
</head>

<body>
<div class="navigation">
        <div class="wrapper">
            <ul>
                <li class="nav-logo"><a href="http://localhost/bohemia/"><img src="Listings_files/logo_small.png" style="height: 100%;"></a></li>
                <div class="responsive-menu">
                    <li class="menu-toggler"><a href="#">Navigation&nbsp; <div class="sprite sprite--caret" style="float: none; display: inline-block; margin-left:5px;"></div></a></li>
                    <div class="menu-links">
                        <li class=""><a href="homepage.php">Home</a></li>
                        
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

                        
                        <li class=""><a href="listings.php">Listings</a></li>
                        <li class="dropdown-link dropdown-large ">
                            <a href="messages.php" class="dropbtn">
                                Messages&nbsp;
                                <span class="badge badge-secondary">0</span>                            </a>
                            <div class="dropdown-content right-dropdown">
                                <a href="compose-message.php?action=compose">Compose Message</a>
                                <a href="messages.php">Inbox</a>
                                <a href="messages.php?action=sent">Sent Items</a>
                            </div>
                        </li>
			<li class="dropdown-link dropdown-large">
			    <a href="wallet.php?action=wallet" class="dropbtn">Wallet</a>
			    <div class="dropdown-content right-dropdown">
                                <a href="exchange.php">Exchange</a>
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
                    <button class="dropbtn" style="margin-top:10px;" ><?php echo "" . $_SESSION["username"] . "<br>"; ?>&nbsp; <div class="sprite sprite--caret" style="float: right; top: 1px;"></div></button>
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
              
                <li class="right shopping-cart-link ">
                  <a href="cart.php">
                    <img src="cart.png" style="    
                    width: 20px;
                    height: 25px;
                    display: inline-block;
                    margin-top: 20px;
                    float:none;
                    ">
                    &nbsp;<span class="badge badge-danger" style="
                    padding: 0.3em 0.4em;
                    font-size: 75%;
                    font-weight: 700;
                    top: 24px;
                    line-height: 1;
                    position: absolute;
                    text-align: center;
                    white-space: nowrap;
                    vertical-align: baseline;
                    border-radius: 0.25rem;
                    background-color:grey;
                    ">0</span>               
                    
                                            </a>
                </li>
                <li class="right shopping-cart-link ">
                  <a href="cart.php">
                    <img src="alert-bell.png" style="    
                    width: 20px;
                    height: 25px;
                    display: inline-block;
                    margin-top: 20px;
                    float:none;
                    ">
                    &nbsp;<span class="badge badge-danger" style="
                    padding: 0.3em 0.4em;
                    font-size: 75%;
                    font-weight: 700;
                    top: 24px;
                    line-height: 1;
                    position: absolute;
                    text-align: center;
                    white-space: nowrap;
                    vertical-align: baseline;
                    border-radius: 0.25rem;
                    background-color:grey;
                    ">0</span>               
                    
                                            </a>
                </li>
                <li class="dropdown-link dropdown-large " style="margin-left:260px; position:absolute; width:210px; margin-top:-15px;">
                            <a href="control-panel.php" class="dropbtn">
                               <p style="margin-top:10px;">C Panel</p>
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
              
                
                <li class="right fix-gap" style="list-style:none;"><a href="becoming-a-merchant.php"><b>Become A Merchant</b></a></li>
                
        </div>
    </div>
        <div class="wrapper">
        <div class="row">
            <div class="col-md-3 sidebar-navigation">
                <ul class="box">
    <li class="title"><h2>User Control Panel</h2></li>
    <li><a href="usercp.php"><div class="sprite sprite--home" style="top: 2px; margin-right: 15px;"></div>User CP Home</a></li> 
    <li><a href="usercp.php?action=following"><div class="sprite sprite--star" style="top: 2px; margin-right: 15px;"></div>Favorite Merchants</a></li>
   
    <li><a href="orders.php" class="active"><div class="sprite sprite--clipboardlist" style="top: 2px; margin-right: 15px;"></div>Orders</a></li>
    <li>
	<a href="wallet.php"><div class="sprite sprite--wallet" style="top: 2px; margin-right: 15px;"></div>Wallet</a>	
    </li>
    <li><a href="exchange.php"><div class="sprite sprite--affiliate" style="top: 2px; margin-right: 15px;"></div>Exchange </a></li>
    <li><a href="affiliate.php"><div class="sprite sprite--money" style="top: 2px; margin-right: 15px;"></div>Affiliate Programme</a></li>
    <li><a href="editprofile.php"><div class="sprite sprite--card" style="top: 2px; margin-right: 15px;"></div>Edit Profile</a></li>
    <li><a href="changepin.php"><div class="sprite sprite--qr" style="top: 2px; margin-right: 15px;"></div>Change PIN</a></li>
    <li><a href="changepassword.php"><div class="sprite sprite--lock" style="top: 2px; margin-right: 15px;"></div>Change Password</a></li>
    <li><a href="settings.php"><div class="sprite sprite--cog" style="top: 2px; margin-right: 15px"></div>Settings</a></li>
</ul>
            </div>
            <div class="col-md-9 sidebar-content-right">
                <form action="" method="GET" style="display: flex; min-height: 55px;">
                    <div style="flex-grow: 1;">
                        <input type="hidden" name="action" value="orders">
                        <input type="hidden" name="do" value="disputed">
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
                            <li><a href="completed.php" class="">Completed&nbsp; <span class="badge badge-secondary">0</span></a></li>
                            <li><a href="disputed.php" class="tab-active">Disputed&nbsp; <span class="badge badge-secondary">0</span></a></li>
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
                                <tr><td colspan="6">There are no orders by this criteria.</td></tr>                            </tbody>
                        </table>
                        
                    </form>
                </div>
                            </div>
</body>
</html>
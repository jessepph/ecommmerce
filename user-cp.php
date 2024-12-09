<?php
include("myfunctions.php");
session_start();
require_once("db.php");
?>

<!DOCTYPE html>
<html><head>
    <title>User Control Panel</title>        <meta http-equiv="Content-type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" type="text/css" href="Listings_files/flexboxgrid.min.css">
        <link rel="stylesheet" type="text/css" href="Listings_files/fontawesome-all.min.css">
        <link rel="stylesheet" type="text/css" href="Listings_files/style.css">
        <link rel="stylesheet" type="text/css" href="Listings_files/main.css">
        <link rel="stylesheet" type="text/css" href="Listings_files/responsive.css">        
        <link rel="stylesheet" type="text/css" href="sprite.css">
        <link rel="stylesheet" href="style.css">
        <link rel="icon" type="image/jpeg" href="img/pentagram.jpg">
           
</head>

<body><div class="navigation">
        <div class="wrapper">
            <ul>
                <li class="nav-logo"><a href="http://localhost/bohemia/"><img src="Listings_files/logo_small.png" style="height: 100%;"></a></li>
                <div class="responsive-menu">
                    <li class="menu-toggler"><a href="#">Navigation&nbsp; <div class="sprite sprite--caret" style="float: none; display: inline-block; margin-left:5px;"></div></a></li>
                    <div class="menu-links">
                        <li><a href="homepage.php">Home</a></li>
                        
                        <li class="dropdown-link dropdown-large ">
                            <a href="usercp.php?action=orders" class="dropbtn">
                                Orders
                                                            </a>
                            <div class="dropdown-content right-dropdown">
                                <a href="usercp.php?action=orders">Processing&nbsp; <span class="badge badge-secondary right">0</span></a>
                                <a href="usercp.php?action=orders&amp;do=shipped">Dispatched&nbsp; <span class="badge badge-secondary right">0</span></a>
                                <a href="usercp.php?action=orders&amp;do=processed">Completed&nbsp; <span class="badge badge-danger right">1</span></a>
                                <a href="usercp.php?action=orders&amp;do=disputed">Disputed&nbsp; <span class="badge badge-secondary right">0</span></a>
                                <a href="usercp.php?action=orders&amp;do=canceled">Cancelled</a>
                            </div>
                        </li>

                        
                        <li class=""><a href="listings.php">Listings</a></li>
                        <li class="dropdown-link dropdown-large ">
                            <a href="messages.php" class="dropbtn">
                                Messages&nbsp;
                                <span class="badge badge-secondary">0</span>                            </a>
                            <div class="dropdown-content right-dropdown">
                                <a href="messages.php?action=compose">Compose Message</a>
                                <a href="messages.php">Inbox</a>
                                <a href="messages.php?action=sent">Sent Items</a>
                            </div>
                        </li>
			<li class="dropdown-link dropdown-large">
			    <a href="usercp.php?action=wallet" class="dropbtn">Wallet</a>
			    <div class="dropdown-content right-dropdown">
                                <a href="usercp.php?action=exchange">Exchange</a>
                            </div>
			</li>
                        <li class="dropdown-link dropdown-large ">
                            <a href="#" class="dropbtn">
                                Support
                                                            </a>
                            <div class="dropdown-content right-dropdown">
                                <a href="faq.php">F.A.Q</a>
                                <a href="support.php">
                                    Support Tickets
                                                                    </a>
                                <a href="support?action=new&amp;do=bugreport">
                                    Report Bug
                                </a>
                            </div>
                        </li>
                    </div>
                </div>

                <li class="dropdown-link user-nav right fix-gap">
                    <button class="dropbtn" style="margin-top:10px;"><?php echo $_SESSION["username"] . "<br>"; ?>&nbsp; <div class="sprite sprite--caret" style="float: right; top: 1px;"></div></button>
                    <div class="dropdown-content">
                        
                        <div class="user-balance">
                            <span class="shadow-text">Balances</span><br>
                            <span class="balance">$</span>4.73 <sup>0.00016300 BTC</sup><br><span class="balance">$</span>0.00 <sup>0.00141754 XMR</sup><br>
                        </div>
                                                <a href="profile.php?id=60Agent">My Profile</a>

                        <a href="theme.php">Night Mode</a>
                        <a href="usercp.php">User CP</a>
                        <a href="logout.php">Logout</a>
                    </div>
                </li>
                <li class="right shopping-cart-link ">
                    <a href="cart.php">
                        <div class="sprite sprite--cart" style="float: none; display: inline-block; margin-left:5px;;"></div>
                                            </a>
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
              
                
                <li class="right fix-gap" style="list-style:none;"><a href="#"><b>Become A Merchant</b></a></li>
                
        </div>
    </div>
    <div class="wrapper">
    <div class="wrapper">
        <div class="row">
            <div class="col-md-3 sidebar-navigation">
                <ul class="box">
    <li class="title"><h2>User Control Panel</h2></li>
    <li><a href="usercp.php"><div class=" active sprite sprite--home" style="top: 2px; margin-right: 15px;"></div>User CP Home</a></li> 
    <li><a href="http://localhost/following.php?action=following"><div class="sprite sprite--star" style="top: 2px; margin-right: 15px;"></div>Favorite Merchants</a></li>
   
    <li><a href="http://localhost/orders.php?action=orders" class=""><div class="sprite sprite--clipboardlist" style="top: 2px; margin-right: 15px;"></div>Orders</a></li>
    <li>
	<a href="http://localhost/bohemia/wallet.php?action=wallet"><div class="sprite sprite--wallet" style="top: 2px; margin-right: 15px;"></div>Wallet</a>	
    </li>
    <li><a href="http://localhost/exchange.php?action=exchange"><div class="sprite sprite--affiliate" style="top: 2px; margin-right: 15px;"></div>Exchange </a></li>
    <li><a href="http:/localhost/afilliate.php?action=affiliate"><div class="sprite sprite--money" style="top: 2px; margin-right: 15px;"></div>Affiliate Programme</a></li>
    <li><a href="http://localhost/profile.php?action=editprofile"><div class="sprite sprite--card" style="top: 2px; margin-right: 15px;"></div>Edit Profile</a></li>
    <li><a href="http://localhost/changepin.php?action=changepin"><div class="sprite sprite--qr" style="top: 2px; margin-right: 15px;"></div>Change PIN</a></li>
    <li><a href="http://localhost/changepassword.php?action=changepassword"><div class="sprite sprite--lock" style="top: 2px; margin-right: 15px;"></div>Change Password</a></li>
    <li><a href="http://localhost/settings.php?action=settings"><div class="sprite sprite--cog" style="top: 2px; margin-right: 15px"></div>Settings</a></li>
</ul>
            </div>
            <div class="row" style="margin-bottom: 1rem;">
            <div class="col-md-3 no-padding-left">
                <div class="container" style="height: 100%;">
                    <div class="row">
                        <div class="col col-md-4">
                            <img src="Bohemia%20-%20Homepage_files/image_002.png">
                        </div>
                        <div class="col col-md-8">
                            <div class="user-detail-row">
                                <strong><span style="color:grey;"> <?php echo $_SESSION["username"] . "<br>"; ?></span></strong>
                            </div>
                            <div class="user-detail-row">
                                <div>Status:</div>
                                <div><span style="color:grey; position:absolute; margin-left:-70px;"><?php echo $_SESSION["account_role"] . "<br>"; ?></span></div>
                            </div>
                            <div class="user-detail-row">
                                <div>Joined:</div>
                                <div><?php echo $_SESSION["dateJoined"] . "<br>"; ?></div>
                            </div>
                                 <div class="user-detail-row">
                                 <div style="display: flex; align-items: center; justify-content: center">
            <?php
            $total_orders = $_SESSION["total_orders"];
            echo "Total Spent:"; ?>
            <div style="
  color: <?php echo $total_orders > 0 ? "green" : "red"; ?>;
  margin-left: 30px;
  ">
              <?php
              $locale = "$";
  
              echo $locale . " " . number_format($total_orders, 2, ".", ",") . "<br>"; ?>
            </div>
          </div>
                                </div>
                                                        <div class="user-balance" style="margin-top: 1em;">
                                <strong>Balances</strong><br>
                                <strong class="balance">0.00000000 BTC</strong> <sup>$0</sup><br><strong class="balance">0.00000000 XMR</strong> <sup style="">$0.00</sup><br>                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <div class="row">
            <div class="col-md-3 sidebar-navigation">
                <div class="container listing-sorting detail-container">
                    <div class="row">
                   
</div>
    <div class="container-header">
        <div class="sprite sprite--diagram"></div>&nbsp; Browse Categories
    </div>
    <div>
    <ul>
                        <li>
                <a href="#">
                    <input type="checkbox" name="catid" value="">
                    <b>Drugs And Chemicals</b>

                    <span class="amount">0</span>
		</a>
		<ul class="sub-categories">
            <li class="secondary-category">
                <a href="#">
                    <input type="checkbox" name="catid" value="26">
                    Stimulants
                    <span class="amount">0</span>
                </a>
            </li>

		
            <li class="secondary-category">
                <a href="#">
                    <input type="checkbox" name="catid" value="27">
                    Psychedelics
                    <span class="amount">0</span>
                </a>
            </li>

		
            <li class="secondary-category">
                <a href="#">
                    <input type="checkbox" name="catid" value="28">
                    Opiates
                    <span class="amount">0</span>
                </a>
            </li>

		
            <li class="secondary-category">
                <a href="#">
                    <input type="checkbox" name="catid" value="29">
                    Pharmacuticials
                    <span class="amount">0</span>
                </a>
            </li>

		</ul>            </li><li>
       
    </div>

</div>



<div class="" style="
border: 1px solid black;
  box-shadow: 0 0 5px 0 rgba(0, 0, 0, 0.1);
  margin: 25px 0;
    margin-top: 25px;
    margin-left: 0px;
  padding: 450px;
  background-color: #fff;
  position: absolute;
  margin-left: 400px;
  margin-top: -913px;">
  <div class="col col-md-4">
                            <img style="position:absolute;
  height: 35%;
  width:30%;
  margin-left: -480px;
  margin-top: -464px;" src="Bohemia%20-%20Homepage_files/image_002.png">
                        </div>
<div>
<h1 style="position: absolute;
  margin-top: -420px;
  margin-left: -160px;"> <?php echo $_SESSION["username"] . "<br>"; ?></h1>
  <label style="position: absolute;
  margin-left: -160px;
  margin-top: -380px;">Status:<?php echo $_SESSION["account_role"] . "<br>"; ?></label>
  <div class="user-detail-row">
                                <div style="position: absolute;
  margin-top: -355px;
  margin-left: -160px;">Joined:</div>
                                <div style="position: absolute;
  margin-top: -355px;
  margin-left: -112px;"><?php echo $_SESSION["dateJoined"] . "<br>"; ?></div>
                            </div>
                            <div class="user-detail-row" style="position:absolute;margin-top:-330px;margin-left:-160px;">
                                 <div style="display: flex; align-items: center; justify-content: center">
            <?php
            $total_orders = $_SESSION["total_orders"];
            echo "Total Spent:"; ?>
            <div style="
  color: <?php echo $total_orders > 0 ? "green" : "red"; ?>;
  margin-left: 30px;
  ">
              <?php
              $locale = "$";
  
              echo $locale . " " . number_format($total_orders, 2, ".", ",") . "<br>"; ?>
            </div>
          </div>
                                </div>
                                                        <div class="user-balance" style="margin-top: 1em;">
                                <strong style="position: absolute;
  margin-left: -150px;
  margin-top: -315px;
">Balances</strong><br>
                                <strong style="margin-left: -150px;
  positioN: absolute;
  margin-top: -300px;" class="balance">0.00000000 BTC</strong> <sup style="position: absolute;
  margin-top: -301px;
  font-size: 15px;">$0</sup><br><strong style="position: absolute;
  margin-top: -285px;
  margin-left: -151px;" class="balance">0.00000000 XMR</strong> <sup style="position: absolute;
  margin-top: -287px;
  font-size: 15px;">$0.00</sup><br>                            </div>
                        </div>
                    </div>
                </div>
            </div>
</div>
</div>























</body>
</html>
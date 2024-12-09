<?php
session_start();
include("db.php");
?>

<!DOCTYPE html>
<html><head>
        <title>Bohemia - New Ticket</title>
                <meta http-equiv="Content-type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" type="text/css" href="Bug-Report_files/flexboxgrid.min.css">
        <link rel="stylesheet" type="text/css" href="Bug-Report_files/fontawesome-all.min.css">
        <link rel="icon" type="image/jpeg" href="img/pentagram.jpg">
        <link rel="stylesheet" type="text/css" href="Bug-Report_files/style.css"><link rel="stylesheet" type="text/css" href="Bug-Report_files/main.css"><link rel="stylesheet" type="text/css" href="Bug-Report_files/responsive.css">        <link rel="stylesheet" type="text/css" href="Bug-Report_files/sprite.css">
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
                                <a href="processed.php">Completed&nbsp; <span class="badge badge-danger right">1</span></a>
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
              
                
                <li class="right fix-gap" style="list-style:none;"><a href="become-a-merchant.php"><b>Become A Merchant</b></a></li>
                
        </div>
    </div>
            <div class="wrapper">
            <div class="row">
                <div class="col-md-3 sidebar-navigation">
                    <ul class="box">
    <li class="title"><h2>Support Tickets</h2></li>
    <li class="compose"><a href="support.php?action=new" class="btn btn-blue btn-block">Open Ticket</a></li>
    <li>
        <a href="support.php">
            Open
                    </a>
    </li>
    <li><a href="support.php?action=archive">Archive</a></li>
</ul>                </div>
                <div class="col-md-9 sidebar-content-right">
                    <form action="" method="POST">
                    
                                                
                        <div class="container">
                            <div class="row form-row align-center">
                                <div class="col-md-3">
                                    <label>Subject</label>
                                </div>
                                <div class="col-md-6">
                                                                        <input type="text" name="subject" class="form-control" placeholder="Enter a subject for your ticket..." value="BUG REPORT">
                                </div>
                            </div>
                            <div class="row form-row">
                                <div class="col-md-3">
                                    <label>Message</label>
                                </div>
                                <div class="col-md-6">
                                                                        <textarea class="form-control" name="message" placeholder="Type in your message here..." rows="15">Bug Report Form Submission

Bug Location:

Bug Description:</textarea>
                                </div>
                            </div>

                            
                            <hr>
                            <div class="row form-row">
                                <div class="col-md-3">
                                    <label>Captcha</label>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <img id="captcha" src="data:image/png;base64,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" alt="CAPTCHA Image">
                                    </div>
                                    <input type="text" class="form-control captcha-text" name="captcha" placeholder="Captcha">
                                </div>
                            </div>
                            
                        </div>
                        <button type="submit" class="btn btn-blue">Create Ticket</button>
                        <input type="hidden" name="create_ticket" value="">
                    </form>
                </div>
            </div>
        </div>
    

</body></html>
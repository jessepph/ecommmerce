<?php
session_start();
include_once("db.php");

?>
<!DOCTYPE html>
<html><head>
        <title>Bohemia - F.A.Q</title>
                <meta http-equiv="Content-type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" type="text/css" href="FAQ_files/flexboxgrid.min.css">
        <link rel="icon" type="image/jpeg" href="img/pentagram.jpg">
        <link rel="stylesheet" type="text/css" href="FAQ_files/fontawesome-all.min.css">
        <link rel="stylesheet" type="text/css" href="FAQ_files/style.css"><link rel="stylesheet" type="text/css" href="FAQ_files/main.css"><link rel="stylesheet" type="text/css" href="FAQ_files/responsive.css">        <link rel="stylesheet" type="text/css" href="FAQ_files/sprite.css">
    </head>
    <body>
    <div class="navigation">
        <div class="wrapper">
            <ul>
                <li class="nav-logo"><a href="http://localhost/bohemia/"><img src="Listings_files/logo_small.png" style="height: 100%;"></a></li>
                <div class="responsive-menu">
                    <li class="menu-toggler"><a href="#">Navigation&nbsp; <div class="sprite sprite--caret" style="float: none; display: inline-block; margin-left:5px;"></div></a></li>
                    <div class="menu-links">
                        <li class="active"><a href="homepage.php">Home</a></li>
                        
                        <li class="dropdown-link dropdown-large ">
                            <a href="orders.php?action=orders" class="dropbtn">
                                Orders
                                                            </a>
                            <div class="dropdown-content right-dropdown">
                                <a href="processing.php?action=orders">Processing&nbsp; <span class="badge badge-secondary right">0</span></a>
                                <a href="dispatched.php?action=orders&amp;do=shipped">Dispatched&nbsp; <span class="badge badge-secondary right">0</span></a>
                                <a href="completed.php?action=orders&amp;do=processed">Completed&nbsp; <span class="badge badge-danger right">1</span></a>
                                <a href="disputed.php?action=orders&amp;do=disputed">Disputed&nbsp; <span class="badge badge-secondary right">0</span></a>
                                <a href="canceled.php?action=orders&amp;do=canceled">Canceled</a>
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
                    <button class="dropbtn" style="margin-top:5px;"><?php echo "" . $_SESSION["username"] . "<br>"; ?>&nbsp; <div class="sprite sprite--caret" style="float: right; top: 1px;"></div></button>
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
            
            <div class="container nopadding">
                <div class="container-header">Market General</div>
                <div class="responsive-table">
                    <table cellspacing="0" cellpadding="0">
                        <tbody>

                
                            <tr>
                                <td>
                                    <strong><a href="faq.php?action=read&amp;id=2">Mirror Verification</a></strong>
                                    <br>
                                    <small class="shadow-text">Instructions on how to ensure that youre accessing Bohemia via a legitimate mirror.</small>
                                </td>
                            </tr>

                
                            <tr>
                                <td>
                                    <strong><a href="faq.php?action=read&amp;id=3">PGP Usage</a></strong>
                                    <br>
                                    <small class="shadow-text">How to protect yourself using PGP.</small>
                                </td>
                            </tr>

                
                            <tr>
                                <td>
                                    <strong><a href="faq.php?action=read&amp;id=4">User Control Panel</a></strong>
                                    <br>
                                    <small class="shadow-text">Instructions relating to sections found in the User CP.</small>
                                </td>
                            </tr>

                
                            <tr>
                                <td>
                                    <strong><a href="faq.php?action=read&amp;id=5">Affiliate Program</a></strong>
                                    <br>
                                    <small class="shadow-text">How to earn via the personal affiliate referral program.</small>
                                </td>
                            </tr>

                
                            <tr>
                                <td>
                                    <strong><a href="faq.php?action=read&amp;id=6">Feedback</a></strong>
                                    <br>
                                    <small class="shadow-text">How feedback works on Bohemia.</small>
                                </td>
                            </tr>

                
                            <tr>
                                <td>
                                    <strong><a href="faq.php?action=read&amp;id=7">Making A Purchase</a></strong>
                                    <br>
                                    <small class="shadow-text">Placing an order for any of the listings available.</small>
                                </td>
                            </tr>

                
                            <tr>
                                <td>
                                    <strong><a href="faq.php?action=read&amp;id=8">Receiving Merchant Alerts</a></strong>
                                    <br>
                                    <small class="shadow-text">What merchant alerts are.</small>
                                </td>
                            </tr>

                
                            <tr>
                                <td>
                                    <strong><a href="faq.php?action=read&amp;id=9">Support Tickets &amp; Bug Reports</a></strong>
                                    <br>
                                    <small class="shadow-text">How to create support tickets and bug reports for the administration.</small>
                                </td>
                            </tr>

                
                            <tr>
                                <td>
                                    <strong><a href="faq.php?action=read&amp;id=10">Becoming A Merchant</a></strong>
                                    <br>
                                    <small class="shadow-text">Start selling on Bohemia today!</small>
                                </td>
                            </tr>

                
                            <tr>
                                <td>
                                    <strong><a href="faq.php?action=read&amp;id=21">Rules</a></strong>
                                    <br>
                                    <small class="shadow-text">The official rules of Bohemia.</small>
                                </td>
                            </tr>

                
                            <tr>
                                <td>
                                    <strong><a href="faq.php?action=read&amp;id=23">Disputes</a></strong>
                                    <br>
                                    <small class="shadow-text">How disputes are initiated and conducted at Bohemia.</small>
                                </td>
                            </tr>

                
                            <tr>
                                <td>
                                    <strong><a href="faq.php?action=read&amp;id=24">2 Factor Authentication</a></strong>
                                    <br>
                                    <small class="shadow-text">Protecting your account using 2FA.</small>
                                </td>
                            </tr>

                

                        </tbody>
                    </table>
                </div>
            </div>

                
            <div class="container nopadding">
                <div class="container-header">Deposits</div>
                <div class="responsive-table">
                    <table cellspacing="0" cellpadding="0">
                        <tbody>

                
                            <tr>
                                <td>
                                    <strong><a href="faq.php?action=read&amp;id=11">Bitcoin</a></strong>
                                    <br>
                                    <small class="shadow-text">How deposits with Bitcoin operate on Bohemia.</small>
                                </td>
                            </tr>

                
                            <tr>
                                <td>
                                    <strong><a href="faq.php?action=read&amp;id=12">Monero</a></strong>
                                    <br>
                                    <small class="shadow-text">How deposits with Monero operate on Bohemia.</small>
                                </td>
                            </tr>

                

                        </tbody>
                    </table>
                </div>
            </div>

                
            <div class="container nopadding">
                <div class="container-header">Withdraws</div>
                <div class="responsive-table">
                    <table cellspacing="0" cellpadding="0">
                        <tbody>

                
                            <tr>
                                <td>
                                    <strong><a href="faq.php?action=read&amp;id=13">Bitcoin</a></strong>
                                    <br>
                                    <small class="shadow-text">Withdrawing Bitcoin from your market wallet.</small>
                                </td>
                            </tr>

                
                            <tr>
                                <td>
                                    <strong><a href="faq.php?action=read&amp;id=14">Monero</a></strong>
                                    <br>
                                    <small class="shadow-text">Withdrawing Monero from your market wallet.</small>
                                </td>
                            </tr>

                

                        </tbody>
                    </table>
                </div>
            </div>

                
            <div class="container nopadding">
                <div class="container-header">Merchant General</div>
                <div class="responsive-table">
                    <table cellspacing="0" cellpadding="0">
                        <tbody>

                
                            <tr>
                                <td>
                                    <strong><a href="faq.php?action=read&amp;id=15">Creating A Listing (Digital)</a></strong>
                                    <br>
                                    <small class="shadow-text">The creation of a digital listing.</small>
                                </td>
                            </tr>

                
                            <tr>
                                <td>
                                    <strong><a href="faq.php?action=read&amp;id=16">Creating A Listing (Physical)</a></strong>
                                    <br>
                                    <small class="shadow-text">The creation of a physical listing.</small>
                                </td>
                            </tr>

                
                            <tr>
                                <td>
                                    <strong><a href="faq.php?action=read&amp;id=17">Listing Promotions</a></strong>
                                    <br>
                                    <small class="shadow-text">Available listing promotions and how they work.</small>
                                </td>
                            </tr>

                
                            <tr>
                                <td>
                                    <strong><a href="faq.php?action=read&amp;id=18">Featured Listings</a></strong>
                                    <br>
                                    <small class="shadow-text">How the featured listings system works.</small>
                                </td>
                            </tr>

                
                            <tr>
                                <td>
                                    <strong><a href="faq.php?action=read&amp;id=19">Mass Messages</a></strong>
                                    <br>
                                    <small class="shadow-text">Broadcast messages to your client base.</small>
                                </td>
                            </tr>

                
                            <tr>
                                <td>
                                    <strong><a href="faq.php?action=read&amp;id=20">Managing Sales &amp; Disputes</a></strong>
                                    <br>
                                    <small class="shadow-text">How to manage sales and disputes.</small>
                                </td>
                            </tr>

                
                            <tr>
                                <td>
                                    <strong><a href="faq.php?action=read&amp;id=22">Prohibited Listings</a></strong>
                                    <br>
                                    <small class="shadow-text">A list of banned goods &amp; services on Bohemia.</small>
                                </td>
                            </tr>

                

                        </tbody>
                    </table>
                </div>
            </div>

                
            <div class="container nopadding">
                <div class="container-header">Cannabia</div>
                <div class="responsive-table">
                    <table cellspacing="0" cellpadding="0">
                        <tbody>

                
                            <tr>
                                <td>
                                    <strong><a href="faq.php?action=read&amp;id=25">Authentication</a></strong>
                                    <br>
                                    <small class="shadow-text">Authentication: How-To</small>
                                </td>
                            </tr>

                
                            <tr>
                                <td>
                                    <strong><a href="faq.php?action=read&amp;id=26">PGP Key</a></strong>
                                    <br>
                                    <small class="shadow-text">PGP Key: How-To</small>
                                </td>
                            </tr>

                

                        </tbody>
                    </table>
                </div>
            </div>

                        </div>
    
</body></html>
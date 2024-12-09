<?php
session_start();
?>


<!DOCTYPE html>
<html><head>
    <title>Bohemia - Homepage</title>
            <meta http-equiv="Content-type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="stylesheet" type="text/css" href="Bohemia%20-%20Homepage_files/flexboxgrid.min.css">
        <link rel="stylesheet" type="text/css" href="Bohemia%20-%20Homepage_files/fontawesome-all.min.css">
        <link rel="stylesheet" type="text/css" href="Bohemia%20-%20Homepage_files/style.css">
        <link rel="stylesheet" type="text/css" href="Bohemia%20-%20Homepage_files/main.css">
        <link rel="stylesheet" type="text/css" href="Bohemia%20-%20Homepage_files/responsive.css">        
        <link rel="stylesheet" type="text/css" href="Bohemia%20-%20Homepage_files/sprite.css">
        <link rel="icon" type="image/jpeg" href="img/pentagram.jpg">
        <!--<link rel="stylesheet" href="style.css">-->
</head>

<body>
    <div class="navigation">
        <div class="wrapper">
            <ul>
                <li class="nav-logo"><a href="http://2fzhe7csdmsl6tqfevrabnctewlh3ynro7zlkd5ie4xlai4mahr2fqid.onion/"><img src="Bohemia%20-%20Homepage_files/logo_small.png" style="height: 100%;"></a></li>
                <div class="responsive-menu">
                    <li class="menu-toggler"><a href="#">Navigation&nbsp; <div class="sprite sprite--caret" style="float: none; display: inline-block; margin-left:5px;"></div></a></li>
                    <div class="menu-links">
                        <li><a href="homepage.php">Home</a></li>
                        
                        <li class="dropdown-link dropdown-large ">
                            <a href="http://2fzhe7csdmsl6tqfevrabnctewlh3ynro7zlkd5ie4xlai4mahr2fqid.onion/usercp.php?action=orders" class="dropbtn">
                                Orders
                                                            </a>
                            <div class="dropdown-content right-dropdown">
                                <a href="http://2fzhe7csdmsl6tqfevrabnctewlh3ynro7zlkd5ie4xlai4mahr2fqid.onion/usercp.php?action=orders">Processing&nbsp; <span class="badge badge-secondary right">0</span></a>
                                <a href="http://2fzhe7csdmsl6tqfevrabnctewlh3ynro7zlkd5ie4xlai4mahr2fqid.onion/usercp.php?action=orders&amp;do=shipped">Dispatched&nbsp; <span class="badge badge-danger right">1</span></a>
                                <a href="http://2fzhe7csdmsl6tqfevrabnctewlh3ynro7zlkd5ie4xlai4mahr2fqid.onion/usercp.php?action=orders&amp;do=processed">Completed&nbsp; <span class="badge badge-danger right">1</span></a>
                                <a href="http://2fzhe7csdmsl6tqfevrabnctewlh3ynro7zlkd5ie4xlai4mahr2fqid.onion/usercp.php?action=orders&amp;do=disputed">Disputed&nbsp; <span class="badge badge-secondary right">0</span></a>
                                <a href="http://2fzhe7csdmsl6tqfevrabnctewlh3ynro7zlkd5ie4xlai4mahr2fqid.onion/usercp.php?action=orders&amp;do=canceled">Cancelled</a>
                            </div>
                        </li>

                        
                        <li><a href="http://2fzhe7csdmsl6tqfevrabnctewlh3ynro7zlkd5ie4xlai4mahr2fqid.onion/listings.php">Listings</a></li>
                        <li class="dropdown-link dropdown-large ">
                            <a href="http://2fzhe7csdmsl6tqfevrabnctewlh3ynro7zlkd5ie4xlai4mahr2fqid.onion/messages.php" class="dropbtn">
                                Messages&nbsp;
                                <span class="badge badge-secondary">0</span>                            </a>
                            <div class="dropdown-content right-dropdown">
                                <a href="http://2fzhe7csdmsl6tqfevrabnctewlh3ynro7zlkd5ie4xlai4mahr2fqid.onion/messages.php?action=compose">Compose Message</a>
                                <a href="http://2fzhe7csdmsl6tqfevrabnctewlh3ynro7zlkd5ie4xlai4mahr2fqid.onion/messages.php">Inbox</a>
                                <a href="http://2fzhe7csdmsl6tqfevrabnctewlh3ynro7zlkd5ie4xlai4mahr2fqid.onion/messages.php?action=sent">Sent Items</a>
                            </div>
                        </li>
			<li class="dropdown-link dropdown-large">
			    <a href="http://2fzhe7csdmsl6tqfevrabnctewlh3ynro7zlkd5ie4xlai4mahr2fqid.onion/usercp.php?action=wallet" class="dropbtn">Wallet</a>
			    <div class="dropdown-content right-dropdown">
                                <a href="http://2fzhe7csdmsl6tqfevrabnctewlh3ynro7zlkd5ie4xlai4mahr2fqid.onion/usercp.php?action=exchange">Exchange</a>
                            </div>
			</li>
                        <li class="dropdown-link dropdown-large ">
                            <a href="#" class="dropbtn">
                                Support
                                                            </a>
                            <div class="dropdown-content right-dropdown">
                                <a href="http://2fzhe7csdmsl6tqfevrabnctewlh3ynro7zlkd5ie4xlai4mahr2fqid.onion/faq.php">F.A.Q</a>
                                <a href="http://2fzhe7csdmsl6tqfevrabnctewlh3ynro7zlkd5ie4xlai4mahr2fqid.onion/support.php">
                                    Support Tickets
                                                                    </a>
                                <a href="http://2fzhe7csdmsl6tqfevrabnctewlh3ynro7zlkd5ie4xlai4mahr2fqid.onion/support?action=new&amp;do=bugreport">
                                    Report Bug
                                </a>
                            </div>
                        </li>
                    </div>
                </div>
              
              
              
               
                <li class="dropdown-link user-nav right fix-gap">
                <div class="dropdown">
              <button class="dropbtn"><?php echo "" . $_SESSION["username"] . "<br>"; ?></button>
                   
                 
                    <div class="dropdown-content">
                       
                        <div class="user-balance">
                            <span class="shadow-text">Balances</span><br>
                            <span class="balance"></span><sup>0.00000000 BTC</sup><br><span class="balance"></span><sup>0.00000000 XMR</sup><br>
                        </div>
                                                <a href="http://2fzhe7csdmsl6tqfevrabnctewlh3ynro7zlkd5ie4xlai4mahr2fqid.onion/profile.php?id=60Agent">My Profile</a>

                        <a href="http://2fzhe7csdmsl6tqfevrabnctewlh3ynro7zlkd5ie4xlai4mahr2fqid.onion/theme.php">Night Mode</a>
                        <a href="http://2fzhe7csdmsl6tqfevrabnctewlh3ynro7zlkd5ie4xlai4mahr2fqid.onion/usercp.php">User CP</a>
                        <a href="logout.php">Logout</a>
                    </div>
                </li>
                
                <li class="right shopping-cart-link ">
                  <a href="http://2fzhe7csdmsl6tqfevrabnctewlh3ynro7zlkd5ie4xlai4mahr2fqid.onion/cart.php">
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
                    ">6</span>               
                    
                                            </a>
                </li>
                <li class="right ">
                    <a href="http://2fzhe7csdmsl6tqfevrabnctewlh3ynro7zlkd5ie4xlai4mahr2fqid.onion/notifications.php">
                        <img src="alert-bell.png" style="    
                        width: 20px;
                        height: 25px;
                        display: inline-block;
                        margin-top: 20px;">
                        
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
                        ">6</span>                    </a>
                </li>
                
                <li class="active" style="float:right;"><a href="becoming-a-merchant.php"><b>Become A Merchant</b></a></li>

                                        
            </ul>
        </div>
    </div>
       

            <div class="wrapper">
            <div class="container nopadding">
                <div class="container-header">Becoming A Merchant</div>
                <div class="container-content">
                    Becoming a merchant with Bohemia is a seamless 
process. You can find the "Become A Merchant" button on the right-hand 
side of the top toolbar of the platform. Clicking this text will present
 you with the progressive information pages relating to the criteria 
expected to be met as a merchant with Bohemia, along with terms &amp; 
conditions and the extensive list of features you can expect to have at 
your disposal. Details are also provided relating to the merchant bond 
one-off payment and instructions on how to apply for a bond waiver for 
those of you who are established merchants migrating from elsewhere.<br>
<br>
Upon accepting the terms &amp; conditions of becoming a merchant, the 
relevant bond will be deducted from your account balanceand your new 
role will be initiated immediately.<br>
<br>
IMPORTANT: Already an established vendor? Select the option to apply for
 a free vendor bond on the vendor bond payment page. Ensure that you 
have attached your PGP key correctly to your account, and matches the 
records which can be verified on the Recon directory. Upon verification 
you will receive the merchant role on Bohemia free of charge.           
     </div>
            </div>
        </div>
    
</body></html>
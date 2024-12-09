<!DOCTYPE html>
<html><head>
        <title>Bohemia - Sent Messages</title>
                <meta http-equiv="Content-type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" type="text/css" href="Sent-Messages_files/flexboxgrid.min.css">
        <link rel="icon" type="image/jpeg" href="img/pentagram.jpg">
        <link rel="stylesheet" type="text/css" href="Sent-Messages_files/fontawesome-all.min.css">
        <link rel="stylesheet" type="text/css" href="Sent-Messages_files/style.css"><link rel="stylesheet" type="text/css" href="Sent-Messages_files/main.css"><link rel="stylesheet" type="text/css" href="Sent-Messages_files/responsive.css">        <link rel="stylesheet" type="text/css" href="Sent-Messages_files/sprite.css">
    </head>
    <body>
        <div class="navigation">
        <div class="wrapper">
            <ul>
                <li class="nav-logo"><a href="homepage.php"><img src="Sent-Messages_files/logo_small.png" style="height: 100%;"></a></li>
                <div class="responsive-menu">
                    <li class="menu-toggler"><a href="homepage.php">Navigation&nbsp; <div class="sprite sprite--caret" style="float: none; display: inline-block; margin-left:5px;"></div></a></li>
                    <div class="menu-links">
                        <li><a href="">Home</a></li>
                        
                        <li class="dropdown-link dropdown-large ">
                            <a href="usercp.php?action=orders" class="dropbtn">
                                Orders
                                                            </a>
                            <div class="dropdown-content right-dropdown">
                                <a href="usercp.php?action=orders">Processing&nbsp; <span class="badge badge-secondary right">0</span></a>
                                <a href="usercp.php?action=orders&amp;do=shipped">Dispatched&nbsp; <span class="badge badge-secondary right">0</span></a>
                                <a href="usercp.php?action=orders&amp;do=processed">Completed&nbsp; <span class="badge badge-danger right">1</span></a>
                                <a href="usercp.php?action=orders&amp;do=disputed">Disputed&nbsp; <span class="badge badge-danger right">1</span></a>
                                <a href="usercp.php?action=orders&amp;do=canceled">Cancelled</a>
                            </div>
                        </li>

                        
                        <li><a href="listings.php">Listings</a></li>
                        <li class="dropdown-link dropdown-large  active">
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
                    <button class="dropbtn">60Agent&nbsp; <div class="sprite sprite--caret" style="float: right; top: 1px;"></div></button>
                    <div class="dropdown-content">
                        
                        <div class="user-balance">
                            <span class="shadow-text">Balances</span><br>
                            <span class="balance">$</span>4.72 <sup>0.00016300 BTC</sup><br><span class="balance">$</span>0.23 <sup>0.00141754 XMR</sup><br>
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
                <li class="right ">
                    <a href="notifications.php">
                        <div class="sprite sprite--bell" style="top: 39%;"></div>
                        &nbsp;<span class="badge badge-danger">6</span>                    </a>
                </li>
                
                <li class="right fix-gap "><a href="become-a-merchant.php"><b>Become A Merchant</b></a></li>

                                        
            </ul>
        </div>
    </div>
            <div class="wrapper">
                <div class="row">
                    <div class="col-md-3 sidebar-navigation">
                        <ul class="box">
    <li class="title"><h2>Messages</h2></li>
    <li class="compose"><a href="messages.php?action=compose" class="btn btn-blue btn-block">Compose A Message</a></li>
    <li><a href="messages.php"><div class="sprite sprite--inbox" style="float: none;display: inline-block; margin-left:5px;; margin-right: 15px;"></div>Inbox</a></li>
    <li><a href="messages.php?action=sent" class="active"><div class="sprite sprite--reply" style="float: none;display: inline-block; margin-left:5px;; margin-right: 15px;"></div>Sent</a></li>
</ul>
                    </div>
                    <div class="col-md-9 sidebar-content-right">
                                                                        <div class="container nopadding">
                            <form action="" method="post">
                                <div class="responsive-table">
                                    <table class="message-table" cellspacing="0" cellpadding="0">
                                        <tbody><tr>
                                            <th></th>
                                            <th>Subject</th>
                                            <th>Started by</th>
                                            <th>Created</th>
                                            <th>Last Reply</th>
                                        </tr>
                                                                                    <tr class="read">
                                                <td><input type="checkbox" name="messages[]" value="7445289a-2e39-11ee-aa85-d05099fcf7bb"></td>
                                                <td><a href="messages.php?action=read&amp;id=7445289a-2e39-11ee-aa85-d05099fcf7bb&amp;from=sent">Please confirm address for recent order....</a></td>
                                                <td><a href="profile.php?id=60Agent">60Agent</a></td>
                                                <td>29th July 5:57 PM</td>
                                                <td><a href="profile.php?id=60Agent">60Agent</a></td>
                                            </tr>
                                                                                    <tr class="read">
                                                <td><input type="checkbox" name="messages[]" value="d9ed0e08-1aab-11ee-beb5-d05099fcf7bb"></td>
                                                <td><a href="messages.php?action=read&amp;id=d9ed0e08-1aab-11ee-beb5-d05099fcf7bb&amp;from=sent">Can I get a pic with timestamp for 28g blue nectar</a></td>
                                                <td><a href="profile.php?id=60Agent">60Agent</a></td>
                                                <td>4th July 8:46 PM</td>
                                                <td><a href="profile.php?id=60Agent">60Agent</a></td>
                                            </tr>
                                                                                    <tr class="read">
                                                <td><input type="checkbox" name="messages[]" value="f84854d6-1486-11ee-beb5-d05099fcf7bb"></td>
                                                <td><a href="messages.php?action=read&amp;id=f84854d6-1486-11ee-beb5-d05099fcf7bb&amp;from=sent">Package for 60Agent</a></td>
                                                <td><a href="profile.php?id=60Agent">60Agent</a></td>
                                                <td>27th June 1:07 AM</td>
                                                <td><a href="profile.php?id=60Agent">60Agent</a></td>
                                            </tr>
                                                                                                                        </tbody></table>
                                </div>
                                <div class="form-inline form-padding">
                                    <div class="form-group">
                                        <div><label>Do with</label></div>
                                        <select class="form-control" name="do_messages">
                                            <option value="selected" selected="selected">Selected messages</option>
                                            <option value="all">All messages</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <div><label>Choose action</label></div>
                                        <select class="form-control" name="do_action">
                                            <option value="markread" selected="selected">Mark as read</option>
                                            <option value="markunread">Mark as unread</option>
                                            <option value="pin">Pin message</option>
                                            <option value="unpin">Unpin message</option>
                                            <option value="delete">Delete</option>
                                        </select>
                                    </div>
                                    <div class="form-group" style="margin-bottom: 0; margin-top: 14px;">
                                        <button type="submit" class="btn btn-larger btn-blue" style="margin-bottom: 2px;">Apply</button>
                                        <input type="hidden" name="do_message_action" value="">
                                    </div>
                                </div>
                            </form>
                        </div>
                                            </div>
                </div>
        </div>
    

</body></html>
<?php
include("myfunctions.php");
session_start();
require("db.php");

// Database connection parameters
$servername = "localhost";
$dbusername = "root";
$password = "CoheedAndCambria666!";
$dbname = "market";
$port = 888;

// Create a new database connection
$conn = new mysqli($servername, $dbusername, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Retrieve the user's role from the database
$user_role = 'Buyer'; // Default value

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    
    // Prepare and execute the query to get the user's role
    $query = "SELECT account_role FROM register WHERE username = ?";
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($user_role);
        $stmt->fetch();
        $stmt->close();
    } else {
        die("Database query preparation failed: " . $conn->error);
    }
}

// Check user role and conditionally show C Panel
$showControlPanel = ($user_role !== 'Buyer');

// Get the category_id from the URL
$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;

// Function to get category name
function getCategoryName2($conn, $category_id) {
    $query = "SELECT name FROM categories WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $category = $result->fetch_assoc();
    $stmt->close();
    return $category ? $category['name'] : 'Unknown';
}

// Function to get vendor info
function getVendorInfo2($conn, $vendorName) {
    $query = "SELECT username, vendor_rating, total_orders, time_seen, trust_level, level FROM register WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $vendorName);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc() ?? false;
}

// Function to retrieve average Bitcoin price in USD from multiple exchanges
function getAverageBitcoinPriceUSD2() {
    $api_endpoints = [
        'https://api.coindesk.com/v1/bpi/currentprice.json',
        'https://api.blockchain.com/v3/exchange/tickers/BTC-USD',
        'https://api.coinbase.com/v2/prices/spot?currency=USD'
    ];

    $bitcoin_prices = [];

    foreach ($api_endpoints as $endpoint) {
        $response = @file_get_contents($endpoint);

        if ($response !== false) {
            $data = json_decode($response, true);
            $price = null;

            switch ($endpoint) {
                case 'https://api.coindesk.com/v1/bpi/currentprice.json':
                    $price = $data['bpi']['USD']['rate_float'] ?? null;
                    break;
                case 'https://api.blockchain.com/v3/exchange/tickers/BTC-USD':
                    $price = $data['last_trade_price'] ?? null;
                    break;
                case 'https://api.coinbase.com/v2/prices/spot?currency=USD':
                    $price = $data['data']['amount'] ?? null;
                    break;
            }

            if ($price !== null) {
                $bitcoin_prices[] = floatval($price);
            }
        }
    }

    return count($bitcoin_prices) > 0 ? array_sum($bitcoin_prices) / count($bitcoin_prices) : 0;
}

// Function to convert USD price to Bitcoin
function convertToBitcoin2($usdPrice) {
    $bitcoinPriceUSD = getAverageBitcoinPriceUSD2();

    return $bitcoinPriceUSD !== 0 ? $usdPrice / $bitcoinPriceUSD : 0;
}

// Function to format currency
function formatCurrency2($amount) {
    return number_format($amount, 2);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asmodeus - Listings</title>
    <link rel="stylesheet" type="text/css" href="Listings_files/flexboxgrid.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Font Awesome CSS -->
    <link rel="stylesheet" type="text/css" href="Listings_files/style.css">
    <link rel="stylesheet" type="text/css" href="Listings_files/main.css">
    <link rel="stylesheet" type="text/css" href="Listings_files/responsive.css">
    <link rel="stylesheet" type="text/css" href="product-view.css">
    <link rel="stylesheet" type="text/css" href="product-view-stylesheet.css">
    <link rel="stylesheet" type="text/css" href="sprite.css">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/jpeg" href="img/pentagram.jpg">

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Function to handle redirection based on screen width
            function handleRedirection() {
                if (window.innerWidth <= 390) {
                    if (!localStorage.getItem('redirectedToMobile')) {
                        localStorage.setItem('redirectedToMobile', 'true');
                        window.location.href = 'product-view-mobile.php';
                    }
                } else {
                    if (localStorage.getItem('redirectedToMobile')) {
                        localStorage.removeItem('redirectedToMobile');
                        window.location.href = 'product-view.php';
                    }
                }
            }

            // Handle redirection on page load
            handleRedirection();

            // Handle redirection on window resize
            window.addEventListener('resize', handleRedirection);
        });
    </script>
    
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .navigation {
            background-color: #333;
            color: #fff;
        }
        .navigation .wrapper {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
        }
        .navigation .logo img {
            height: 40px;
        }
        .navigation ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
        }
        .navigation ul li {
            margin: 0;
        }
        .navigation ul li a {
            color: #fff;
            text-decoration: none;
            padding: 15px;
            display: block;
        }
        .menu-links {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
        }
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #333;
            min-width: 160px;
            z-index: 1;
            right: 0;
        }
        .dropdown-content a {
            color: #fff;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }
        .dropdown-content a:hover {
            background-color: #575757;
        }
        .dropdown-link:hover .dropdown-content {
            display: block;
        }
        @media (max-width: 768px) {
            .navigation ul {
                flex-direction: column;
                width: 100%;
            }
            .navigation ul li {
                text-align: center;
                width: 100%;
            }
            .menu-links {
                flex-direction: column;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="navigation">
        <div class="wrapper">
            <div class="logo">
                <a href="homepage.php"><img src="Listings_files/logo_small.png" alt="Logo"></a>
            </div>
            <ul class="menu-links">
                <li style="background-color:black;"><a href="homepage.php">Home</a></li>
                <li style="background-color:black;" class="dropdown-link">
                    <a href="orders.php?action=orders">Orders</a>
                    <div class="dropdown-content">
                        <a href="processing.php">Processing&nbsp; <span class="badge badge-secondary right">0</span></a>
                        <a href="dispatched.php">Dispatched&nbsp; <span class="badge badge-secondary right">0</span></a>
                        <a href="completed.php">Completed&nbsp; <span class="badge badge-danger right">1</span></a>
                        <a href="disputed.php">Disputed&nbsp; <span class="badge badge-secondary right">0</span></a>
                        <a href="canceled.php">Canceled</a>
                    </div>
                </li>
                <li style="background-color:black;"><a href="listings.php">Listings</a></li>
                <li style="background-color:black;" class="dropdown-link">
                    <a href="messages.php">Messages&nbsp; <span class="badge badge-secondary">0</span></a>
                    <div class="dropdown-content">
                        <a href="compose-message.php?action=compose">Compose Message</a>
                        <a href="pm_inbox.php">Inbox</a>
                        <a href="message-sent.php">Sent Items</a>
                    </div>
                </li>
                <li style="background-color:black;" class="dropdown-link">
                    <a href="wallet.php?action=wallet">Wallet</a>
                    <div class="dropdown-content">
                        <a href="exchange.php?action=exchange">Exchange</a>
                    </div>
                </li>
                <li style="background-color:black;" class="dropdown-link">
                    <a href="bug-report.php">Support</a>
                    <div class="dropdown-content">
                        <a href="faq.php">F.A.Q</a>
                        <a href="support-tickets-and-bug-reports.php">Support Tickets</a>
                        <a href="bug-report.php">Report Bug</a>
                    </div>
                </li>
                <?php if ($showControlPanel): ?>
                    <li style="background-color:black;" class="dropdown-link">
                        <a href="control-panel.php">C Panel</a>
                        <div class="dropdown-content">
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
                <li style="background-color:black;"><a href="become-a-merchant.php"><b>Become A Merchant</b></a></li>
                <li style="background-color:black;" class="user-nav dropdown-link">
                    <a href="#" class="dropbtn">
                        <?php echo $_SESSION["username"]; ?>&nbsp;
                        <div class="sprite sprite--caret"></div>
                    </a>
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
                <li style="background-color:black;" class="shopping-cart-link">
                    <a href="cart.php">
                        <img src="cart.png" alt="Cart" style="width: 20px; height: 25px;">
                        &nbsp;<span class="badge badge-danger">0</span>
                    </a>
                </li>
                <li style="background-color:black;" class="shopping-cart-link">
                    <a href="cart.php">
                        <img src="alert-bell.png" alt="Alerts" style="width: 20px; height: 25px;">
                        &nbsp;<span class="badge badge-danger">0</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="wrapper">
        <div class="row" style="">
            <div style="display: block; width: 100%; margin-bottom: 25px; padding-left: 0; margin-top: 710px;" class="col-md-3 sidebar-navigation">
                <div class="container listing-sorting detail-container" style="height: auto;">
        <div class="container-header">
            <div class="sprite sprite--diagram"></div>&nbsp; Browse Categories
        </div>
        <div style="overflow:visible;">
            <ul>
                <?php
                // SQL query to count products
$sql = "SELECT COUNT(*) AS product_count FROM products";
$result = $con->query($sql);

if ($result->num_rows > 0) {
    // Output data of each row
    while($row = $result->fetch_assoc()) {
        // Display the product count inside the span with class "amount"
        echo '<a href="http://wbxnudwhsfpta4ljzwm7mhkzph4ntpsvidubwr2gptfqyg4fohyrv7ad.onion/bohemia/listings-mobile.php"><input type="checkbox" name="catid" value="">
                    <b>Drugs and Chemicals</b>
                    <span class="amount" style="display: block;
  float: right;
  padding: 5px; 9px;
  font-weight: 600;
  min-width: 30px;
  text-align: center;
  margin-top: -25px;
  margin-left:250px;
  position:absolute;
  color: #4B7AA2;
  background: rgba(75, 122, 162, 0.3);
  border-radius: 15px;">' . $row["product_count"] . '</span></a>';
    }
} else {
    echo "0 results";
}
                // SQL query to get categories and their product counts
                $query = "SELECT c.id AS category_id, c.name AS category_name, COUNT(p.id) AS product_count
                          FROM categories c
                          LEFT JOIN products p ON c.id = p.category_id
                          GROUP BY c.id, c.name";
                $result = $con->query($query);

                if ($result->num_rows > 0) {
                    // Output data for each category
                    while ($row = $result->fetch_assoc()) {
                        echo '<li>
                            <a href="http://wbxnudwhsfpta4ljzwm7mhkzph4ntpsvidubwr2gptfqyg4fohyrv7ad.onion/bohemia/listings-mobile.php?category_id=' . $row['category_id'] . '">
                                <input type="checkbox" name="catid" value="' . $row['category_id'] . '">
                                <b>' . htmlspecialchars($row['category_name']) . '</b>
                                <span class="amount">' . $row['product_count'] . '</span>
                            </a>
                        </li>';
                    }
                } else {
                    echo '<li>No categories found</li>';
                }
                ?>
            </ul>
        </div>
    </div>

                <div class="container listing-sorting detail-container" style="margin-top:15px;">
    <div class="container-header">
        <div class="sprite sprite--search"></div>&nbsp; Advanced Search
    </div>
    <div class="container-content">
        <form action="listings.php" method="GET">
            <div class="form-group">
                <div><label>Search by Keyword / Merchant Name</label></div>
                <input type="text" class="form-control" name="query">
	    </div>
	    <div class="form-group">
                <div><label>Sort by</label></div>
                <select class="form-control" name="sortby">
                    <option value="mosthighest" selected="selected">Highest to lowest price</option>
                    <option value="cheapest">Lowest to highest price</option>
                    <option value="oldest">Oldest to newest</option>
                    <option value="newest">Newest to oldest</option>
                    <option value="mostrated">Highest rated</option>
                </select>
            </div>
            <div class="form-inline">
                    <div><label>Price range</label></div>
                <div class="form-group" style="width: 45%;">
                    <input type="number" name="priceFrom" step="0.01" class="form-control" placeholder="Price from">
                </div>
                <div class="form-group" style="float: right; width: 45%;">
                    <input type="number" name="priceTo" step="0.01" class="form-control" placeholder="Price to">
                </div>
            </div>
            <div class="form-group">
                <div><label>Ships from</label></div>
                <select class="form-control" name="shipFrom">
                    <option selected="selected"></option>
                    <option value="1">Afghanistan</option><option value="2">Albania</option><option value="3">Algeria</option><option value="4">American Samoa</option><option value="5">Andorra</option><option value="6">Angola</option><option value="7">Anguilla</option><option value="8">Antarctica</option><option value="9">Antigua and Barbuda</option><option value="10">Argentina</option><option value="11">Armenia</option><option value="12">Aruba</option><option value="13">Australia</option><option value="14">Austria</option><option value="15">Azerbaijan</option><option value="16">Bahamas</option><option value="17">Bahrain</option><option value="18">Bangladesh</option><option value="19">Barbados</option><option value="20">Belarus</option><option value="21">Belgium</option><option value="22">Belize</option><option value="23">Benin</option><option value="24">Bermuda</option><option value="25">Bhutan</option><option value="26">Bolivia</option><option value="27">Bosnia and Herzegovina</option><option value="28">Botswana</option><option value="29">Bouvet Island</option><option value="30">Brazil</option><option value="31">British Indian Ocean Territory</option><option value="32">Brunei Darussalam</option><option value="33">Bulgaria</option><option value="34">Burkina Faso</option><option value="35">Burundi</option><option value="36">Cambodia</option><option value="37">Cameroon</option><option value="38">Canada</option><option value="39">Cape Verde</option><option value="40">Cayman Islands</option><option value="41">Central African Republic</option><option value="42">Chad</option><option value="43">Chile</option><option value="44">China</option><option value="45">Christmas Island</option><option value="46">Cocos (Keeling) Islands</option><option value="47">Colombia</option><option value="48">Comoros</option><option value="51">Cook Islands</option><option value="52">Costa Rica</option><option value="53">Croatia (Hrvatska)</option><option value="54">Cuba</option><option value="55">Cyprus</option><option value="56">Czech Republic</option><option value="49">Democratic Republic of the Congo</option><option value="57">Denmark</option><option value="58">Djibouti</option><option value="59">Dominica</option><option value="60">Dominican Republic</option><option value="61">East Timor</option><option value="62">Ecuador</option><option value="63">Egypt</option><option value="64">El Salvador</option><option value="65">Equatorial Guinea</option><option value="66">Eritrea</option><option value="67">Estonia</option><option value="68">Ethiopia</option><option value="69">Falkland Islands (Malvinas)</option><option value="70">Faroe Islands</option><option value="71">Fiji</option><option value="72">Finland</option><option value="73">France</option><option value="74">France, Metropolitan</option><option value="75">French Guiana</option><option value="76">French Polynesia</option><option value="77">French Southern Territories</option><option value="78">Gabon</option><option value="79">Gambia</option><option value="80">Georgia</option><option value="81">Germany</option><option value="82">Ghana</option><option value="83">Gibraltar</option><option value="85">Greece</option><option value="86">Greenland</option><option value="87">Grenada</option><option value="88">Guadeloupe</option><option value="89">Guam</option><option value="90">Guatemala</option><option value="84">Guernsey</option><option value="91">Guinea</option><option value="92">Guinea-Bissau</option><option value="93">Guyana</option><option value="94">Haiti</option><option value="95">Heard and Mc Donald Islands</option><option value="96">Honduras</option><option value="97">Hong Kong</option><option value="98">Hungary</option><option value="99">Iceland</option><option value="100">India</option><option value="102">Indonesia</option><option value="103">Iran (Islamic Republic of)</option><option value="104">Iraq</option><option value="105">Ireland</option><option value="101">Isle of Man</option><option value="106">Israel</option><option value="107">Italy</option><option value="108">Ivory Coast</option><option value="110">Jamaica</option><option value="111">Japan</option><option value="109">Jersey</option><option value="112">Jordan</option><option value="113">Kazakhstan</option><option value="114">Kenya</option><option value="115">Kiribati</option><option value="116">Korea, Democratic People's Republic of</option><option value="117">Korea, Republic of</option><option value="118">Kosovo</option><option value="119">Kuwait</option><option value="120">Kyrgyzstan</option><option value="121">Lao People's Democratic Republic</option><option value="122">Latvia</option><option value="123">Lebanon</option><option value="124">Lesotho</option><option value="125">Liberia</option><option value="126">Libyan Arab Jamahiriya</option><option value="127">Liechtenstein</option><option value="128">Lithuania</option><option value="129">Luxembourg</option><option value="130">Macau</option><option value="132">Madagascar</option><option value="133">Malawi</option><option value="134">Malaysia</option><option value="135">Maldives</option><option value="136">Mali</option><option value="137">Malta</option><option value="138">Marshall Islands</option><option value="139">Martinique</option><option value="140">Mauritania</option><option value="141">Mauritius</option><option value="142">Mayotte</option><option value="143">Mexico</option><option value="144">Micronesia, Federated States of</option><option value="145">Moldova, Republic of</option><option value="146">Monaco</option><option value="147">Mongolia</option><option value="148">Montenegro</option><option value="149">Montserrat</option><option value="150">Morocco</option><option value="151">Mozambique</option><option value="152">Myanmar</option><option value="153">Namibia</option><option value="154">Nauru</option><option value="155">Nepal</option><option value="156">Netherlands</option><option value="157">Netherlands Antilles</option><option value="158">New Caledonia</option><option value="159">New Zealand</option><option value="160">Nicaragua</option><option value="161">Niger</option><option value="162">Nigeria</option><option value="163">Niue</option><option value="164">Norfolk Island</option><option value="131">North Macedonia</option><option value="165">Northern Mariana Islands</option><option value="166">Norway</option><option value="167">Oman</option><option value="168">Pakistan</option><option value="169">Palau</option><option value="170">Palestine</option><option value="171">Panama</option><option value="172">Papua New Guinea</option><option value="173">Paraguay</option><option value="174">Peru</option><option value="175">Philippines</option><option value="176">Pitcairn</option><option value="177">Poland</option><option value="178">Portugal</option><option value="179">Puerto Rico</option><option value="180">Qatar</option><option value="50">Republic of Congo</option><option value="181">Reunion</option><option value="182">Romania</option><option value="183">Russian Federation</option><option value="184">Rwanda</option><option value="185">Saint Kitts and Nevis</option><option value="186">Saint Lucia</option><option value="187">Saint Vincent and the Grenadines</option><option value="188">Samoa</option><option value="189">San Marino</option><option value="190">Sao Tome and Principe</option><option value="191">Saudi Arabia</option><option value="192">Senegal</option><option value="193">Serbia</option><option value="194">Seychelles</option><option value="195">Sierra Leone</option><option value="196">Singapore</option><option value="197">Slovakia</option><option value="198">Slovenia</option><option value="199">Solomon Islands</option><option value="200">Somalia</option><option value="201">South Africa</option><option value="202">South Georgia South Sandwich Islands</option><option value="203">South Sudan</option><option value="204">Spain</option><option value="205">Sri Lanka</option><option value="206">St. Helena</option><option value="207">St. Pierre and Miquelon</option><option value="208">Sudan</option><option value="209">Suriname</option><option value="210">Svalbard and Jan Mayen Islands</option><option value="211">Swaziland</option><option value="212">Sweden</option><option value="213">Switzerland</option><option value="214">Syrian Arab Republic</option><option value="215">Taiwan</option><option value="216">Tajikistan</option><option value="217">Tanzania, United Republic of</option><option value="218">Thailand</option><option value="219">Togo</option><option value="220">Tokelau</option><option value="221">Tonga</option><option value="222">Trinidad and Tobago</option><option value="223">Tunisia</option><option value="224">Turkey</option><option value="225">Turkmenistan</option><option value="226">Turks and Caicos Islands</option><option value="227">Tuvalu</option><option value="228">Uganda</option><option value="229">Ukraine</option><option value="230">United Arab Emirates</option><option value="231">United Kingdom</option><option value="233">United States</option><option value="234">Uruguay</option><option value="235">Uzbekistan</option><option value="236">Vanuatu</option><option value="237">Vatican City State</option><option value="238">Venezuela</option><option value="239">Vietnam</option><option value="240">Virgin Islands (British)</option><option value="241">Virgin Islands (U.S.)</option><option value="242">Wallis and Futuna Islands</option><option value="243">Western Sahara</option><option value="244">Yemen</option><option value="245">Zambia</option><option value="246">Zimbabwe</option>                </select>
            </div>
            <div class="form-group">
                <div><label>Ships to</label></div>
                <select class="form-control" name="shipTo">
                    <option selected="selected"></option>
                    <option value="250">Africa</option><option value="232">America</option><option value="249">Asia</option><option value="248">Europe</option><option value="252">Worldwide</option><option value="1">Afghanistan</option><option value="2">Albania</option><option value="3">Algeria</option><option value="4">American Samoa</option><option value="5">Andorra</option><option value="6">Angola</option><option value="7">Anguilla</option><option value="8">Antarctica</option><option value="9">Antigua and Barbuda</option><option value="10">Argentina</option><option value="11">Armenia</option><option value="12">Aruba</option><option value="13">Australia</option><option value="14">Austria</option><option value="15">Azerbaijan</option><option value="16">Bahamas</option><option value="17">Bahrain</option><option value="18">Bangladesh</option><option value="19">Barbados</option><option value="20">Belarus</option><option value="21">Belgium</option><option value="22">Belize</option><option value="23">Benin</option><option value="24">Bermuda</option><option value="25">Bhutan</option><option value="26">Bolivia</option><option value="27">Bosnia and Herzegovina</option><option value="28">Botswana</option><option value="29">Bouvet Island</option><option value="30">Brazil</option><option value="31">British Indian Ocean Territory</option><option value="32">Brunei Darussalam</option><option value="33">Bulgaria</option><option value="34">Burkina Faso</option><option value="35">Burundi</option><option value="36">Cambodia</option><option value="37">Cameroon</option><option value="38">Canada</option><option value="39">Cape Verde</option><option value="40">Cayman Islands</option><option value="41">Central African Republic</option><option value="42">Chad</option><option value="43">Chile</option><option value="44">China</option><option value="45">Christmas Island</option><option value="46">Cocos (Keeling) Islands</option><option value="47">Colombia</option><option value="48">Comoros</option><option value="51">Cook Islands</option><option value="52">Costa Rica</option><option value="53">Croatia (Hrvatska)</option><option value="54">Cuba</option><option value="55">Cyprus</option><option value="56">Czech Republic</option><option value="49">Democratic Republic of the Congo</option><option value="57">Denmark</option><option value="58">Djibouti</option><option value="59">Dominica</option><option value="60">Dominican Republic</option><option value="61">East Timor</option><option value="62">Ecuador</option><option value="63">Egypt</option><option value="64">El Salvador</option><option value="65">Equatorial Guinea</option><option value="66">Eritrea</option><option value="67">Estonia</option><option value="68">Ethiopia</option><option value="69">Falkland Islands (Malvinas)</option><option value="70">Faroe Islands</option><option value="71">Fiji</option><option value="72">Finland</option><option value="73">France</option><option value="74">France, Metropolitan</option><option value="75">French Guiana</option><option value="76">French Polynesia</option><option value="77">French Southern Territories</option><option value="78">Gabon</option><option value="79">Gambia</option><option value="80">Georgia</option><option value="81">Germany</option><option value="82">Ghana</option><option value="83">Gibraltar</option><option value="85">Greece</option><option value="86">Greenland</option><option value="87">Grenada</option><option value="88">Guadeloupe</option><option value="89">Guam</option><option value="90">Guatemala</option><option value="84">Guernsey</option><option value="91">Guinea</option><option value="92">Guinea-Bissau</option><option value="93">Guyana</option><option value="94">Haiti</option><option value="95">Heard and Mc Donald Islands</option><option value="96">Honduras</option><option value="97">Hong Kong</option><option value="98">Hungary</option><option value="99">Iceland</option><option value="100">India</option><option value="102">Indonesia</option><option value="103">Iran (Islamic Republic of)</option><option value="104">Iraq</option><option value="105">Ireland</option><option value="101">Isle of Man</option><option value="106">Israel</option><option value="107">Italy</option><option value="108">Ivory Coast</option><option value="110">Jamaica</option><option value="111">Japan</option><option value="109">Jersey</option><option value="112">Jordan</option><option value="113">Kazakhstan</option><option value="114">Kenya</option><option value="115">Kiribati</option><option value="116">Korea, Democratic People's Republic of</option><option value="117">Korea, Republic of</option><option value="118">Kosovo</option><option value="119">Kuwait</option><option value="120">Kyrgyzstan</option><option value="121">Lao People's Democratic Republic</option><option value="122">Latvia</option><option value="123">Lebanon</option><option value="124">Lesotho</option><option value="125">Liberia</option><option value="126">Libyan Arab Jamahiriya</option><option value="127">Liechtenstein</option><option value="128">Lithuania</option><option value="129">Luxembourg</option><option value="130">Macau</option><option value="132">Madagascar</option><option value="133">Malawi</option><option value="134">Malaysia</option><option value="135">Maldives</option><option value="136">Mali</option><option value="137">Malta</option><option value="138">Marshall Islands</option><option value="139">Martinique</option><option value="140">Mauritania</option><option value="141">Mauritius</option><option value="142">Mayotte</option><option value="143">Mexico</option><option value="144">Micronesia, Federated States of</option><option value="145">Moldova, Republic of</option><option value="146">Monaco</option><option value="147">Mongolia</option><option value="148">Montenegro</option><option value="149">Montserrat</option><option value="150">Morocco</option><option value="151">Mozambique</option><option value="152">Myanmar</option><option value="153">Namibia</option><option value="154">Nauru</option><option value="155">Nepal</option><option value="156">Netherlands</option><option value="157">Netherlands Antilles</option><option value="158">New Caledonia</option><option value="159">New Zealand</option><option value="160">Nicaragua</option><option value="161">Niger</option><option value="162">Nigeria</option><option value="163">Niue</option><option value="164">Norfolk Island</option><option value="131">North Macedonia</option><option value="165">Northern Mariana Islands</option><option value="166">Norway</option><option value="167">Oman</option><option value="168">Pakistan</option><option value="169">Palau</option><option value="170">Palestine</option><option value="171">Panama</option><option value="172">Papua New Guinea</option><option value="173">Paraguay</option><option value="174">Peru</option><option value="175">Philippines</option><option value="176">Pitcairn</option><option value="177">Poland</option><option value="178">Portugal</option><option value="179">Puerto Rico</option><option value="180">Qatar</option><option value="50">Republic of Congo</option><option value="181">Reunion</option><option value="182">Romania</option><option value="183">Russian Federation</option><option value="184">Rwanda</option><option value="185">Saint Kitts and Nevis</option><option value="186">Saint Lucia</option><option value="187">Saint Vincent and the Grenadines</option><option value="188">Samoa</option><option value="189">San Marino</option><option value="190">Sao Tome and Principe</option><option value="191">Saudi Arabia</option><option value="192">Senegal</option><option value="193">Serbia</option><option value="194">Seychelles</option><option value="195">Sierra Leone</option><option value="196">Singapore</option><option value="197">Slovakia</option><option value="198">Slovenia</option><option value="199">Solomon Islands</option><option value="200">Somalia</option><option value="201">South Africa</option><option value="202">South Georgia South Sandwich Islands</option><option value="203">South Sudan</option><option value="204">Spain</option><option value="205">Sri Lanka</option><option value="206">St. Helena</option><option value="207">St. Pierre and Miquelon</option><option value="208">Sudan</option><option value="209">Suriname</option><option value="210">Svalbard and Jan Mayen Islands</option><option value="211">Swaziland</option><option value="212">Sweden</option><option value="213">Switzerland</option><option value="214">Syrian Arab Republic</option><option value="215">Taiwan</option><option value="216">Tajikistan</option><option value="217">Tanzania, United Republic of</option><option value="218">Thailand</option><option value="219">Togo</option><option value="220">Tokelau</option><option value="221">Tonga</option><option value="222">Trinidad and Tobago</option><option value="223">Tunisia</option><option value="224">Turkey</option><option value="225">Turkmenistan</option><option value="226">Turks and Caicos Islands</option><option value="227">Tuvalu</option><option value="228">Uganda</option><option value="229">Ukraine</option><option value="230">United Arab Emirates</option><option value="231">United Kingdom</option><option value="233">United States</option><option value="234">Uruguay</option><option value="235">Uzbekistan</option><option value="236">Vanuatu</option><option value="237">Vatican City State</option><option value="238">Venezuela</option><option value="239">Vietnam</option><option value="240">Virgin Islands (British)</option><option value="241">Virgin Islands (U.S.)</option><option value="242">Wallis and Futuna Islands</option><option value="243">Western Sahara</option><option value="244">Yemen</option><option value="245">Zambia</option><option value="246">Zimbabwe</option>                </select>
	    </div>
	    	<div class="form-group">
		<label><input type="checkbox" name="autoship" value="1">&nbsp; Autoship</label>
		</div>
		<div class="form-group">
                <div><label>Accepted Currencies</label></div>
                <label><input type="checkbox" name="currencies[]" value="4"> Bitcoin</label>&nbsp; 
           
            </div>
            <div class="form-group">
                <div><label>Product Type</label></div>
                <label><input type="radio" name="type" value="all" checked="checked"> All</label>&nbsp; 
                <label><input type="radio" name="type" value="digital"> Digital</label>&nbsp; 
                <label><input type="radio" name="type" value="physical"> Physical</label>&nbsp; <br>
            </div>
            <div class="form-group text-right">
                <button type="submit" class="btn btn-larger btn-blue" style="margin-bottom: 2px;">Search</button>
                            </div>
        </form>
    </div>
</div>
                <div class="container nopadding" style="margin-top:15px;">
                    <table class="table exchange-table" cellspacing="0" cellpadding="0">
                        <tbody><tr>
                            <th></th>
                                                            <th><strong>
                                    <div class="sprite sprite--bitcoin" style="top:2px;"></div>&nbsp;Bitcoin 
                                </strong></th>
                                                            <th><strong>
                                    <div class="sprite sprite--monero" style="top:2px;"></div>&nbsp;Monero 
                                </strong></th>
                                                    </tr>
                                                    <tr>
                                <td><strong>
                                    AUD                                </strong></td>
                                    <td class="text-center"><?php require_once("aud-current-price-btc.php") ?></td>
                               
                                    <td class="text-center"><?php include("aud-current-price-monero.php") ?></td>
                            </tr>
                                                    <tr>
                                <td><strong>
                                    CAD                                </strong></td>
                                <td class="text-center"><?php require_once("cad-current-price-btc.php") ?></td>
                                <td class="text-center"><?php include("cad-current-price-monero.php") ?></td>
                            </tr>
                                                    <tr>
                                <td><strong>
                                    EUR                                </strong></td>
                                <td class="text-center"><?php require_once("euro-current-price-btc.php") ?></td>
                                <td class="text-center"><?php include("euro-current-price-monero.php") ?></td>
                            </tr>
                                                    <tr>
                                <td><strong>
                                    GBP                                </strong></td>
                                <td class="text-center"><?php require_once("GBP-current-price-btc.php") ?></td>
                                <td class="text-center"><?php include("british-pound-current-price-monero.php") ?></td>
                            </tr>
                                                    <tr>
                                <td><strong>
                                    USD                                </strong></td>
                                <td class="text-center"><?php require_once("usd-current-price-btc.php") ?></td>
                                <td class="text-center"><?php include("usd-current-price-monero.php") ?></td>
                            </tr>
                                            </tbody></table>
                </div>
            </div>
            <div class="col-md-9 sidebar-content-right listing-content">
              
                                    <div class="container">
                                     
                                                                             
                            <?php
                                    
                                  

                            ?>
                            <!-- listings?page=2&type=all&catid=11 make a database name it listings pulll table data from products page query 
                                 the database for the the listings,page,type,and category id -->

                                      <!--  <div class="product-details">
                                            <div class="product-heading">
                                                
                                                <h2><a href="#" class="product-link"><?php //echo $row['name']; ?></a></h2>
                                                <span class="shadow-text smalltext">In <strong><?php //echo $row3['name'] ?></strong></span><br>
                                                <b>Sold By <a href="#">
                                                <?php //echo $rowVendorName['vendor_name']?> </a> ( <div class="" style="display: inline-block; margin-left:-8px; float: none; width:20px; position:absolute;"><img src="images/icons8-star-48.png"></div>&nbsp; <strong>
                                                <?php //echo $rowVendorRating['vendor_rating']; ?>  </strong> )</b> <span class="badge badge-pill" style="display:inline-block;vertical-align: middle;margin-bottom: 1px; background-color: black; color: white;">Level 1</span>&nbsp;<div class="sprite sprite--shopping-cart" title="Total Sales" style="float: none; display: inline-block; margin-left:5px;;"></div>&nbsp; 0<br>
                                                                        
                                                                    </div>
                                            <div class="product-details-bottom">
                                                <div class="sold-amount smalltext">Sold (Number of Times) times in the last 48 hours</div>
                                               <span class="smalltext"><?php //echo $rowTimesSold['times_sold_last_48_hr'] ?></span>
                                               <span class="smalltext">Sold times in total</span>
                                               
                                            </div>
                                        </div>
                                        <div class="product-price">
                                            <span class="badge badge-pill badge-secondary">Status</span>                                                                                        <br>                                            Unlimited 
                                            Available <?php //echo $rowAvailable['available'] ?>
                                            <h2>USD <?php //echo $row['selling_price'] ?> </h2>
                                            <span class="shadow-text smalltext boldtext">0.00010301 BTC<br>0.01870622 XMR<br>                                            </span></div>
                                    </div>
                                </div>
                            </div> -->

                           
                         

                            <?php
$rows_per_page = 20;

// Calculate total records
$records_query = "SELECT COUNT(*) AS total FROM products";
$records_result = mysqli_query($con, $records_query);
$total_records_row = mysqli_fetch_assoc($records_result);
$total_records = $total_records_row['total'];

// Calculate total pages
$pages = ceil($total_records / $rows_per_page);

// Initialize current page variable
$current_page = isset($_GET['page']) ? max(1, min($_GET['page'], $pages)) : 1;

// Calculate start index for the current page
$start = 0;

// Query to retrieve products for the current page
$products_query = "SELECT * FROM products LIMIT $start, $rows_per_page";
$result = mysqli_query($con, $products_query);
?>
<!--
<div class="product-listing">
                                                <div class="product-link">
                                <div class="product">
                                    <div class="container">
                                        <div class="product-photo">
                                                                                            <img src="Listings_files/image_004.jpeg">
                                                                                    </div>
                                        <div class="product-details">
                                            <div class="product-heading">
                                                <h2><a href="listing.php?pid=a5bdc9af-869c-11ed-84a1-d05099fcf7bb" class="product-link">How To Make Money on Fiverr</a></h2>
                                                <span class="shadow-text smalltext">In <strong>E-Books</strong></span><br>
                                                <b>Sold By <a href="profile.php?id=DrunkDragon">
                                                    DrunkDragon                                                </a> ( <div class="sprite sprite--stargold" style="display: inline-block; margin-left:5px;; float: none;"></div>&nbsp; <strong>
                                                    4.5                                                </strong> )</b> <span class="badge badge-pill" style="display:inline-block;vertical-align: middle;margin-bottom: 1px; background-color: black; color: white;">Level 1</span>&nbsp;<div class="sprite sprite--shopping-cart" title="Total Sales" style="float: none; display: inline-block; margin-left:5px;;"></div>&nbsp; 421<br>
                                                                        
                                                                    </div>
                                            <div class="product-details-bottom">
                                                <div class="sold-amount smalltext">Sold 0 times in the last 48 hours</div>
                                                <span class="smalltext">Sold 0 times in total</span>
                                            </div>
                                        </div>
                                        <div class="product-price">
                                            <span class="badge badge-pill badge-secondary">Autoship</span>                                                                                        <br>                                            Unlimited 
                                            Available
                                            <h2>USD 2.99</h2>
                                            <span class="shadow-text smalltext boldtext">
                                                0.00010301 BTC<br>0.01870622 XMR<br>                                            </span></div>
                                    </div>
                                </div>
                            </div>
                                                <div class="product-link">
                                <div class="product">
                                    <div class="container">
                                        <div class="product-photo">
                                                                                            <img src="Listings_files/image_006.jpeg">
                                                                                    </div>
                                        <div class="product-details">
                                            <div class="product-heading">
                                                <h2><a href="http://phfbc3whrbtij36skevox76eqckcwrpyzvegbtpx3afmhdrsrrzjj6id.onion/listing.php?pid=6ed9a00c-9a82-11ec-9889-0025909102ac" class="product-link">56g S-Isomer HQ NEEDLE KETAMINE (UK-UK) GUARANTEED DELIVERY</a></h2>
                                                <span class="shadow-text smalltext">In <strong>Ketamine</strong></span><br>
                                                <b>Sold By <a href="http://phfbc3whrbtij36skevox76eqckcwrpyzvegbtpx3afmhdrsrrzjj6id.onion/profile.php?id=TopDog">
                                                    TopDog                                                </a> ( <div class="sprite sprite--stargold" style="display: inline-block; margin-left:5px;; float: none;"></div>&nbsp; <strong>
                                                    4.8                                                </strong> )</b> <span class="badge badge-pill" style="display:inline-block;vertical-align: middle;margin-bottom: 1px; background-color: blue; color: white;">Level 3</span>&nbsp;<div class="sprite sprite--shopping-cart" title="Total Sales" style="float: none; display: inline-block; margin-left:5px;;"></div>&nbsp; 382<br>                                                <b>Shipped From</b> United Kingdom<br>                                                <b>Shipped To</b> United Kingdom                                            </div>
                                            <div class="product-details-bottom">
                                                <div class="sold-amount smalltext">Sold 0 times in the last 48 hours</div>
                                                <span class="smalltext">Sold 10 times in total</span>
                                            </div>
                                        </div>
                                        <div class="product-price">
                                                                        
                                                                        
                                Unlimited 
                                            Available
                                            <h2>USD 523.84</h2>
                                            <span class="shadow-text smalltext boldtext">
                                                0.01756800 BTC<br>3.19030000 XMR<br>                                            </span></div>
                                    </div>
                                </div>
                            </div>
                                                <div class="product-link">
                                <div class="product">
                                    <div class="container">
                                        <div class="product-photo">
                                                                                            <img src="Listings_files/image_002.png">
                                                                                    </div>
                                        <div class="product-details">
                                            <div class="product-heading">
                                                <h2><a href="http://phfbc3whrbtij36skevox76eqckcwrpyzvegbtpx3afmhdrsrrzjj6id.onion/listing.php?pid=03ba8f6a-a8a6-11ed-a4cb-d05099fcf7bb" class="product-link">| SALE | 10 - 1000ML GHB LIQUID PURIFIED GHB | MADE WITH FOOD GRADE QUALITY GBL |</a></h2>
                                                <span class="shadow-text smalltext">In <strong>GHB</strong></span><br>
                                                <b>Sold By <a href="http://phfbc3whrbtij36skevox76eqckcwrpyzvegbtpx3afmhdrsrrzjj6id.onion/profile.php?id=DasApothecary">
                                                    DasApothecary                                                </a> ( <div class="sprite sprite--stargold" style="display: inline-block; margin-left:5px;; float: none;"></div>&nbsp; <strong>
                                                    4.4                                                </strong> )</b> <span class="badge badge-pill" style="display:inline-block;vertical-align: middle;margin-bottom: 1px; background-color: navy; color: white;">Level 2</span>&nbsp;<div class="sprite sprite--shopping-cart" title="Total Sales" style="float: none; display: inline-block; margin-left:5px;;"></div>&nbsp; 170<br>                                                <b>Shipped From</b> Germany<br>                                                <b>Shipped To</b> Worldwide                                            </div>
                                            <div class="product-details-bottom">
                                                <div class="sold-amount smalltext">Sold 2 times in the last 48 hours</div>
                                                <span class="smalltext">Sold 50 times in total</span>
                                            </div>
                                        </div>
                                        <div class="product-price">
                                                                        
                                                                        
                                Unlimited 
                                            Available
                                            <h2>USD 2.25</h2>
                                            <span class="shadow-text smalltext boldtext">
                                                0.00007598 BTC<br>0.01379596 XMR<br>                                            </span></div>
                                    </div>
                                </div>
                            </div>
                                                <div class="product-link">
                                <div class="product">
                                    <div class="container">
                                        <div class="product-photo">
                                                                                            <img src="Listings_files/image_017.jpeg">
                                                                                    </div>
                                        <div class="product-details">
                                            <div class="product-heading">
                                                <h2><a href="http://phfbc3whrbtij36skevox76eqckcwrpyzvegbtpx3afmhdrsrrzjj6id.onion/listing.php?pid=be8b3fc1-301e-11ee-aa85-d05099fcf7bb" class="product-link">x500 30mg A215 Oxycodone cheap Offer US to US (Fast USPS shipping)</a></h2>
                                                <span class="shadow-text smalltext">In <strong>Oxycodone</strong></span><br>
                                                <b>Sold By <a href="http://phfbc3whrbtij36skevox76eqckcwrpyzvegbtpx3afmhdrsrrzjj6id.onion/profile.php?id=Oxy">
                                                    Oxy                                                </a> ( <div class="sprite sprite--stargold" style="display: inline-block; margin-left:5px;; float: none;"></div>&nbsp; <strong>
                                                    4.1                                                </strong> )</b> <span class="badge badge-pill" style="display:inline-block;vertical-align: middle;margin-bottom: 1px; background-color: black; color: white;">Level 1</span>&nbsp;<div class="sprite sprite--shopping-cart" title="Total Sales" style="float: none; display: inline-block; margin-left:5px;;"></div>&nbsp; 11<br>                                                <b>Shipped From</b> United States<br>                                                <b>Shipped To</b> United States                                            </div>
                                            <div class="product-details-bottom">
                                                <div class="sold-amount smalltext">Sold 0 times in the last 48 hours</div>
                                                <span class="smalltext">Sold 0 times in total</span>
                                            </div>
                                        </div>
                                        <div class="product-price">
                                                                        
                                                                        
                                Unlimited 
                                            Available
                                            <h2>USD 1500</h2>
                                            <span class="shadow-text smalltext boldtext">
                                                0.05167500 BTC<br>9.38439000 XMR<br>                                            </span></div>
                                    </div>
                                </div>
                            </div>
                                                <div class="product-link">
                                <div class="product">
                                    <div class="container">
                                        <div class="product-photo">
                                                                                            <img src="Listings_files/image_013.jpeg">
                                                                                    </div>
                                        <div class="product-details">
                                            <div class="product-heading">
                                                <h2><a href="http://phfbc3whrbtij36skevox76eqckcwrpyzvegbtpx3afmhdrsrrzjj6id.onion/listing.php?pid=6b2cfdb4-4964-11ed-9ed3-d05099fd2e99" class="product-link">DMT Puff Bars - 1G Puff DMT Vape Pens</a></h2>
                                                <span class="shadow-text smalltext">In <strong>DMT</strong></span><br>
                                                <b>Sold By <a href="http://phfbc3whrbtij36skevox76eqckcwrpyzvegbtpx3afmhdrsrrzjj6id.onion/profile.php?id=monster_12">
                                                    monster_12                                                </a> ( <div class="sprite sprite--stargold" style="display: inline-block; margin-left:5px;; float: none;"></div>&nbsp; <strong>
                                                    4.7                                                </strong> )</b> <span class="badge badge-pill" style="display:inline-block;vertical-align: middle;margin-bottom: 1px; background-color: black; color: white;">Level 1</span>&nbsp;<div class="sprite sprite--shopping-cart" title="Total Sales" style="float: none; display: inline-block; margin-left:5px;;"></div>&nbsp; 34<br>                                                <b>Shipped From</b> United States<br>                                                <b>Shipped To</b> United States                                            </div>
                                            <div class="product-details-bottom">
                                                <div class="sold-amount smalltext">Sold 0 times in the last 48 hours</div>
                                                <span class="smalltext">Sold 0 times in total</span>
                                            </div>
                                        </div>
                                        <div class="product-price">
                                                                        
                                                                        
                                9999 
                                            Available
                                            <h2>USD 185</h2>
                                            <span class="shadow-text smalltext boldtext">
                                                0.00637325 BTC<br>1.15740810 XMR<br>                                            </span></div>
                                    </div>
                                </div>
                            </div>
                                                <div class="product-link">
                                <div class="product">
                                    <div class="container">
                                        <div class="product-photo">
                                                                                            <img src="Listings_files/image.jpeg">
                                                                                    </div>
                                        <div class="product-details">
                                            <div class="product-heading">
                                                <h2><a href="http://phfbc3whrbtij36skevox76eqckcwrpyzvegbtpx3afmhdrsrrzjj6id.onion/listing.php?pid=0ead4071-ede4-11ed-9d39-d05099fcf7bb" class="product-link">[Loyalty Blind Box Promotion] 25 G of Ketamine 100% Pure S-Isomer Sugar</a></h2>
                                                <span class="shadow-text smalltext">In <strong>Ketamine</strong></span><br>
                                                <b>Sold By <a href="http://phfbc3whrbtij36skevox76eqckcwrpyzvegbtpx3afmhdrsrrzjj6id.onion/profile.php?id=ketakingdom1">
                                                    ketakingdom1                                                </a> ( <div class="sprite sprite--stargold" style="display: inline-block; margin-left:5px;; float: none;"></div>&nbsp; <strong>
                                                    4.9                                                </strong> )</b> <span class="badge badge-pill" style="display:inline-block;vertical-align: middle;margin-bottom: 1px; background-color: black; color: white;">Level 1</span>&nbsp;<div class="sprite sprite--shopping-cart" title="Total Sales" style="float: none; display: inline-block; margin-left:5px;;"></div>&nbsp; 28<br>                                                <b>Shipped From</b> United States<br>                                                <b>Shipped To</b> United States                                            </div>
                                            <div class="product-details-bottom">
                                                <div class="sold-amount smalltext">Sold 0 times in the last 48 hours</div>
                                                <span class="smalltext">Sold 0 times in total</span>
                                            </div>
                                        </div>
                                        <div class="product-price">
                                                                        
                                                                        
                                Unlimited 
                                            Available
                                            <h2>USD 869.99</h2>
                                            <span class="shadow-text smalltext boldtext">
                                                0.02997116 BTC<br>5.44288364 XMR<br>                                            </span></div>
                                    </div>
                                </div>
                            </div>
                                                <div class="product-link">
                                <div class="product">
                                    <div class="container">
                                        <div class="product-photo">
                                                                                            <img src="Listings_files/image_008.jpeg">
                                                                                    </div>
                                        <div class="product-details">
                                            <div class="product-heading">
                                                <h2><a href="http://phfbc3whrbtij36skevox76eqckcwrpyzvegbtpx3afmhdrsrrzjj6id.onion/listing.php?pid=876701c2-f383-11ec-9079-d05099fd2e99" class="product-link">Cinnamon Shakes 454 grams 1LB</a></h2>
                                                <span class="shadow-text smalltext">In <strong>Shake</strong></span><br>
                                                <b>Sold By <a href="http://phfbc3whrbtij36skevox76eqckcwrpyzvegbtpx3afmhdrsrrzjj6id.onion/profile.php?id=CaliConnection">
                                                    CaliConnection                                                </a> ( <div class="sprite sprite--stargold" style="display: inline-block; margin-left:5px;; float: none;"></div>&nbsp; <strong>
                                                    4.9                                                </strong> )</b> <span class="badge badge-pill" style="display:inline-block;vertical-align: middle;margin-bottom: 1px; background-color: navy; color: white;">Level 2</span>&nbsp;<div class="sprite sprite--shopping-cart" title="Total Sales" style="float: none; display: inline-block; margin-left:5px;;"></div>&nbsp; 127<br>                                                <b>Shipped From</b> United States<br>                                                <b>Shipped To</b> United States                                            </div>
                                            <div class="product-details-bottom">
                                                <div class="sold-amount smalltext">Sold 0 times in the last 48 hours</div>
                                                <span class="smalltext">Sold 9 times in total</span>
                                            </div>
                                        </div>
                                        <div class="product-price">
                                                                        
                                                                        
                                37 
                                            Available
                                            <h2>USD 150</h2>
                                            <span class="shadow-text smalltext boldtext">
                                                0.00516750 BTC<br>                                            </span></div>
                                    </div>
                                </div>
                            </div>
                                                <div class="product-link">
                                <div class="product">
                                    <div class="container">
                                        <div class="product-photo">
                                                                                            <img src="Listings_files/image_012.jpeg">
                                                                                    </div>
                                        <div class="product-details">
                                            <div class="product-heading">
                                                <h2><a href="http://phfbc3whrbtij36skevox76eqckcwrpyzvegbtpx3afmhdrsrrzjj6id.onion/listing.php?pid=e702276c-d53c-11ed-9d39-d05099fcf7bb" class="product-link">250g MDMA Candied Sugar [1g - 1000g]</a></h2>
                                                <span class="shadow-text smalltext">In <strong>MDMA</strong></span><br>
                                                <b>Sold By <a href="http://phfbc3whrbtij36skevox76eqckcwrpyzvegbtpx3afmhdrsrrzjj6id.onion/profile.php?id=3APES">
                                                    3APES                                                </a> ( <div class="sprite sprite--stargold" style="display: inline-block; margin-left:5px;; float: none;"></div>&nbsp; <strong>
                                                    5.0                                                </strong> )</b> <span class="badge badge-pill" style="display:inline-block;vertical-align: middle;margin-bottom: 1px; background-color: black; color: white;">Level 1</span>&nbsp;<div class="sprite sprite--shopping-cart" title="Total Sales" style="float: none; display: inline-block; margin-left:5px;;"></div>&nbsp; 13<br>                                                <b>Shipped From</b> Germany<br>                                                <b>Shipped To</b> Worldwide                                            </div>
                                            <div class="product-details-bottom">
                                                <div class="sold-amount smalltext">Sold 0 times in the last 48 hours</div>
                                                <span class="smalltext">Sold 0 times in total</span>
                                            </div>
                                        </div>
                                        <div class="product-price">
                                                                        
                                                                        
                                9999 
                                            Available
                                            <h2>USD 2807.25</h2>
                                            <span class="shadow-text smalltext boldtext">
                                                0.09497500 BTC<br>17.24495000 XMR<br>                                            </span></div>
                                    </div>
                                </div>
                            </div>
                                                <div class="product-link">
                                <div class="product">
                                    <div class="container">
                                        <div class="product-photo">
                                                                                            <img src="Listings_files/image_005.jpeg">
                                                                                    </div>
                                        <div class="product-details">
                                            <div class="product-heading">
                                                <h2><a href="http://phfbc3whrbtij36skevox76eqckcwrpyzvegbtpx3afmhdrsrrzjj6id.onion/listing.php?pid=61637c27-31bd-11ed-a8dc-d05099fd2e99" class="product-link">3g Mephedrone - 4MMC - High Quality</a></h2>
                                                <span class="shadow-text smalltext">In <strong>Other Stimulants</strong></span><br>
                                                <b>Sold By <a href="http://phfbc3whrbtij36skevox76eqckcwrpyzvegbtpx3afmhdrsrrzjj6id.onion/profile.php?id=DutchDragons">
                                                    DutchDragons                                                </a> ( <div class="sprite sprite--stargold" style="display: inline-block; margin-left:5px;; float: none;"></div>&nbsp; <strong>
                                                    4.8                                                </strong> )</b> <span class="badge badge-pill" style="display:inline-block;vertical-align: middle;margin-bottom: 1px; background-color: navy; color: white;">Level 2</span>&nbsp;<div class="sprite sprite--shopping-cart" title="Total Sales" style="float: none; display: inline-block; margin-left:5px;;"></div>&nbsp; 164<br>                                                <b>Shipped From</b> Netherlands<br>                                                <b>Shipped To</b> Worldwide                                            </div>
                                            <div class="product-details-bottom">
                                                <div class="sold-amount smalltext">Sold 0 times in the last 48 hours</div>
                                                <span class="smalltext">Sold 1 times in total</span>
                                            </div>
                                        </div>
                                        <div class="product-price">
                                                                        
                                                                        
                                Unlimited 
                                            Available
                                            <h2>USD 61.76</h2>
                                            <span class="shadow-text smalltext boldtext">
                                                0.00208945 BTC<br>0.37938890 XMR<br>                                            </span></div>
                                    </div>
                                </div>
                            </div>
                                                <div class="product-link">
                                <div class="product">
                                    <div class="container">
                                        <div class="product-photo">
                                                                                            <img src="Listings_files/image_010.jpeg">
                                                                                    </div>
                                        <div class="product-details">
                                            <div class="product-heading">
                                                <h2><a href="http://phfbc3whrbtij36skevox76eqckcwrpyzvegbtpx3afmhdrsrrzjj6id.onion/listing.php?pid=d657cf1e-f8ce-11ed-beb5-d05099fcf7bb" class="product-link">M523 Percocet Sample 100 x10mg</a></h2>
                                                <span class="shadow-text smalltext">In <strong>Oxycodone</strong></span><br>
                                                <b>Sold By <a href="http://phfbc3whrbtij36skevox76eqckcwrpyzvegbtpx3afmhdrsrrzjj6id.onion/profile.php?id=PharmaGrade">
                                                    PharmaGrade                                                </a> ( <div class="sprite sprite--stargold" style="display: inline-block; margin-left:5px;; float: none;"></div>&nbsp; <strong>
                                                    4.2                                                </strong> )</b> <span class="badge badge-pill" style="display:inline-block;vertical-align: middle;margin-bottom: 1px; background-color: navy; color: white;">Level 2</span>&nbsp;<div class="sprite sprite--shopping-cart" title="Total Sales" style="float: none; display: inline-block; margin-left:5px;;"></div>&nbsp; 77<br>                                                <b>Shipped From</b> United States<br>                                                <b>Shipped To</b> United States                                            </div>
                                            <div class="product-details-bottom">
                                                <div class="sold-amount smalltext">Sold 0 times in the last 48 hours</div>
                                                <span class="smalltext">Sold 1 times in total</span>
                                            </div>
                                        </div>
                                        <div class="product-price">
                                                                        
                                                                        
                                Unlimited 
                                            Available
                                            <h2>USD 289</h2>
                                            <span class="shadow-text smalltext boldtext">
                                                0.00995605 BTC<br>1.80805914 XMR<br>                                            </span></div>
                                    </div>
                                </div>
                            </div>
                                                <div class="product-link">
                                  <div class="product">
                                    <div class="container">
                                        <div class="product-photo">
                                                                                            <img src="Listings_files/image_009.jpeg">
                                                                                    </div>
                                        <div class="product-details">
                                            <div class="product-heading">
                                                <h2><a href="http://phfbc3whrbtij36skevox76eqckcwrpyzvegbtpx3afmhdrsrrzjj6id.onion/listing.php?pid=299c13f9-f1bc-11ed-83ad-d05099fcf7bb" class="product-link">Sherbet Cake UK2UK - NDD AVAILABLE 56g</a></h2>
                                                <span class="shadow-text smalltext">In <strong>Buds</strong></span><br>
                                                <b>Sold By <a href="http://phfbc3whrbtij36skevox76eqckcwrpyzvegbtpx3afmhdrsrrzjj6id.onion/profile.php?id=KandyKones">
                                                    KandyKones                                                </a> ( <div class="sprite sprite--stargold" style="display: inline-block; margin-left:5px;; float: none;"></div>&nbsp; <strong>
                                                    0.0                                                </strong> )</b> <span class="badge badge-pill" style="display:inline-block;vertical-align: middle;margin-bottom: 1px; background-color: black; color: white;">Level 1</span>&nbsp;<div class="sprite sprite--shopping-cart" title="Total Sales" style="float: none; display: inline-block; margin-left:5px;;"></div>&nbsp; 6<br>                                                <b>Shipped From</b> United Kingdom<br>                                                <b>Shipped To</b> United Kingdom                                            </div>
                                            <div class="product-details-bottom">
                                                <div class="sold-amount smalltext">Sold 0 times in the last 48 hours</div>
                                                <span class="smalltext">Sold 0 times in total</span>
                                            </div>
                                        </div>
                                        <div class="product-price">
                                                                        
                                                                        
                                1000 
                                            Available
                                            <h2>USD 523.84</h2>
                                            <span class="shadow-text smalltext boldtext">
                                                0.01756800 BTC<br>3.19030000 XMR<br>                                            </span></div>
                                    </div>
                                </div>
                            </div>
                                                <div class="product-link">
                                <div class="product">
                                    <div class="container">
                                        <div class="product-photo">
                                                                                            <img src="Listings_files/image_016.jpeg">
                                                                                    </div>
                                        <div class="product-details">
                                            <div class="product-heading">
                                                <h2><a href="http://phfbc3whrbtij36skevox76eqckcwrpyzvegbtpx3afmhdrsrrzjj6id.onion/listing.php?pid=48fcd4ff-0e8e-11ee-beb5-d05099fcf7bb" class="product-link">Golden offer x1000 10mg pink Oxycodone US to US (Fast USPS shipping with tracking )</a></h2>
                                                <span class="shadow-text smalltext">In <strong>Oxycodone</strong></span><br>
                                                <b>Sold By <a href="http://phfbc3whrbtij36skevox76eqckcwrpyzvegbtpx3afmhdrsrrzjj6id.onion/profile.php?id=blacknoir001">
                                                    blacknoir001                                                </a> ( <div class="sprite sprite--stargold" style="display: inline-block; margin-left:5px;; float: none;"></div>&nbsp; <strong>
                                                    4.7                                                </strong> )</b> <span class="badge badge-pill" style="display:inline-block;vertical-align: middle;margin-bottom: 1px; background-color: navy; color: white;">Level 2</span>&nbsp;<div class="sprite sprite--shopping-cart" title="Total Sales" style="float: none; display: inline-block; margin-left:5px;;"></div>&nbsp; 142<br>                                                <b>Shipped From</b> United States<br>                                                <b>Shipped To</b> United States                                            </div>
                                            <div class="product-details-bottom">
                                                <div class="sold-amount smalltext">Sold 0 times in the last 48 hours</div>
                                                <span class="smalltext">Sold 0 times in total</span>
                                            </div>
                                        </div>
                                        <div class="product-price">
                                                                        
                                                                        
                                Unlimited 
                                            Available
                                            <h2>USD 1000</h2>
                                            <span class="shadow-text smalltext boldtext">
                                                0.03445000 BTC<br>6.25626000 XMR<br>                                            </span></div>
                                    </div>
                                </div>
                            </div>
                                                <div class="product-link">
                                <div class="product">
                                    <div class="container">
                                        <div class="product-photo">
                                                                                            <img src="Listings_files/image_002.jpeg">
                                                                                    </div>
                                        <div class="product-details">
                                            <div class="product-heading">
                                                <h2><a href="http://phfbc3whrbtij36skevox76eqckcwrpyzvegbtpx3afmhdrsrrzjj6id.onion/listing.php?pid=a60fcd3d-59bf-11ec-b06e-0025909102ac" class="product-link">3.5g Weed Purple Haze 40 GBP</a></h2>
                                                <span class="shadow-text smalltext">In <strong>Buds</strong></span><br>
                                                <b>Sold By <a href="http://phfbc3whrbtij36skevox76eqckcwrpyzvegbtpx3afmhdrsrrzjj6id.onion/profile.php?id=maurelius">
                                                    maurelius                                                </a> ( <div class="sprite sprite--stargold" style="display: inline-block; margin-left:5px;; float: none;"></div>&nbsp; <strong>
                                                    4.8                                                </strong> )</b> <span class="badge badge-pill" style="display:inline-block;vertical-align: middle;margin-bottom: 1px; background-color: blue; color: white;">Level 3</span>&nbsp;<div class="sprite sprite--shopping-cart" title="Total Sales" style="float: none; display: inline-block; margin-left:5px;;"></div>&nbsp; 309<br>                                                <b>Shipped From</b> United Kingdom<br>                                                <b>Shipped To</b> Worldwide                                            </div>
                                            <div class="product-details-bottom">
                                                <div class="sold-amount smalltext">Sold 0 times in the last 48 hours</div>
                                                <span class="smalltext">Sold 2 times in total</span>
                                            </div>
                                        </div>
                                        <div class="product-price">
                                                                        
                                                                        
                                997 
                                            Available
                                            <h2>USD 52.38</h2>
                                            <span class="shadow-text smalltext boldtext">
                                                0.00175680 BTC<br>0.31903000 XMR<br>                                            </span></div>
                                    </div>
                                </div>
                            </div>
                                                <div class="product-link">
                                <div class="product">
                                    <div class="container">
                                        <div class="product-photo">
                                                                                            <img src="Listings_files/image_015.jpeg">
                                                                                    </div>
                                        <div class="product-details">
                                            <div class="product-heading">
                                                <h2><a href="http://phfbc3whrbtij36skevox76eqckcwrpyzvegbtpx3afmhdrsrrzjj6id.onion/listing.php?pid=28375cf1-843a-11ec-9889-0025909102ac" class="product-link">25x II XTC BLUE EA SPORTS 300MG MDMA</a></h2>
                                                <span class="shadow-text smalltext">In <strong>Pills</strong></span><br>
                                                <b>Sold By <a href="http://phfbc3whrbtij36skevox76eqckcwrpyzvegbtpx3afmhdrsrrzjj6id.onion/profile.php?id=GermanMasters">
                                                    GermanMasters                                                </a> ( <div class="sprite sprite--stargold" style="display: inline-block; margin-left:5px;; float: none;"></div>&nbsp; <strong>
                                                    4.8                                                </strong> )</b> <span class="badge badge-pill" style="display:inline-block;vertical-align: middle;margin-bottom: 1px; background-color: blue; color: white;">Level 3</span>&nbsp;<div class="sprite sprite--shopping-cart" title="Total Sales" style="float: none; display: inline-block; margin-left:5px;;"></div>&nbsp; 971<br>                                                <b>Shipped From</b> Germany<br>                                                <b>Shipped To</b> Europe                                            </div>
                                            <div class="product-details-bottom">
                                                <div class="sold-amount smalltext">Sold 0 times in the last 48 hours</div>
                                                <span class="smalltext">Sold 4 times in total</span>
                                            </div>
                                        </div>
                                        <div class="product-price">
                                                                        
                                                                        
                                Unlimited 
                                            Available
                                            <h2>USD 44.92</h2>
                                            <span class="shadow-text smalltext boldtext">
                                                0.00151960 BTC<br>0.27591920 XMR<br>                                            </span></div>
                                    </div>
                                </div>
                            </div>
                                                <div class="product-link">
                                <div class="product">
                                    <div class="container">
                                        <div class="product-photo">
                                                                                            <img src="Listings_files/image_003.png">
                                                                                    </div>
                                        <div class="product-details">
                                            <div class="product-heading">
                                                <h2><a href="http://phfbc3whrbtij36skevox76eqckcwrpyzvegbtpx3afmhdrsrrzjj6id.onion/listing.php?pid=2c4df459-25ae-11ed-b36a-d05099fd2e99" class="product-link">Testosterone 500mg/ml - 10ml - Germany - EU</a></h2>
                                                <span class="shadow-text smalltext">In <strong>Steroids</strong></span><br>
                                                <b>Sold By <a href="http://phfbc3whrbtij36skevox76eqckcwrpyzvegbtpx3afmhdrsrrzjj6id.onion/profile.php?id=ApocalpyseLabs">
                                                    ApocalpyseLabs                                                </a> ( <div class="sprite sprite--stargold" style="display: inline-block; margin-left:5px;; float: none;"></div>&nbsp; <strong>
                                                    5.0                                                </strong> )</b> <span class="badge badge-pill" style="display:inline-block;vertical-align: middle;margin-bottom: 1px; background-color: blue; color: white;">Level 3</span>&nbsp;<div class="sprite sprite--shopping-cart" title="Total Sales" style="float: none; display: inline-block; margin-left:5px;;"></div>&nbsp; 807<br>                                                <b>Shipped From</b> Germany<br>                                                <b>Shipped To</b> Europe, Germany                                            </div>
                                            <div class="product-details-bottom">
                                                <div class="sold-amount smalltext">Sold 0 times in the last 48 hours</div>
                                                <span class="smalltext">Sold 3 times in total</span>
                                            </div>
                                        </div>
                                        <div class="product-price">
                                                                        
                                                                        
                                9994 
                                            Available
                                            <h2>USD 35</h2>
                                            <span class="shadow-text smalltext boldtext">
                                                0.00120575 BTC<br>0.21896910 XMR<br>                                            </span></div>
                                    </div>
                                </div>
                            </div>
                                                <div class="product-link">
                                <div class="product">
                                    <div class="container">
                                        <div class="product-photo">
                                                                                            <img src="Listings_files/image_003.jpeg">
                                                                                    </div>
                                        <div class="product-details">
                                            <div class="product-heading">
                                                <h2><a href="http://phfbc3whrbtij36skevox76eqckcwrpyzvegbtpx3afmhdrsrrzjj6id.onion/listing.php?pid=a7efffef-bd62-11ed-9d39-d05099fcf7bb" class="product-link">PREMIUM Porn Pack movies videos movies Bubble Butt</a></h2>
                                                <span class="shadow-text smalltext">In <strong>Pornography</strong></span><br>
                                                <b>Sold By <a href="http://phfbc3whrbtij36skevox76eqckcwrpyzvegbtpx3afmhdrsrrzjj6id.onion/profile.php?id=danielvitor61">
                                                    danielvitor61                                                </a> ( <div class="sprite sprite--stargold" style="display: inline-block; margin-left:5px;; float: none;"></div>&nbsp; <strong>
                                                    4.9                                                </strong> )</b> <span class="badge badge-pill" style="display:inline-block;vertical-align: middle;margin-bottom: 1px; background-color: navy; color: white;">Level 2</span>&nbsp;<div class="sprite sprite--shopping-cart" title="Total Sales" style="float: none; display: inline-block; margin-left:5px;;"></div>&nbsp; 1114<br>
                                                                        
                                                                    </div>
                                            <div class="product-details-bottom">
                                                <div class="sold-amount smalltext">Sold 0 times in the last 48 hours</div>
                                                <span class="smalltext">Sold 0 times in total</span>
                                            </div>
                                        </div>
                                        <div class="product-price">
                                            <span class="badge badge-pill badge-secondary">Autoship</span>                                                                                        <br>                                            Unlimited 
                                            Available
                                            <h2>USD 2.99</h2>
                                            <span class="shadow-text smalltext boldtext">
                                                0.00010301 BTC<br>0.01870622 XMR<br>                                            </span></div>
                                    </div>
                                </div>
                            </div>
                                                <div class="product-link">
                                <div class="product">
                                    <div class="container">
                                        <div class="product-photo">
                                                                                            <img src="Listings_files/image_011.jpeg">
                                                                                    </div>
                                        <div class="product-details">
                                            <div class="product-heading">
                                                <h2><a href="http://phfbc3whrbtij36skevox76eqckcwrpyzvegbtpx3afmhdrsrrzjj6id.onion/listing.php?pid=890bfe73-9981-11ec-9889-0025909102ac" class="product-link">10 | 260mg Squid Games | XTC Ecstacy Pills</a></h2>
                                                <span class="shadow-text smalltext">In <strong>Pills</strong></span><br>
                                                <b>Sold By <a href="http://phfbc3whrbtij36skevox76eqckcwrpyzvegbtpx3afmhdrsrrzjj6id.onion/profile.php?id=DrSwole">
                                                    DrSwole                                                </a> ( <div class="sprite sprite--stargold" style="display: inline-block; margin-left:5px;; float: none;"></div>&nbsp; <strong>
                                                    5.0                                                </strong> )</b>
                                                    
                                            <span class="badge badge-pill" style="display:inline-block;vertical-align: middle;margin-bottom: 1px; background-color: blue; color: white;">Level 3</span>&nbsp;<div class="sprite sprite--shopping-cart" title="Total Sales" style="float: none; display: inline-block; margin-left:5px;;"></div>&nbsp; 600<br>                                                <b>Shipped From</b> United Kingdom<br>                                                <b>Shipped To</b> Worldwide                                            </div>
                                            <div class="product-details-bottom">
                                                <div class="sold-amount smalltext">Sold 0 times in the last 48 hours</div>
                                                <span class="smalltext">Sold 8 times in total</span>
                                            </div>
                                        </div>
                                        <div class="product-price">
                                                                        
                                                                        
                                Unlimited 
                                            Available
                                            <h2>USD 58.93</h2>
                                            <span class="shadow-text smalltext boldtext">
                                                0.00197640 BTC<br>0.35890875 XMR<br>                                            </span></div>
                                    </div>
                                </div>
                            </div>
                                                <div class="product-link">
                                <div class="product">
                                    <div class="container">
                                        <div class="product-photo">
                                                                                            <img src="Listings_files/image.png">
                                                                                    </div>
                                        <div class="product-details">
                                            <div class="product-heading">
                                                <h2><a href="http://phfbc3whrbtij36skevox76eqckcwrpyzvegbtpx3afmhdrsrrzjj6id.onion/listing.php?pid=e348c1f1-1863-11ee-beb5-d05099fcf7bb" class="product-link">[PL-&gt;PL] 10g AMFETAMINA 74% (FETA PASTA)</a></h2>
                                                <span class="shadow-text smalltext">In <strong>Speed</strong></span><br>
                                                <b>Sold By <a href="http://phfbc3whrbtij36skevox76eqckcwrpyzvegbtpx3afmhdrsrrzjj6id.onion/profile.php?id=queenofweed">
                                                    queenofweed                                                </a> ( <div class="sprite sprite--stargold" style="display: inline-block; margin-left:5px;; float: none;"></div>&nbsp; <strong>
                                                    5.0                                                </strong> )</b> <span class="badge badge-pill" style="display:inline-block;vertical-align: middle;margin-bottom: 1px; background-color: navy; color: white;">Level 2</span>&nbsp;<div class="sprite sprite--shopping-cart" title="Total Sales" style="float: none; display: inline-block; margin-left:5px;;"></div>&nbsp; 57<br>                                                <b>Shipped From</b> Poland<br>                                                <b>Shipped To</b> Poland                                            </div>
                                            <div class="product-details-bottom">
                                                <div class="sold-amount smalltext">Sold 0 times in the last 48 hours</div>
                                                <span class="smalltext">Sold 0 times in total</span>
                                            </div>
                                        </div>
                                        <div class="product-price">
                                                                        
                                                                        
                                Unlimited 
                                            Available
                                            <h2>USD 32.56</h2>
                                            <span class="shadow-text smalltext boldtext">
                                                0.00110171 BTC<br>0.20004142 XMR<br>                                            </span></div>
                                    </div>
                                </div>
                            </div>
                                                <div class="product-link">
                                <div class="product">
                                    <div class="container">
                                        <div class="product-photo">
                                                                                            <img src="Listings_files/image_014.jpeg">
                                                                                    </div>
                                        <div class="product-details">
                                            <div class="product-heading">
                                                <h2><a href="http://phfbc3whrbtij36skevox76eqckcwrpyzvegbtpx3afmhdrsrrzjj6id.onion/listing.php?pid=179a28da-b7e1-11ed-9d39-d05099fcf7bb" class="product-link">28G Ketamine R Isomer Rocks (Worldwide)</a></h2>
                                                <span class="shadow-text smalltext">In <strong>Ketamine</strong></span><br>
                                                <b>Sold By <a href="http://phfbc3whrbtij36skevox76eqckcwrpyzvegbtpx3afmhdrsrrzjj6id.onion/profile.php?id=Partyfaves">
                                                    Partyfaves                                                </a> ( <div class="sprite sprite--stargold" style="display: inline-block; margin-left:5px;; float: none;"></div>&nbsp; <strong>
                                                    5.0                                                </strong> )</b> <span class="badge badge-pill" style="display:inline-block;vertical-align: middle;margin-bottom: 1px; background-color: navy; color: white;">Level 2</span>&nbsp;<div class="sprite sprite--shopping-cart" title="Total Sales" style="float: none; display: inline-block; margin-left:5px;;"></div>&nbsp; 201<br>                                                <b>Shipped From</b> Canada<br>                                                <b>Shipped To</b> Worldwide                                            </div>
                                            <div class="product-details-bottom">
                                                <div class="sold-amount smalltext">Sold 0 times in the last 48 hours</div>
                                                <span class="smalltext">Sold 1 times in total</span>
                                            </div>
                                        </div>
                                        <div class="product-price">
                                                                        
                                                                        
                                999 
                                            Available
                                            <h2>USD 1060.08</h2>
                                            <span class="shadow-text smalltext boldtext">
                                                0.03603600 BTC<br>6.54420200 XMR<br>                                            </span></div>
                                    </div>
                                </div>
                            </div>
                                                <div class="product-link">
                                <div class="product">
                                    <div class="container">
                                        <div class="product-photo">
                                                                                            <img src="Listings_files/image_007.jpeg">
                                                                                    </div>
                                        <div class="product-details">
                                            <div class="product-heading">
                                                <h2><a href="http://phfbc3whrbtij36skevox76eqckcwrpyzvegbtpx3afmhdrsrrzjj6id.onion/listing.php?pid=255b4c61-f8a0-11ed-beb5-d05099fcf7bb" class="product-link">Prime AAA/S+  Haze +++ 500 G</a></h2>
                                                <span class="shadow-text smalltext">In <strong>Buds</strong></span><br>
                                                <b>Sold By <a href="http://phfbc3whrbtij36skevox76eqckcwrpyzvegbtpx3afmhdrsrrzjj6id.onion/profile.php?id=CannaComp">
                                                    CannaComp                                                </a> ( <div class="sprite sprite--stargold" style="display: inline-block; margin-left:5px;; float: none;"></div>&nbsp; <strong>
                                                    4.8                                                </strong> )</b> <span class="badge badge-pill" style="display:inline-block;vertical-align: middle;margin-bottom: 1px; background-color: navy; color: white;">Level 2</span>&nbsp;<div class="sprite sprite--shopping-cart" title="Total Sales" style="float: none; display: inline-block; margin-left:5px;;"></div>&nbsp; 193<br>                                                <b>Shipped From</b> Germany<br>                                                <b>Shipped To</b> Germany                                            </div>
                                            <div class="product-details-bottom">
                                                <div class="sold-amount smalltext">Sold 0 times in the last 48 hours</div>
                                                <span class="smalltext">Sold 0 times in total</span>
                                            </div>
                                        </div>
                                        
                                        <div class="product-price">
                                                                        
                                                                        
                                Unlimited 
                                            Available
                                            <h2>USD 2919.54</h2>
                                            <span class="shadow-text smalltext boldtext">
                                                0.09877400 BTC<br>17.93474800 XMR<br>                                            </span></div>
                                    </div>
                                </div>
                            </div>
                                    </div>-->
                                    <?php
// Function to format price as currency with 2 decimal places
function formatCurrency($price) {
    return number_format($price, 2);
}

$host = "localhost";
$username = "root";
$password = "CoheedAndCambria666!";
$database = "market";
$port = 888;
// Database connection
$con = mysqli_connect($host, $username, $password, $database, $port);

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit();
}

// Function to retrieve products based on parameters
function getProducts($con, $categoryId = null, $categoryName = null) {
    if ($categoryId !== null && $categoryName !== null) {
        // Prepare a query to fetch products that match both category_id and category_name
        $query = "SELECT * FROM products WHERE category_id = ? AND category_name = ? ORDER BY id";
        $stmt = $con->prepare($query);
        $stmt->bind_param("is", $categoryId, $categoryName);
    } else {
        // Fetch all products if no category_id or category_name provided
        $query = "SELECT * FROM products ORDER BY id";
        $stmt = $con->prepare($query);
    }
    $stmt->execute();
    return $stmt->get_result();
}

// Function to retrieve product category name by ID
function getCategoryName($con, $categoryId) {
    $query = "SELECT name FROM categories WHERE id = '$categoryId'";
    $result = mysqli_query($con, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return $row['name'];
    }
    return "Unknown";
}

// Function to retrieve vendor information by vendor name
function getVendorInfo($con, $vendorName) {
    $query = "SELECT username, vendor_rating, total_orders, level FROM register WHERE username = '$vendorName'";
    $result = mysqli_query($con, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    return false; // Return false if vendor information is not found
}

// Function to retrieve average Bitcoin price in USD from multiple exchanges
function getAverageBitcoinPriceUSD() {
    // Array of API endpoints for Bitcoin prices from different exchanges
    $api_endpoints = [
        'https://api.coindesk.com/v1/bpi/currentprice.json',
        'https://api.blockchain.com/v3/exchange/tickers/BTC-USD',
        'https://api.coinbase.com/v2/prices/spot?currency=USD'
        // Add more API endpoints here for other exchanges if needed
    ];

    // Array to store Bitcoin prices
    $bitcoin_prices = [];

    // Fetch Bitcoin prices from each exchange API endpoint
    foreach ($api_endpoints as $endpoint) {
        $response = @file_get_contents($endpoint);
        
        if ($response !== false) {
            $data = json_decode($response, true);

            // Extract Bitcoin price from each API response
            $price = null;

            // Extracting price from each API response
            switch ($endpoint) {
                case 'https://api.coindesk.com/v1/bpi/currentprice.json':
                    if (isset($data['bpi']['USD']['rate_float'])) {
                        $price = $data['bpi']['USD']['rate_float'];
                    }
                    break;
                case 'https://api.blockchain.com/v3/exchange/tickers/BTC-USD':
                    if (isset($data['last_trade_price'])) {
                        $price = $data['last_trade_price'];
                    }
                    break;
                case 'https://api.coinbase.com/v2/prices/spot?currency=USD':
                    if (isset($data['data']['amount'])) {
                        $price = $data['data']['amount'];
                    }
                    break;
                // Add cases for other exchange endpoints here if needed
            }

            // Add the price to the array if it's valid
            if ($price !== null) {
                $bitcoin_prices[] = floatval($price);
            }
        }
    }
    
    // Calculate the average Bitcoin price
    $average_price = count($bitcoin_prices) > 0 ? array_sum($bitcoin_prices) / count($bitcoin_prices) : 0;

    return $average_price; // Return the average Bitcoin price
}

// Function to convert USD price to Bitcoin
function convertToBitcoin($usdPrice) {
    // Get the average Bitcoin price in USD
    $bitcoinPriceUSD = getAverageBitcoinPriceUSD();
    
    // Avoid division by zero errors
    if ($bitcoinPriceUSD === 0) {
        return 0; // Return zero if Bitcoin price in USD is not available or zero
    }

    // Convert USD price to Bitcoin using the average Bitcoin price
    return $usdPrice / $bitcoinPriceUSD;
}

/// Pagination variables
$rows_per_page = 10;
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$start = ($current_page - 1) * $rows_per_page;

// Database connection
$con = mysqli_connect($host, $username, $password, $database, $port);
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit();
}

// Get category ID from request or default to 0
$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;

// Check if category_id is 0
if ($category_id == 0) {
    // Query to fetch all products
    $products_query = "SELECT * FROM products ORDER BY id LIMIT $start, $rows_per_page";
    $result = mysqli_query($con, $products_query);

    // Query to count total records
    $total_records_query = "SELECT COUNT(*) AS total FROM products";
    $total_records_result = mysqli_query($con, $total_records_query);
    $total_records_row = mysqli_fetch_assoc($total_records_result);
    $total_records = isset($total_records_row['total']) ? $total_records_row['total'] : 0;

    // Calculate total pages
    $total_pages = $total_records > 0 ? ceil($total_records / $rows_per_page) : 1;

    // Display products
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            // Fetching and processing data
            $categoryName = getCategoryName($con, $row['category_id']);
            $vendorName = isset($row['vendor_name']) ? $row['vendor_name'] : "Unknown";

            // Fetch vendor information
            $vendorInfo = getVendorInfo($con, $vendorName);
            if ($vendorInfo) {
                $vendorRating = $vendorInfo['vendor_rating'];
                $totalOrders = $vendorInfo['total_orders'];
                $vendorLevel = $vendorInfo['level'];
            } else {
                $vendorRating = 0;
                $totalOrders = 0;
                $vendorLevel = 0;
            }

            $timesSold = isset($row['times_sold_last_48_hr']) ? $row['times_sold_last_48_hr'] : 0;
            $totalSold = isset($row['total_sold']) ? $row['total_sold'] : 0;
            $shipsFrom = isset($row['ships_from']) ? $row['ships_from'] : "Unknown";
            $shipsTo = isset($row['ships_to']) ? $row['ships_to'] : "Unknown";
            $sellingPrice = isset($row['selling_price']) ? $row['selling_price'] : 0;

            // Convert USD price to Bitcoin
            $bitcoinPrice = convertToBitcoin($sellingPrice);

            // Format the selling price as currency
            $formatted_price = formatCurrency($sellingPrice);

            // Get product name and encode it properly
            $productName = htmlspecialchars($row['name']);
            $productUrlName = rawurlencode($productName);
            $mobileProductUrl = 'product-view-mobile.php?name=' . $productUrlName;
            // Display product details
            echo '<div class="product-listing">
                <div class="product-link">
                    <div class="product">
                        <div class="container">
                            <div class="product-photo">
                                <img src="uploads/' . ($row['image'] ?? 'default.jpg') . '" alt="' . $productName . '">
                            </div>
                            <div class="product-details">
                                <div class="product-heading">
                                    <h2><a href="product-view-mobile.php?name=' . $productName . '" class="product-link">' . $productName . '</a></h2>
                                    <span class="shadow-text smalltext">In <strong>' . htmlspecialchars($categoryName) . '</strong></span><br>
                                    <span><b>Sold By <a href="#">' . htmlspecialchars($vendorName) . '</a> (<img src="images/icons8-star-48.png" style="height: 13.2px; width:13.2px;" alt="Rating">' . htmlspecialchars($vendorRating) . ')</b><span class="badge badge-pill-level-1" style="display:inline-block;vertical-align: middle;margin-bottom: 1px; margin-left:5px;">Level ' . htmlspecialchars($vendorLevel) . '</span></span>
                                    <div class="sprite sprite--shopping-cart" title="Total Sales" style="float: none; display: inline-flex; margin-left:5px;"><img src="images/shopping-cart.png" style="width:18px;height:18px; margin-top:2px;" alt="Total Sales">' . htmlspecialchars($totalOrders) . '</div>
                                    <br>
                                    <span><b>Shipped From</b> ' . htmlspecialchars($shipsFrom) . '</span><br>
                                    <span><b>Shipped To</b> ' . htmlspecialchars($shipsTo) . '</span><br>
                                </div>
                                <div class="product-details-bottom">
                                    <div class="sold-amount smalltext">Sold ' . htmlspecialchars($timesSold) . ' in the last 48 hours</div>
                                    <span class="smalltext">Sold ' . htmlspecialchars($totalSold) . ' in total</span>
                                </div>
                            </div>
                            <div class="product-price">
                                <span class="badge badge-primary">Unlimited Available</span>
                                <h2>USD ' . $formatted_price . '</h2>
                                <span class="shadow-text smalltext boldtext">' . number_format($bitcoinPrice, 8) . ' BTC</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>';
        }
    } else {
        echo '<p>No products found.</p>';
    }
} else {
    // Query to fetch products by category ID
    $products_query = "SELECT * FROM products WHERE category_id = $category_id ORDER BY id LIMIT $start, $rows_per_page";
    $result = mysqli_query($con, $products_query);

    // Query to count total records for the category
    $total_records_query = "SELECT COUNT(*) AS total FROM products WHERE category_id = $category_id";
    $total_records_result = mysqli_query($con, $total_records_query);
    $total_records_row = mysqli_fetch_assoc($total_records_result);
    $total_records = isset($total_records_row['total']) ? $total_records_row['total'] : 0;

    // Calculate total pages
    $total_pages = $total_records > 0 ? ceil($total_records / $rows_per_page) : 1;

    // Display products
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            // Fetching and processing data
            $categoryName = getCategoryName($con, $row['category_id']);
            $vendorName = isset($row['vendor_name']) ? $row['vendor_name'] : "Unknown";

            // Fetch vendor information
            $vendorInfo = getVendorInfo($con, $vendorName);
            if ($vendorInfo) {
                $vendorRating = $vendorInfo['vendor_rating'];
                $totalOrders = $vendorInfo['total_orders'];
                $vendorLevel = $vendorInfo['level'];
            } else {
                $vendorRating = 0;
                $totalOrders = 0;
                $vendorLevel = 0;
            }

            $timesSold = isset($row['times_sold_last_48_hr']) ? $row['times_sold_last_48_hr'] : 0;
            $totalSold = isset($row['total_sold']) ? $row['total_sold'] : 0;
            $shipsFrom = isset($row['ships_from']) ? $row['ships_from'] : "Unknown";
            $shipsTo = isset($row['ships_to']) ? $row['ships_to'] : "Unknown";
            $sellingPrice = isset($row['selling_price']) ? $row['selling_price'] : 0;

            // Convert USD price to Bitcoin
            $bitcoinPrice = convertToBitcoin($sellingPrice);

            // Format the selling price as currency
            $formatted_price = formatCurrency($sellingPrice);

            // Get product name and encode it properly
            $productName = htmlspecialchars($row['name']);
            $productUrlName = rawurlencode($productName);
            $mobileProductUrl = 'product-view-mobile.php?name=' . $productUrlName;
            // Display product details
            echo '<div class="product-listing">
                <div class="product-link">
                    <div class="product">
                        <div class="container">
                            <div class="product-photo">
                                <img src="uploads/' . ($row['image'] ?? 'default.jpg') . '" alt="' . $productName . '">
                            </div>
                            <div class="product-details">
                                <div class="product-heading">
                                    <h2><a href="product-view-mobile.php?name=' . $productName . '" class="product-link">' . $productName . '</a></h2>
                                    <span class="shadow-text smalltext">In <strong>' . htmlspecialchars($categoryName) . '</strong></span><br>
                                    <span><b>Sold By <a href="#">' . htmlspecialchars($vendorName) . '</a> (<img src="images/icons8-star-48.png" style="height: 13.2px; width:13.2px;" alt="Rating">' . htmlspecialchars($vendorRating) . ')</b><span class="badge badge-pill-level-1" style="display:inline-block;vertical-align: middle;margin-bottom: 1px; margin-left:5px;">Level ' . htmlspecialchars($vendorLevel) . '</span></span>
                                    <div class="sprite sprite--shopping-cart" title="Total Sales" style="float: none; display: inline-flex; margin-left:5px;"><img src="images/shopping-cart.png" style="width:18px;height:18px; margin-top:2px;" alt="Total Sales">' . htmlspecialchars($totalOrders) . '</div>
                                    <br>
                                    <span><b>Shipped From</b> ' . htmlspecialchars($shipsFrom) . '</span><br>
                                    <span><b>Shipped To</b> ' . htmlspecialchars($shipsTo) . '</span><br>
                                </div>
                                <div class="product-details-bottom">
                                    <div class="sold-amount smalltext">Sold ' . htmlspecialchars($timesSold) . ' in the last 48 hours</div>
                                    <span class="smalltext">Sold ' . htmlspecialchars($totalSold) . ' in total</span>
                                </div>
                            </div>
                            <div class="product-price">
                                <span class="badge badge-primary">Unlimited Available</span>
                                <h2>USD ' . $formatted_price . '</h2>
                                <span class="shadow-text smalltext boldtext">' . number_format($bitcoinPrice, 8) . ' BTC</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>';
        }
    } else {
        echo '<p>No products found for this category.</p>';
    }
}

// Display pagination
/*if (isset($total_pages) && $total_pages > 1) {
    echo '<div class="pagination">';
    if ($current_page > 1) {
        echo '<a href="?page=' . ($current_page - 1) . '&category_id=' . $category_id . '">&laquo; Previous</a>';
    }
    for ($i = 1; $i <= $total_pages; $i++) {
        echo '<a href="?page=' . $i . '&category_id=' . $category_id . '" ' . ($i == $current_page ? 'class="active"' : '') . '>' . $i . '</a>';
    }
    if ($current_page < $total_pages) {
        echo '<a href="?page=' . ($current_page + 1) . '&category_id=' . $category_id . '">Next &raquo;</a>';
    }
    echo '</div>';
}*/

// Close the database connection
mysqli_close($con);
?>

    </div>
<ul class="pagination justify-content-end">
    <li class="page-item <?php echo ($current_page == 1) ? 'disabled' : ''; ?>">
        <a class="page-link" href="listings.php?page=1" tabindex="-1" aria-disabled="true">First</a>
    </li>
    <li class="page-item <?php echo ($current_page == 1) ? 'disabled' : ''; ?>">
        <a class="page-link" href="listings.php?page=<?php echo $current_page - 1; ?>" tabindex="-1" aria-disabled="true">Previous</a>
    </li>
    <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
        <li class="page-item <?php echo ($i == $current_page) ? 'active' : ''; ?>">
            <a class="page-link" href="listings.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
        </li>
    <?php endfor; ?>
    <li class="page-item <?php echo ($current_page == $total_pages || $total_pages == 0) ? 'disabled' : ''; ?>">
        <a class="page-link" href="listings.php?page=<?php echo $current_page + 1; ?>">Next</a>
    </li>
    <li class="page-item <?php echo ($current_page == $total_pages || $total_pages == 0) ? 'disabled' : ''; ?>">
        <a class="page-link" href="listings.php?page=<?php echo $total_pages; ?>">Last</a>
    </li>
</ul>

<div style="margin-right: auto; padding: .5rem .75rem;">
    Showing products <?php echo $start + 1; ?> to <?php echo min($start + $rows_per_page, $total_records); ?> of <?php echo $total_records; ?> Total items
</div>

<?php
// Example: Loop through retrieved products and display
//if (mysqli_num_rows($result) > 0) {
    //while ($row = mysqli_fetch_assoc($result)) {
        // Display or process each product row as needed
        //echo "Product ID: " . $row['id'] . " - Name: " . $row['name'] . "<br>";
    //}
//} else {
    //echo "No products found.";
//} 
?>


</div>
</div>
</div>
</body>
</html>




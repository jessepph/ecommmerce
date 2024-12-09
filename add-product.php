
  

<?php
include('alertify.php');
session_start();
require_once("db.php");

// Enable error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

function getAll($table) {
    global $conn;
    $query = "SELECT * FROM $table";
    return mysqli_query($conn, $query);
}

// Retrieve logged-in username from session
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
} else {
    header("Location: login.php");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['vendor_name'])) {
        $vendorNameInput = mysqli_real_escape_string($conn, $_POST['vendor_name']);

        if ($vendorNameInput !== $username) {
            echo '<script>
                    $(document).ready(function () {
                        alertify.error("You must type in the exact name of your vendor account.");
                    });
                  </script>';
        } else {
            // Prepare data for insertion
            $category_id = mysqli_real_escape_string($conn, $_POST['category_id']);
            $name = mysqli_real_escape_string($conn, $_POST['name']);
            $slug = mysqli_real_escape_string($conn, $_POST['slug']);
            $product_description = mysqli_real_escape_string($conn, $_POST['product_description']);
            $terms_refund_policy = mysqli_real_escape_string($conn, $_POST['terms_refund_policy']);
            $original_price = mysqli_real_escape_string($conn, $_POST['original_price']);
            $selling_price = mysqli_real_escape_string($conn, $_POST['selling_price']);
            $qty = mysqli_real_escape_string($conn, $_POST['qty']);
            $meta_title = mysqli_real_escape_string($conn, $_POST['meta_title']);
            $meta_keywords = mysqli_real_escape_string($conn, $_POST['meta_keywords']);
            $meta_description = mysqli_real_escape_string($conn, $_POST['meta_description']);
            $shipping_method = mysqli_real_escape_string($conn, $_POST['shipping_method']);
            $shipping_price = mysqli_real_escape_string($conn, $_POST['shipping_price']);
            $available = isset($_POST['available']) ? 1 : 0; // Use integers
            $status = isset($_POST['status']) ? 1 : 0; // Use integers
            $trending = isset($_POST['trending']) ? 1 : 0; // Use integers
            $ships_from = mysqli_real_escape_string($conn, $_POST['ships_from']);
            $ships_to = mysqli_real_escape_string($conn, $_POST['ships_to']);
            $created_at = date('Y-m-d H:i:s'); // Current timestamp
            $times_sold_last_48_hr = 0; // Default value
            $total_sold = 0; // Default value
            $filename = '';

            // File upload handling
            if (isset($_FILES['image']['name']) && !empty($_FILES['image']['name'])) {
                $image = $_FILES['image']['name'];
                $image_ext = pathinfo($image, PATHINFO_EXTENSION);
                $filename = time() . '.' . $image_ext;
                move_uploaded_file($_FILES['image']['tmp_name'], "uploads/" . $filename);
            }

            // Construct the INSERT query
            $product_query = "INSERT INTO products 
                (category_id, product_name, vendor_name, product_description, terms_refund_policy, 
                original_price, selling_price, image, qty, status, trending, 
                meta_title, meta_keywords, meta_description, created_at, 
                times_sold_last_48_hr, total_sold, ships_to, ships_from, 
                shipping_method, shipping_price, available, slug, vendor_rating) 
                VALUES (
                    '$category_id', 
                    '$name', 
                    '$vendorNameInput', 
                    '$product_description', 
                    '$terms_refund_policy', 
                    '$original_price', 
                    '$selling_price', 
                    '$filename', 
                    '$qty', 
                    '$status', 
                    '$trending', 
                    '$meta_title', 
                    '$meta_keywords', 
                    '$meta_description', 
                    '$created_at', 
                    '$times_sold_last_48_hr', 
                    '$total_sold', 
                    '$ships_to', 
                    '$ships_from', 
                    '$shipping_method', 
                    '$shipping_price', 
                    '$available', 
                    '$slug', 
                    '0' -- Assuming default vendor_rating as 0
                )";

            // Execute the query
            if (mysqli_query($conn, $product_query)) {
                echo '<script>
                        $(document).ready(function () {
                            alertify.success("Product Added Successfully");
                        });
                      </script>';
            } else {
                echo '<script>
                        $(document).ready(function () {
                            alertify.error("Something went wrong: ' . mysqli_error($conn) . '");
                        });
                      </script>';
            }
        }
    }
    mysqli_close($conn);
}
?>

<html>
    <head>
    <title>Add Product</title>
            <meta http-equiv="Content-type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/jpeg" href="img/pentagram.jpg">
        <link rel="stylesheet" type="text/css" href="Bohemia%20-%20Homepage_files/flexboxgrid.min.css">
        <link rel="stylesheet" type="text/css" href="Bohemia%20-%20Homepage_files/fontawesome-all.min.css">
        <link rel="stylesheet" type="text/css" href="Bohemia%20-%20Homepage_files/style.css">
        <link rel="stylesheet" type="text/css" href="Bohemia%20-%20Homepage_files/main.css">
        <link rel="stylesheet" type="text/css" href="Bohemia%20-%20Homepage_files/responsive.css">        
        <link rel="stylesheet" type="text/css" href="Bohemia%20-%20Homepage_files/sprite.css">
        <link rel="icon" type="image/jpeg" href="img/pentagram.jpg">
        <link rel="stylesheet" href="day.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/alertify.js/1.13.1/alertify.min.js"></script>
    <style>
  .alertify-notifier {
    width: 400px; /* Adjust width as needed */
    height: auto; /* Adjust height as needed */
    font-size: 16px; /* Adjust font size as needed */
  }

  .alertify-log .alertify-message {
    padding: 20px; /* Adjust padding to make the notification box larger */
  }

  .alertify-log {
    transition: opacity 0.5s ease-in-out;
  }
</style>
    
        
</head>

<body>
   
              <div class="navigation">
        <div class="wrapper">
            <ul>
                <li class="nav-logo"><a href="homepage.php"><img src="Bohemia%20-%20Homepage_files/logo_small.png" style="height: 100%;"></a></li>
                <div class="responsive-menu">
                    <li class="menu-toggler"><a href="#">Navigation&nbsp; <div class="sprite sprite--caret" style="float: none; display: inline-block; margin-left:5px;"></div></a></li>
                    <div class="menu-links">
                        <li class=""><a href="homepage.php">Home</a></li>
                        
                        <li class="dropdown-link dropdown-large ">
                            <a href="orders.php?action=orders" class="dropbtn">
                                Orders
                                                            </a>
                            <div class="dropdown-content right-dropdown">
                                <a href="orders.php?action=orprocessing">Processing&nbsp; <span class="badge badge-secondary right">0</span></a>
                                <a href="orders.php?action=dispatched">Dispatched&nbsp; <span class="badge badge-danger right" style="background-color:grey;">0</span></a>
                                <a href="orders.php?action=completed">Completed&nbsp; <span class="badge badge-danger right" style= "background-color:grey">0</span></a>
                                <a href="orders.php?action=disputed">Disputed&nbsp; <span class="badge badge-secondary right">0</span></a>
                                <a href="orders.php?action=canceled">Canceled</a>
                            </div>
                        </li>

                        
                        <li><a href="listings.php">Listings</a></li>
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
			    <a href="wallet.php" class="dropbtn">Wallet</a>
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
                            <li class="dropdown-link dropdown-large " style="margin-left:260px; position:absolute; width:210px; margin-top:-15px;">
                            <a href="categories.php" class="dropbtn">
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
                           
                        <a href="edit-category.php">
                                Edit Category
                                                                    </a>
                </div>
              
              
              
               
                <li class="dropdown-link user-nav right fix-gap">
                <div class="dropdown">
              <button class="dropbtn"><?php echo "" . $_SESSION["username"] . "<br>"; ?></button>
                   
                 
                    <div class="dropdown-content">
                       
                        <div class="user-balance">
                            <span class="shadow-text">Balances</span><br>
                            <span class="balance"></span><sup>0.00000000 BTC</sup><br><span class="balance"></span><sup>0.00000000 XMR</sup><br>
                        </div>
                                                <a href="profile.php?id=60Agent">My Profile</a>

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
                <li class="right ">
                    <a href="notifications.php">
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
                        background-color:grey;
                        border-radius: 0.25rem;
                        ">0</span>                    </a>
                </li>
                
                <li class="right fix-gap "><a href="become-a-merchant.php"><b>Become A Merchant</b></a></li>

                                        
            </ul>
        </div>
    </div>
        <div class="wrapper">
                    <div class="row">
                <div class="col-md-12" style="padding-left: 0;">
                    <div class="alert alert-primary">
                        <strong>Current Private Mirror:</strong> buyerajqj4pjnmlkedzma6gok7beynyl3v5tnyx4isv6cyzl5hvmadid.onion
                        <br>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12" style="padding-left: 0;">
                    <div class="alert alert-primary">
                        <strong>Cannabia Private Mirror:</strong> smoker3rw32tfgolpi576lnfthp5dp3zvhx2eqzyx4a45rorkyksurid.onion
                        <br>
                    </div>
                </div>
            </div>
        
            <div class="row">
                <div class="col-md-12" style="padding-left: 0;">
                    <div class="alert alert-danger">
                        <div class="sprite sprite--triangle"></div>&nbsp;
                        <strong>YOU DO NOT HAVE A PGP KEY ASSIGNED TO YOUR ACCOUNT, PLEASE ATTACH ONE IN THE USER PANEL</strong>
                    </div>
                </div>
            </div>




        
        

  <header></header>

    <div class="container">
        <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary">
                    <h4 class="text-white" style="text-align:center; font-size:larger;">Add Product</h4>
                </div>
                <div class="card-body">
                        <form action="add-product.php" method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="mb-0">Select Category</label>
                                    <select name="category_id" class="form-select mb-2" >
                                       
                                        <?php 
                                        $queryVendorName = "SELECT vendor_name FROM products";
                                        $resultVendorName = mysqli_query($conn, $queryVendorName);
                                        $rowVendorName = mysqli_fetch_array($resultVendorName);
                                        $queryUsername = "SELECT username FROM register";
                                        $resultUsername= mysqli_query($conn, $queryUsername);
                                        $rowUsername = mysqli_fetch_array($resultUsername);
                                            $categories = getAll("categories");

                                            if(mysqli_num_rows($categories) > 0)
                                            {
                                                foreach ($categories as $item) {
                                                    ?>
                                                        <option class="form-control" value="<?= $item['id']; ?>"><?= $item['name']; ?></option>
                                                    <?php
                                                }
                                            }
                                            else
                                            {
                                                echo "No category available";
                                            }

                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="mb-0">Name</label>
                                    <input type="text" autocomplete="false" required name="name" placeholder="Enter Product Name" class="form-control mb-2">
                                </div>
                                
                                 <div class="col-md-6">
                                    <label class="mb-0" style="">Vendor Name: <?=$_SESSION['username'];?></label>
                                    <input type="text" autocomplete="false" required name="vendor_name" placeholder=<?= $_SESSION['username'];?> class="form-control mb-2">
                                </div>
                                <div class="col-md-6">
                                    <label class="mb-0">Shipping Price</label>
                                    <input type="text" autocomplete="false" required name="shipping_price" placeholder="Enter Shipping Price" class="form-control mb-2">
                                </div>
                                <div class="col-md-6">
                                    <label class="mb-0">Shipping Method</label>
                                    <input type="text" autocomplete="false" required name="shipping_method" placeholder="Enter Shipping Method" class="form-control mb-2">
                                </div>
                                <div class="col-md-6">
                                    <label class="mb-0">Ships From</label>
                                    
                                    <select class="form-control" name="ships_from">
                    <option selected="selected"></option>
                    <option value="Worldwide">Worldwide</option>
                    <option value="Afghanistan">Afghanistan</option>
                    <option value="Albania">Albania</option>
                    <option value="Algeria">Algeria</option>
                    <option value="American Samoa">American Samoa</option>
                    <option value="Andorra">Andorra</option>
                    <option value="Angola">Angola</option>
                    <option value="Anguilla">Anguilla</option>
                    <option value="Antarctica">Antarctica</option>
                    <option value="Antigua and Barbuda">Antigua and Barbuda</option>
                    <option value="Argentina">Argentina</option>
                    <option value="Armenia">Armenia</option>
                    <option value="Aruba">Aruba</option>
                    <option value="Australia">Australia</option>
                    <option value="1Austria">Austria</option>
                    <option value="Azerbaijan">Azerbaijan</option>
                    <option value="Bahamas">Bahamas</option>
                    <option value="Bahrain">Bahrain</option>
                    <option value="Bangladesh">Bangladesh</option>
                    <option value="Barbados">Barbados</option>
                    <option value="Belarus">Belarus</option>
                    <option value="Belgium">Belgium</option>
                    <option value="Belize">Belize</option>
                    <option value="Benin">Benin</option>
                    <option value="Bermuda">Bermuda</option>
                    <option value="Bhutan">Bhutan</option>
                    <option value="Bolivia">Bolivia</option>
                    <option value="Bosnia and Herzegovina">Bosnia and Herzegovina</option>
                    <option value="Botswana">Botswana</option>
                    <option value="Bouvet Island">Bouvet Island</option>
                    <option value="Brazil">Brazil</option>
                    <option value="British Indian Ocean Territory">British Indian Ocean Territory</option>
                    <option value="Brunei Darussalam">Brunei Darussalam</option>
                    <option value="Bulgaria">Bulgaria</option>
                    <option value="Burkina Faso">Burkina Faso</option>
                    <option value="Burundi">Burundi</option>
                    <option value="Cambodia">Cambodia</option>
                    <option value="Cameroon">Cameroon</option>
                    <option value="Canada">Canada</option>
                    <option value="Cape Verde">Cape Verde</option>
                    <option value="Cayman Islands">Cayman Islands</option>
                    <option value="Central African Republic">Central African Republic</option>
                    <option value="Chad">Chad</option>
                    <option value="Chile">Chile</option>
                    <option value="China">China</option>
                    <option value="Christmas Island">Christmas Island</option>
                    <option value="Cocos (Keeling) Islands">Cocos (Keeling) Islands</option>
                    <option value="Colombia">Colombia</option>
                    <option value="Comoros">Comoros</option>
                    <option value="Cook Islands">Cook Islands</option>
                    <option value="Costa Rica">Costa Rica</option>
                    <option value="Croatia (Hrvatska)">Croatia (Hrvatska)</option>
                    <option value="Cuba">Cuba</option>
                    <option value="Cyprus">Cyprus</option>
                    <option value="Czech Republic">Czech Republic</option>
                    <option value="Democratic Republic of the Congo">Democratic Republic of the Congo</option>
                    <option value="Denmark">Denmark</option>
                    <option value="Djibouti">Djibouti</option>
                    <option value="Dominica">Dominica</option>
                    <option value="Dominican Republic">Dominican Republic</option>
                    <option value="East Timor">East Timor</option>
                    <option value="Ecuador">Ecuador</option>
                    <option value="Egypt">Egypt</option>
                    <option value="El Salvador">El Salvador</option>
                    <option value="Equatorial Guinea">Equatorial Guinea</option>
                    <option value="Eritrea">Eritrea</option>
                    <option value="Estonia">Estonia</option>
                    <option value="Ethiopia">Ethiopia</option>
                    <option value="Falkland Islands (Malvinas)">Falkland Islands (Malvinas)</option>
                    <option value="Faroe Islands">Faroe Islands</option>
                    <option value="Fiji">Fiji</option>
                    <option value="Finland">Finland</option>
                    <option value="France">France</option>
                    <option value="France, Metropolitan">France, Metropolitan</option>
                    <option value="French Guiana">French Guiana</option>
                    <option value="French Polynesia">French Polynesia</option>
                    <option value="French Southern Territories">French Southern Territories</option>
                    <option value="Gabon">Gabon</option>
                    <option value="Gambia">Gambia</option>
                    <option value="Georgia">Georgia</option>
                    <option value="Germany">Germany</option>
                    <option value="Ghana">Ghana</option>
                    <option value="Gibraltar">Gibraltar</option>
                    <option value="Greece">Greece</option>
                    <option value="Greenland">Greenland</option>
                    <option value="Grenada">Grenada</option>
                    <option value="Guadeloupe">Guadeloupe</option>
                    <option value="Guam">Guam</option>
                    <option value="Guatemala">Guatemala</option>
                    <option value="Guernsey">Guernsey</option>
                    <option value="Guinea">Guinea</option>
                    <option value="Guinea-Bissau">Guinea-Bissau</option>
                    <option value="Guyana">Guyana</option>
                    <option value="Haiti">Haiti</option>
                    <option value="Heard and Mc Donald Islands">Heard and Mc Donald Islands</option>
                    <option value="Honduras">Honduras</option>
                    <option value="Hong Kong">Hong Kong</option>
                    <option value="Hungary">Hungary</option>
                    <option value="Iceland">Iceland</option>
                    <option value="India">India</option>
                    <option value="Indonesia">Indonesia</option>
                    <option value="1Iran (Islamic Republic of)">Iran (Islamic Republic of)</option>
                    <option value="Iraq">Iraq</option>
                    <option value="Ireland">Ireland</option>
                    <option value="Isle of Man">Isle of Man</option>
                    <option value="1srael">Israel</option>
                    <option value="Italy">Italy</option>
                    <option value="Ivory Coast">Ivory Coast</option>
                    <option value="Jamaica">Jamaica</option>
                    <option value="Japan">Japan</option>
                    <option value="Jersey">Jersey</option>
                    <option value="Jordan">Jordan</option>
                    <option value="Kazakhstan">Kazakhstan</option>
                    <option value="Kenya">Kenya</option>
                    <option value="Kiribati">Kiribati</option>
                    <option value="Korea, Democratic People's Republic of">Korea, Democratic People's Republic of</option>
                    <option value="Korea, Republic of">Korea, Republic of</option>
                    <option value="Kosovo">Kosovo</option>
                    <option value="Kuwait">Kuwait</option>
                    <option value="Kyrgyzstan">Kyrgyzstan</option>
                    <option value="Lao People's Democratic Republic">Lao People's Democratic Republic</option>
                    <option value="Latvia">Latvia</option>
                    <option value="Lebanon">Lebanon</option>
                    <option value="Lesotho">Lesotho</option>
                    <option value="Liberia">Liberia</option>
                    <option value="Libyan Arab Jamahiriya">Libyan Arab Jamahiriya</option>
                    <option value="Liechtenstein">Liechtenstein</option>
                    <option value="Lithuania">Lithuania</option>
                    <option value="Luxembourg">Luxembourg</option>
                    <option value="Macau">Macau</option>
                    <option value="Madagascar">Madagascar</option>
                    <option value="Malawi">Malawi</option>
                    <option value="Malaysia">Malaysia</option>
                    <option value="Maldives">Maldives</option>
                    <option value="Mali">Mali</option>
                    <option value="Malta">Malta</option>
                    <option value="Marshall Islands">Marshall Islands</option>
                    <option value="Martinique">Martinique</option>
                    <option value="Mauritania">Mauritania</option>
                    <option value="Mauritius">Mauritius</option>
                    <option value="Mayotte">Mayotte</option>
                    <option value="Mexico">Mexico</option>
                    <option value="Micronesia, Federated States of">Micronesia, Federated States of</option>
                    <option value="Moldova, Republic of">Moldova, Republic of</option>
                    <option value="Monaco">Monaco</option>
                    <option value="Mongolia">Mongolia</option>
                    <option value="Montenegro">Montenegro</option>
                    <option value="Montserrat">Montserrat</option>
                    <option value="Morocco">Morocco</option>
                    <option value="Mozambique">Mozambique</option>
                    <option value="Myanmar">Myanmar</option>
                    <option value="Namibia">Namibia</option>
                    <option value="Mauru">Nauru</option>
                    <option value="Nepal">Nepal</option>
                    <option value="Netherlands">Netherlands</option>
                    <option value="Netherlands Antilles">Netherlands Antilles</option>
                    <option value="New Caledonia">New Caledonia</option>
                    <option value="New Zealand">New Zealand</option>
                    <option value="Nicaragua">Nicaragua</option>
                    <option value="Niger">Niger</option>
                    <option value="Nigeria">Nigeria</option>
                    <option value="Niue">Niue</option>
                    <option value="Norfolk Island">Norfolk Island</option>
                    <option value="North Macedonia">North Macedonia</option>
                    <option value="Northern Mariana Islands">Northern Mariana Islands</option>
                    <option value="Norway">Norway</option>
                    <option value="Oman">Oman</option>
                    <option value="Pakistan">Pakistan</option>
                    <option value="Palau">Palau</option>
                    <option value="Palestine">Palestine</option>
                    <option value="Panama">Panama</option>
                    <option value="Papua New Guinea">Papua New Guinea</option>
                    <option value="Paraguay">Paraguay</option>
                    <option value="Peru">Peru</option>
                    <option value="Philippines">Philippines</option>
                    <option value="Pitcairn">Pitcairn</option>
                    <option value="Poland">Poland</option>
                    <option value="Portugal">Portugal</option>
                    <option value="Puerto Rico">Puerto Rico</option>
                    <option value="Qatar<">Qatar</option>
                    <option value="Republic of Congo">Republic of Congo</option>
                    <option value="Reunion">Reunion</option>
                    <option value="Romania">Romania</option>
                    <option value="Russian Federation">Russian Federation</option>
                    <option value="Rwanda">Rwanda</option>
                    <option value="Saint Kitts and Nevis">Saint Kitts and Nevis</option>
                    <option value="Saint Lucia">Saint Lucia</option>
                    <option value="Saint Vincent and the Grenadines">Saint Vincent and the Grenadines</option>
                    <option value="Samoa">Samoa</option>
                    <option value="San Marino">San Marino</option>
                    <option value="Sao Tome and Principe">Sao Tome and Principe</option>
                    <option value="Saudi Arabia">Saudi Arabia</option>
                    <option value="Senegal">Senegal</option>
                    <option value="Serbia">Serbia</option>
                    <option value="Seychelles">Seychelles</option>
                    <option value="Sierra Leone">Sierra Leone</option>
                    <option value="Singapore">Singapore</option>
                    <option value="Slovakia">Slovakia</option>
                    <option value="Slovenia">Slovenia</option>
                    <option value="Solomon Islands">Solomon Islands</option>
                    <option value="Somalia">Somalia</option>
                    <option value="South Africa">South Africa</option>
                    <option value="South Georgia South Sandwich Islands">South Georgia South Sandwich Islands</option>
                    <option value="South Sudan">South Sudan</option>
                    <option value="Spain<">Spain</option>
                    <option value="Sri Lanka">Sri Lanka</option>
                    <option value="St. Helena">St. Helena</option>
                    <option value="St. Pierre and Miquelon">St. Pierre and Miquelon</option>
                    <option value="Sudan">Sudan</option>
                    <option value="Suriname">Suriname</option>
                    <option value="Svalbard and Jan Mayen Islands">Svalbard and Jan Mayen Islands</option>
                    <option value="Swaziland">Swaziland</option>
                    <option value="Sweden">Sweden</option>
                    <option value="Switzerland">Switzerland</option>
                    <option value="Syrian Arab Republic">Syrian Arab Republic</option>
                    <option value="Taiwan">Taiwan</option>
                    <option value="Tajikistan">Tajikistan</option>
                    <option value="Tanzania, United Republic of">Tanzania, United Republic of</option>
                    <option value="Thailand">Thailand</option>
                    <option value="Togo">Togo</option>
                    <option value="Tokelau">Tokelau</option>
                    <option value="Tonga">Tonga</option>
                    <option value="Trinidad and Tobago">Trinidad and Tobago</option>
                    <option value="Tunisia">Tunisia</option>
                    <option value="Turkey">Turkey</option>
                    <option value="Turkmenistan">Turkmenistan</option>
                    <option value="Turks and Caicos Islands">Turks and Caicos Islands</option>
                    <option value="Tuvalu">Tuvalu</option>
                    <option value="Uganda">Uganda</option>
                    <option value="Ukraine">Ukraine</option>
                    <option value="United Arab Emirates">United Arab Emirates</option>
                    <option value="United Kingdom<">United Kingdom</option>
                    <option value="United States">United States</option>
                    <option value="Uruguay<">Uruguay</option>
                    <option value="Uzbekistan">Uzbekistan</option>
                    <option value="Vanuatu">Vanuatu</option>
                    <option value="Vatican City State">Vatican City State</option>
                    <option value="Venezuela">Venezuela</option>
                    <option value="Vietnam">Vietnam</option>
                    <option value="Virgin Islands (British)">Virgin Islands (British)</option>
                    <option value="Virgin Islands (U.S.)">Virgin Islands (U.S.)</option>
                    <option value="242">Wallis and Futuna Islands</option>
                    <option value="Wallis and Futuna Islands">Western Sahara</option>
                    <option value="Yemen">Yemen</option>
                    <option value="Zambia">Zambia</option>
                    <option value="2Zimbabwe">Zimbabwe</option>                </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="mb-0">Ships To</label>
                                    
                                   <select class="form-control" name="ships_to">
                    <option selected="selected"></option>
                    <option value="Worldwide">Worldwide</option>
                    <option value="Afghanistan">Afghanistan</option>
                    <option value="Albania">Albania</option>
                    <option value="Algeria">Algeria</option>
                    <option value="American Samoa">American Samoa</option>
                    <option value="Andorra">Andorra</option>
                    <option value="Angola">Angola</option>
                    <option value="Anguilla">Anguilla</option>
                    <option value="Antarctica">Antarctica</option>
                    <option value="Antigua and Barbuda">Antigua and Barbuda</option>
                    <option value="Argentina">Argentina</option>
                    <option value="Armenia">Armenia</option>
                    <option value="Aruba">Aruba</option>
                    <option value="Australia">Australia</option>
                    <option value="1Austria">Austria</option>
                    <option value="Azerbaijan">Azerbaijan</option>
                    <option value="Bahamas">Bahamas</option>
                    <option value="Bahrain">Bahrain</option>
                    <option value="Bangladesh">Bangladesh</option>
                    <option value="Barbados">Barbados</option>
                    <option value="Belarus">Belarus</option>
                    <option value="Belgium">Belgium</option>
                    <option value="Belize">Belize</option>
                    <option value="Benin">Benin</option>
                    <option value="Bermuda">Bermuda</option>
                    <option value="Bhutan">Bhutan</option>
                    <option value="Bolivia">Bolivia</option>
                    <option value="Bosnia and Herzegovina">Bosnia and Herzegovina</option>
                    <option value="Botswana">Botswana</option>
                    <option value="Bouvet Island">Bouvet Island</option>
                    <option value="Brazil">Brazil</option>
                    <option value="British Indian Ocean Territory">British Indian Ocean Territory</option>
                    <option value="Brunei Darussalam">Brunei Darussalam</option>
                    <option value="Bulgaria">Bulgaria</option>
                    <option value="Burkina Faso">Burkina Faso</option>
                    <option value="Burundi">Burundi</option>
                    <option value="Cambodia">Cambodia</option>
                    <option value="Cameroon">Cameroon</option>
                    <option value="Canada">Canada</option>
                    <option value="Cape Verde">Cape Verde</option>
                    <option value="Cayman Islands">Cayman Islands</option>
                    <option value="Central African Republic">Central African Republic</option>
                    <option value="Chad">Chad</option>
                    <option value="Chile">Chile</option>
                    <option value="China">China</option>
                    <option value="Christmas Island">Christmas Island</option>
                    <option value="Cocos (Keeling) Islands">Cocos (Keeling) Islands</option>
                    <option value="Colombia">Colombia</option>
                    <option value="Comoros">Comoros</option>
                    <option value="Cook Islands">Cook Islands</option>
                    <option value="Costa Rica">Costa Rica</option>
                    <option value="Croatia (Hrvatska)">Croatia (Hrvatska)</option>
                    <option value="Cuba">Cuba</option>
                    <option value="Cyprus">Cyprus</option>
                    <option value="Czech Republic">Czech Republic</option>
                    <option value="Democratic Republic of the Congo">Democratic Republic of the Congo</option>
                    <option value="Denmark">Denmark</option>
                    <option value="Djibouti">Djibouti</option>
                    <option value="Dominica">Dominica</option>
                    <option value="Dominican Republic">Dominican Republic</option>
                    <option value="East Timor">East Timor</option>
                    <option value="Ecuador">Ecuador</option>
                    <option value="Egypt">Egypt</option>
                    <option value="El Salvador">El Salvador</option>
                    <option value="Equatorial Guinea">Equatorial Guinea</option>
                    <option value="Eritrea">Eritrea</option>
                    <option value="Estonia">Estonia</option>
                    <option value="Ethiopia">Ethiopia</option>
                    <option value="Falkland Islands (Malvinas)">Falkland Islands (Malvinas)</option>
                    <option value="Faroe Islands">Faroe Islands</option>
                    <option value="Fiji">Fiji</option>
                    <option value="Finland">Finland</option>
                    <option value="France">France</option>
                    <option value="France, Metropolitan">France, Metropolitan</option>
                    <option value="French Guiana">French Guiana</option>
                    <option value="French Polynesia">French Polynesia</option>
                    <option value="French Southern Territories">French Southern Territories</option>
                    <option value="Gabon">Gabon</option>
                    <option value="Gambia">Gambia</option>
                    <option value="Georgia">Georgia</option>
                    <option value="Germany">Germany</option>
                    <option value="Ghana">Ghana</option>
                    <option value="Gibraltar">Gibraltar</option>
                    <option value="Greece">Greece</option>
                    <option value="Greenland">Greenland</option>
                    <option value="Grenada">Grenada</option>
                    <option value="Guadeloupe">Guadeloupe</option>
                    <option value="Guam">Guam</option>
                    <option value="Guatemala">Guatemala</option>
                    <option value="Guernsey">Guernsey</option>
                    <option value="Guinea">Guinea</option>
                    <option value="Guinea-Bissau">Guinea-Bissau</option>
                    <option value="Guyana">Guyana</option>
                    <option value="Haiti">Haiti</option>
                    <option value="Heard and Mc Donald Islands">Heard and Mc Donald Islands</option>
                    <option value="Honduras">Honduras</option>
                    <option value="Hong Kong">Hong Kong</option>
                    <option value="Hungary">Hungary</option>
                    <option value="Iceland">Iceland</option>
                    <option value="India">India</option>
                    <option value="Indonesia">Indonesia</option>
                    <option value="1Iran (Islamic Republic of)">Iran (Islamic Republic of)</option>
                    <option value="Iraq">Iraq</option>
                    <option value="Ireland">Ireland</option>
                    <option value="Isle of Man">Isle of Man</option>
                    <option value="1srael">Israel</option>
                    <option value="Italy">Italy</option>
                    <option value="Ivory Coast">Ivory Coast</option>
                    <option value="Jamaica">Jamaica</option>
                    <option value="Japan">Japan</option>
                    <option value="Jersey">Jersey</option>
                    <option value="Jordan">Jordan</option>
                    <option value="Kazakhstan">Kazakhstan</option>
                    <option value="Kenya">Kenya</option>
                    <option value="Kiribati">Kiribati</option>
                    <option value="Korea, Democratic People's Republic of">Korea, Democratic People's Republic of</option>
                    <option value="Korea, Republic of">Korea, Republic of</option>
                    <option value="Kosovo">Kosovo</option>
                    <option value="Kuwait">Kuwait</option>
                    <option value="Kyrgyzstan">Kyrgyzstan</option>
                    <option value="Lao People's Democratic Republic">Lao People's Democratic Republic</option>
                    <option value="Latvia">Latvia</option>
                    <option value="Lebanon">Lebanon</option>
                    <option value="Lesotho">Lesotho</option>
                    <option value="Liberia">Liberia</option>
                    <option value="Libyan Arab Jamahiriya">Libyan Arab Jamahiriya</option>
                    <option value="Liechtenstein">Liechtenstein</option>
                    <option value="Lithuania">Lithuania</option>
                    <option value="Luxembourg">Luxembourg</option>
                    <option value="Macau">Macau</option>
                    <option value="Madagascar">Madagascar</option>
                    <option value="Malawi">Malawi</option>
                    <option value="Malaysia">Malaysia</option>
                    <option value="Maldives">Maldives</option>
                    <option value="Mali">Mali</option>
                    <option value="Malta">Malta</option>
                    <option value="Marshall Islands">Marshall Islands</option>
                    <option value="Martinique">Martinique</option>
                    <option value="Mauritania">Mauritania</option>
                    <option value="Mauritius">Mauritius</option>
                    <option value="Mayotte">Mayotte</option>
                    <option value="Mexico">Mexico</option>
                    <option value="Micronesia, Federated States of">Micronesia, Federated States of</option>
                    <option value="Moldova, Republic of">Moldova, Republic of</option>
                    <option value="Monaco">Monaco</option>
                    <option value="Mongolia">Mongolia</option>
                    <option value="Montenegro">Montenegro</option>
                    <option value="Montserrat">Montserrat</option>
                    <option value="Morocco">Morocco</option>
                    <option value="Mozambique">Mozambique</option>
                    <option value="Myanmar">Myanmar</option>
                    <option value="Namibia">Namibia</option>
                    <option value="Mauru">Nauru</option>
                    <option value="Nepal">Nepal</option>
                    <option value="Netherlands">Netherlands</option>
                    <option value="Netherlands Antilles">Netherlands Antilles</option>
                    <option value="New Caledonia">New Caledonia</option>
                    <option value="New Zealand">New Zealand</option>
                    <option value="Nicaragua">Nicaragua</option>
                    <option value="Niger">Niger</option>
                    <option value="Nigeria">Nigeria</option>
                    <option value="Niue">Niue</option>
                    <option value="Norfolk Island">Norfolk Island</option>
                    <option value="North Macedonia">North Macedonia</option>
                    <option value="Northern Mariana Islands">Northern Mariana Islands</option>
                    <option value="Norway">Norway</option>
                    <option value="Oman">Oman</option>
                    <option value="Pakistan">Pakistan</option>
                    <option value="Palau">Palau</option>
                    <option value="Palestine">Palestine</option>
                    <option value="Panama">Panama</option>
                    <option value="Papua New Guinea">Papua New Guinea</option>
                    <option value="Paraguay">Paraguay</option>
                    <option value="Peru">Peru</option>
                    <option value="Philippines">Philippines</option>
                    <option value="Pitcairn">Pitcairn</option>
                    <option value="Poland">Poland</option>
                    <option value="Portugal">Portugal</option>
                    <option value="Puerto Rico">Puerto Rico</option>
                    <option value="Qatar<">Qatar</option>
                    <option value="Republic of Congo">Republic of Congo</option>
                    <option value="Reunion">Reunion</option>
                    <option value="Romania">Romania</option>
                    <option value="Russian Federation">Russian Federation</option>
                    <option value="Rwanda">Rwanda</option>
                    <option value="Saint Kitts and Nevis">Saint Kitts and Nevis</option>
                    <option value="Saint Lucia">Saint Lucia</option>
                    <option value="Saint Vincent and the Grenadines">Saint Vincent and the Grenadines</option>
                    <option value="Samoa">Samoa</option>
                    <option value="San Marino">San Marino</option>
                    <option value="Sao Tome and Principe">Sao Tome and Principe</option>
                    <option value="Saudi Arabia">Saudi Arabia</option>
                    <option value="Senegal">Senegal</option>
                    <option value="Serbia">Serbia</option>
                    <option value="Seychelles">Seychelles</option>
                    <option value="Sierra Leone">Sierra Leone</option>
                    <option value="Singapore">Singapore</option>
                    <option value="Slovakia">Slovakia</option>
                    <option value="Slovenia">Slovenia</option>
                    <option value="Solomon Islands">Solomon Islands</option>
                    <option value="Somalia">Somalia</option>
                    <option value="South Africa">South Africa</option>
                    <option value="South Georgia South Sandwich Islands">South Georgia South Sandwich Islands</option>
                    <option value="South Sudan">South Sudan</option>
                    <option value="Spain<">Spain</option>
                    <option value="Sri Lanka">Sri Lanka</option>
                    <option value="St. Helena">St. Helena</option>
                    <option value="St. Pierre and Miquelon">St. Pierre and Miquelon</option>
                    <option value="Sudan">Sudan</option>
                    <option value="Suriname">Suriname</option>
                    <option value="Svalbard and Jan Mayen Islands">Svalbard and Jan Mayen Islands</option>
                    <option value="Swaziland">Swaziland</option>
                    <option value="Sweden">Sweden</option>
                    <option value="Switzerland">Switzerland</option>
                    <option value="Syrian Arab Republic">Syrian Arab Republic</option>
                    <option value="Taiwan">Taiwan</option>
                    <option value="Tajikistan">Tajikistan</option>
                    <option value="Tanzania, United Republic of">Tanzania, United Republic of</option>
                    <option value="Thailand">Thailand</option>
                    <option value="Togo">Togo</option>
                    <option value="Tokelau">Tokelau</option>
                    <option value="Tonga">Tonga</option>
                    <option value="Trinidad and Tobago">Trinidad and Tobago</option>
                    <option value="Tunisia">Tunisia</option>
                    <option value="Turkey">Turkey</option>
                    <option value="Turkmenistan">Turkmenistan</option>
                    <option value="Turks and Caicos Islands">Turks and Caicos Islands</option>
                    <option value="Tuvalu">Tuvalu</option>
                    <option value="Uganda">Uganda</option>
                    <option value="Ukraine">Ukraine</option>
                    <option value="United Arab Emirates">United Arab Emirates</option>
                    <option value="United Kingdom<">United Kingdom</option>
                    <option value="United States">United States</option>
                    <option value="Uruguay<">Uruguay</option>
                    <option value="Uzbekistan">Uzbekistan</option>
                    <option value="Vanuatu">Vanuatu</option>
                    <option value="Vatican City State">Vatican City State</option>
                    <option value="Venezuela">Venezuela</option>
                    <option value="Vietnam">Vietnam</option>
                    <option value="Virgin Islands (British)">Virgin Islands (British)</option>
                    <option value="Virgin Islands (U.S.)">Virgin Islands (U.S.)</option>
                    <option value="242">Wallis and Futuna Islands</option>
                    <option value="Wallis and Futuna Islands">Western Sahara</option>
                    <option value="Yemen">Yemen</option>
                    <option value="Zambia">Zambia</option>
                    <option value="2Zimbabwe">Zimbabwe</option>                </select>
                                        </div>
                               
                                <div class="col-md-6">
                                    <label class="mb-0">Slug</label>
                                    <input type="text" autocomplete="false" required name="slug" placeholder="Enter slug" class="form-control mb-2">
                                </div>
                                <div class="col-md-12">
                                    <label class="mb-0">Product Description</label>
                                    <textarea rows="3" autocomplete="false" required name="product_description" placeholder="Enter small description" class="form-control mb-2"></textarea>
                                </div>
                                <div class="col-md-12">
                                    <label class="mb-0">Terms And Refund Policy</label>
                                    <textarea rows="3" autocomplete="false" required name="terms_refund_policy" placeholder="Enter Terms & Refund Policy" class="form-control mb-2"></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="mb-0">Original Price</label>
                                    <input type="text" autocomplete="false" required name="original_price" placeholder="Enter Original Price" class="form-control mb-2">
                                </div>
                                <div class="col-md-6">
                                    <label class="mb-0">Selling Price</label>
                                    <input type="text" autocomplete="false" required name="selling_price" placeholder="Selling Price" class="form-control mb-2">
                                </div>
                                <div class="col-md-12">
                                    <label class="mb-0">Upload Image</label>
                                    <input type="file" autocomplete="false" required name="image" class="form-control mb-2">
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="mb-0">Quantity</label>
                                        <input type="number" autocomplete="false" required name="qty" placeholder="Enter Quantity" class="form-control mb-2">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="mb-0">Status</label> <br>
                                        <input type="checkbox" autocomplete="false" name="status">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="mb-0">Trending</label> <br>
                                        <input type="checkbox" autocomplete="false" name="trending">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <label class="mb-0">Meta Title</label>
                                    <input type="text" autocomplete="false" required name="meta_title" placeholder="Enter meta title" class="form-control mb-2">
                                </div>
                                <div class="col-md-12">
                                    <label class="mb-0">Meta Description</label>
                                    <textarea rows="3" autocomplete="false" required name="meta_description" placeholder="Enter meta description" class="form-control mb-2"></textarea>
                                </div>
                                <div class="col-md-12">
                                    <label class="mb-0">Meta Keywords</label>
                                    <textarea rows="3" autocomplete="false" required name="meta_keywords" placeholder="Enter meta keywords" class="form-control mb-2"></textarea>
                                </div>
                            
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary" name="add_product_btn">Save</button>
                                
                                    <a href="products.php" class="btn btn-primary">
                                        Back
                                    </a>
                                </div>
                                
                                    
                            </div>
                        </form>
                </div>
            </div>
        </div>
        </div>
    </div>
    </div>
    </div>     





</body>
</html>


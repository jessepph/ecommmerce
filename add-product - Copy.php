
  

<?php 
include('alertify.php');
session_start();
require_once("db.php");


function getAll($table)
{
    global $con;
    $query = "SELECT * FROM $table";
    return $query_run = mysqli_query($con, $query);
}


if(isset($_POST['add_product_btn']))
{
    $category_id = $_POST['category_id'];
    $name = $_POST['name'];
    $slug = $_POST['slug'];
    $small_description = $_POST['small_description'];
    $description = $_POST['description'];
    $original_price = $_POST['original_price'];
    $selling_price = $_POST['selling_price'];
    $qty = $_POST['qty'];
    $vendor_name = $_POST['vendor_name'];
    $meta_title = $_POST['meta_title'];
    $meta_description = $_POST['meta_description'];
    $meta_keywords = $_POST['meta_keywords'];
    $status = isset($_POST['status']) ? '1':'0' ;
    $trending = isset($_POST['trending']) ? '1':'0';

    $image = $_FILES['image']['name'];

    $path = "uploads";

    $image_ext = pathinfo($image, PATHINFO_EXTENSION);
    $filename = time().'.'.$image_ext;

    if($name != "" && $slug != "" && $description != "")
    {
        $product_query = "INSERT INTO products (category_id,name,slug,small_description,description,original_price,selling_price,
        qty,vendor_name,meta_title,meta_keywords,status,trending,image) VALUES 
        ('$category_id','$name','$slug','$small_description','$description','$original_price','$selling_price','$qty','$vendor_name','$meta_title','$meta_keywords','$status','$trending','$filename')";

        $product_query_run = mysqli_query($con, $product_query);

        if($product_query_run)
        {
            move_uploaded_file($_FILES['image']['tmp_name'], $path.'/'.$filename);
            ?>
            <script>
                $(document).ready(function () {
                    
                    alertify.set('notifier','position', 'top-right');
                    alertify.success('Product Added Successfully');
                });
            </script>
            <?php
           
        }
        else
        {
            //redirect("add-product.php", "Something went wrong");
            ?>
            <script>
                $(document).ready(function () {
                    
                    alertify.set('notifier','position', 'top-right');
                    alertify.error('Something went wrong');
                });
            </script>
            <?php
        }
    }
    else
    {
        ?>
        <script>
            $(document).ready(function () {
                
                alertify.set('notifier','position', 'top-right');
                alertify.error('Something went wrong');
            });
        </script>
        <?php
        // redirect("products.php", "All fields are mandatory");
    }


}


?>
<head>
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
        <link rel="stylesheet" href="day.css">
        
</head>

<body>
   
              <div class="navigation">
        <div class="wrapper">
            <ul>
                <li class="nav-logo"><a href="http://2fzhe7csdmsl6tqfevrabnctewlh3ynro7zlkd5ie4xlai4mahr2fqid.onion/"><img src="Bohemia%20-%20Homepage_files/logo_small.png" style="height: 100%;"></a></li>
                <div class="responsive-menu">
                    <li class="menu-toggler"><a href="#">Navigation&nbsp; <div class="sprite sprite--caret" style="float: none; display: inline-block; margin-left:5px;"></div></a></li>
                    <div class="menu-links">
                        <li class="active"><a href="http://2fzhe7csdmsl6tqfevrabnctewlh3ynro7zlkd5ie4xlai4mahr2fqid.onion/">Home</a></li>
                        
                        <li class="dropdown-link dropdown-large ">
                            <a href="http://2fzhe7csdmsl6tqfevrabnctewlh3ynro7zlkd5ie4xlai4mahr2fqid.onion/usercp.php?action=orders" class="dropbtn">
                                Orders
                                                            </a>
                            <div class="dropdown-content right-dropdown">
                                <a href="#">Processing&nbsp; <span class="badge badge-secondary right">0</span></a>
                                <a href="#">Dispatched&nbsp; <span class="badge badge-danger right" style="background-color:grey;">0</span></a>
                                <a href="#">Completed&nbsp; <span class="badge badge-danger right" style= "background-color:grey">0</span></a>
                                <a href="#">Disputed&nbsp; <span class="badge badge-secondary right">0</span></a>
                                <a href="#">Cancelled</a>
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
                                All Categories
                                                                    </a>
                            <a href="add-category.php">
                                Categories
                                                                    </a>
                        <a href="edit-category.php">
                                Edit Category
                                                                    </a>
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
                    background-color:grey;
                    ">0</span>               
                    
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
                        background-color:grey;
                        border-radius: 0.25rem;
                        ">0</span>                    </a>
                </li>
                
                <li class="right fix-gap "><a href="becoming-a-merchant.php"><b>Become A Merchant</b></a></li>

                                        
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
                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="mb-0">Select Category</label>
                                    <select name="category_id" class="form-select mb-2" >
                                        <option selected>Select Category</option>
                                        <?php 
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
                                    <label class="mb-0">Vendor Name</label>
                                    <input type="text" autocomplete="false" required name="vendor_name" placeholder="Enter Vendor Name" class="form-control mb-2">
                                </div>
                               
                                <div class="col-md-6">
                                    <label class="mb-0">Slug</label>
                                    <input type="text" autocomplete="false" required name="slug" placeholder="Enter slug" class="form-control mb-2">
                                </div>
                                <div class="col-md-12">
                                    <label class="mb-0">Small Description</label>
                                    <textarea rows="3" autocomplete="false" required name="small_description" placeholder="Enter small description" class="form-control mb-2"></textarea>
                                </div>
                                <div class="col-md-12">
                                    <label class="mb-0">Description</label>
                                    <textarea rows="3" autocomplete="false" required name="description" placeholder="Enter description" class="form-control mb-2"></textarea>
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


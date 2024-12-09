<?php
session_start();
//include('auth.php');
$account_role = $_SESSION["account_role"];

if($_SESSION["account_role"] == "Buyer"){
  header("Location: vendor-non-approved.php");
}

           
    
    ?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <title>Vendor</title>
  <link rel="stylesheet" href="style.css" />
  <link rel="stylesheet" href="password-strength-indicator.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <link rel="icon" type="image/jpeg" href="img/pentagram.jpg">
</head>

<body style="width: 100%;">
  <div class="container" style="    
    top: 2%;
    width: 98%;
    height: 100%;
    border-radius: 5px;
    box-shadow: 0 0 15px rgb(0 0 0 / 20%);">


    <h1 style="text-align:center;">REUP MARKET</h1>
   
     <!--<h5>Dashboard</h5>-->
     <nav class="navbar navbar-default">
      <div class="container-fluid">
        <div class="navbar-header">
          <a class="navbar-brand" href="homepage.php">REUP MARKET</a>
        </div>
        <ul class="nav navbar-nav">
        <li><a href="homepage.php">Home</a></li>
          <li class="active"><a href="vendor.php">Vendor</a></li>
          <li><a href="add-product.php">Add Product</a></li>
          <li><a href="products.php">Products</a></li>
          <li><a href="add-category.php">All Categories</a></li>
          <li><a href="category.php">Categories</a></li>
          <li><a href="edit-category.php">Edit Category</a></li>
          <li><a href="pm_check.php">Messages</a></li>
          <li><a href="chat-index-page.php">Chat</a></li>
          <li> <a href="registration2.php">Register</a></li>
          <li style="
              color: #010204;
              top: -5px;
              position: absolute;
              right: 300px;
          ">
          <a href="cart.php"><i class="fa-sharp fa-regular fa-cart-shopping">
                <img src="../phpecom/images/shopping-cart.png"><span id="cartItemsQuantityCount">0</span></i></a></li>
          <li><a href="logout.php">Log Out</a></li>
        </ul>
        <div style="float:right;">
          <?php include("bitcoin-ticker.php"); ?>

        </div>
      </div>
    </nav>

  </div>
  <header></header>

  <div id="row-1" style="width: 100%; /* display: flex; */ flex-direction: row; justify-content: center; margin-top: 60px;">

    <div class="user-profile" style=" 
    border: none;
    height: auto;
    width: 300px;
    /*display: flex;*/
    /*flex-direction: column;*/
    justify-content: center;
    background: #fff;
    border-radius: 5px;
    box-shadow: 0 0 15px rgb(0 0 0 / 20%);
    padding-bottom: 30px;
    margin-right: 40px;
    position: absolute;
    margin-left: 250px;

">
      <!--<h2>DASHBOARD</h2>-->
      <div class="form" style="border: none;">
        <p>Profile</p>
        <img src="default-profile-pic.jfif"><br>

        <div id="profile-info" style="display: flex; flex-direction: column; align-items: space-evenly; justify-content: space-evenly; height: 200px;">


          <?php echo "Username: " . $_SESSION["username"] . "<br>"; ?>
          <?php echo "Date Joined: " . $_SESSION["dateJoined"] . "<br>"; ?>
          <?php echo "Account Role: " . $_SESSION["account_role"] . "<br>"; ?>
  
          <div style="display: flex; align-items: center; justify-content: center">
            <?php echo "Trust Level: " ?>
            <div style="
            width: 100%;
  max-width: 75px; 
  height: 30px; 
  background: <?php
              $trust_level = $_SESSION["trust_level"];
              switch ($trust_level) {
                case 0 :
                  echo "green";
                  break;
                case 1:
                  echo "blue";
                  break;
                case 2:
                  echo "orange";
                  break;
                case 3:
                  echo "red";
                  break;
                default:
                  echo "green";
                  break;
              }
              ?>;
  border-radius: 50px; 
  display: flex; 
  justify-content: center; 
  align-items: center; 
  color: white;
  margin-left: 5px;
  ">
              <?php echo "Level " . $_SESSION["trust_level"] . "<br>"; ?>
            </div>
          </div>
  
  
  
          <div style="display: flex; align-items: center; justify-content: center">
            <?php
            $total_orders = $_SESSION["total_orders"];
            echo "Total Orders: "; ?>
            <div style="
  color: <?php echo $total_orders > 0 ? "green" : "red"; ?>;
  margin-left: 5px;
  ">
              <?php
              $locale = "USD";
  
              echo $locale . " " . number_format($total_orders, 2, ".", ",") . "<br>"; ?>
            </div>
          </div>
          <!--<p>This is another secured page.</p>-->
          <a href="secure_page.php">Secured Page</a>
          <a href="logout.php">Logout</a>

        </div>

      </div>

    </div>
    <!--
    <div id="news" style="
    border: none;
    height: auto;
    width: 400px;
    justify-content: center;
    background: #fff;
    border-radius: 5px;
    box-shadow: 0 0 15px rgb(0 0 0 / 20%);
    padding-top: 30px;
    padding-bottom: 30px;
    margin-right: 40px;
    ">
      <h3>News</h3><br><br><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum porttitor maximus nibh, congue suscipit odio. Duis et lectus euismod, ullamcorper nisl pharetra, blandit quam. 
        Vivamus vel eros ac diam mattis semper. Nunc nibh magna, aliquam nec nibh sit amet, cursus sagittis magna. Maecenas nec luctus felis. Sed et finibus nibh. Curabitur dignissim 
        turpis quam, id elementum purus luctus pretium. Sed velit est, semper vel urna id, auctor pretium turpis. Maecenas ornare, libero id ultricies ultrices, neque arcu consequat 
        tortor, aliquam accumsan sapien mauris a tortor. Phasellus non diam neque. Quisque accumsan pellentesque odio et maximus. Proin eu rutrum urna.</p> 
      
      
      
        
    
      
    </div>-->
     <!--
    <div id="search" style="
    border: none;
    height: auto;
    width: 600px;
    display: flex;
    justify-content: center;
    background: #fff;
    border-radius: 5px;
    box-shadow: 0 0 15px rgb(0 0 0 / 20%);
    padding-top: 30px;
    padding-bottom: 30px;
    ">
      <h5>Search</h5>
    </div>


  </div>-->

  <div id="row-2" style="width: 100%; display: flex; flex-direction: row; justify-content: center; margin-top: 40px;">

    <div class="column-1" style="display: flex; flex-direction: column;">

      <div id="catagories" style="
      border: none;
      height: auto;
      width: 300px;
      display: flex;
      justify-content: center;
      background: #fff;
      border-radius: 5px;
      box-shadow: 0 0 15px rgb(0 0 0 / 20%);
      padding-top: 30px;
      padding-bottom: 30px;
      margin-right: 40px;
      margin-bottom: 40px;
      margin-top:400px;
      ">
        <h3 style="text-align:center;">Catagories
          <br>
          <br>
          <div class="card-body" style="font-size: 18px; padding-left:15px;">
                  <ul class="nav nav-pills flex-column ">
                 
<br>
<li><b> ➤<a href="listing_category?id=1" class="nav-link"> Category 1 <span class="badge badge-secondary float-right">7502</span></a></b></li>
<li></li>
<li><b>➤<a href="listing_category?id=10" class="nav-link">Category  2 <span class="badge badge-secondary float-right">1260</span></a></b></li>
<li></li>
<li><b>➤<a href="listing_category?id=22" class="nav-link"> Category 3 <span class="badge badge-secondary float-right">153</span></a></b></li>
<li></li>
  <li><b>➤<a href="listing_category?id=38" class="nav-link">Category 4<span class="badge badge-secondary float-right">45995</span></a></b></li>
  <li></li>
  <li><b>➤<a href="listing_category?id=111" class="nav-link"> Category 5<span class="badge badge-secondary float-right">454</span></a></b></li>
  <li></li>
  <li><b>➤<a href="listing_category?id=124" class="nav-link"> Category 6 <span class="badge badge-secondary float-right">160</span></a></b></li>
  <li></li>
  <li><b>➤<a href="listing_category?id=135" class="nav-link"> Category 7 <span class="badge badge-secondary float-right">3560</span></a></b></li>
  <li></li>
  <li><b>➤<a href="listing_category?id=144" class="nav-link"> Category 8 <span class="badge badge-secondary float-right">711</span></a></b></li>
  <li></li>
  <li><b>➤<a href="listing_category?id=151" class="nav-link"> Category 9 <span class="badge badge-secondary float-right">1817</span></a></b></li>
  <li></li>
  <li style="margin-left:-1.5px;"><b>➤<a href="listing_category?id=158" class="nav-link"> Category 10 <span class="badge badge-secondary float-right">19</span></a></b></li>
  <li></li>
  <li><b>➤<a href="listing_category?id=165" class="nav-link"> Category 11 <span class="badge badge-secondary float-right">25</span></a></b></li><li>
</li>
  <li><b>➤<a href="listing_category?id=171" class="nav-link">Category 12 <span class="badge badge-secondary float-right">1009</span></a></b></li>
  <li></li>
  <li><b>➤<a href="listing_category?id=182" class="nav-link"> Category 13 <span class="badge badge-secondary float-right">52</span></a></b></li>
  <li></li>
  <li><b>➤<a href="listing_category?id=198" class="nav-link">Category 14 <span class="badge badge-secondary float-right">16</span></a></b></li>
  <li></li>
  <li><b>➤<a href="listing_category?id=217" class="nav-link"> Category 15 <span class="badge badge-secondary float-right">62</span></a></b></li>
  <li></li>
  <li><b>➤<a href="listing_category?id=233" class="nav-link"> Category <span class="badge badge-secondary float-right">221</span></a></b></li><li>                

  </li></ul></div>
      </div>
        </h3>
        

      <div id="statistics" style="
      border: none;
      height: auto;
      width: 300px;
      display: flex;
      justify-content: center;
      background: #fff;
      border-radius: 5px;
      box-shadow: 0 0 15px rgb(0 0 0 / 20%);
      padding-top: 30px;
      padding-bottom: 30px;
      margin-right: 40px;
      margin-bottom: 40px;
      position: absolute;
      margin-top: 927px;
      ">
        <h3>Statistics</h3>
      </div>

      <div id="exchange-rates" style="
      border: none;
      height: auto;
      width: 300px;
      justify-content: center;
      background: #fff;
      border-radius: 5px;
      box-shadow: 0 0 15px rgb(0 0 0 / 20%);
      padding-top: 30px;
      padding-bottom: 30px;
      margin-right: 40px;
      position: absolute;
      margin-top: 900px;
      position: absolute;
      margin-top: 1200px;
      text-align:center;
      ">
        <h3>Exchange rates</br></br</br></h3>
       <?php include("bitcoin-ticker.php"); ?>
      </div>
      
   </div>

    <div class="column-2" style="display: flex; flex-direction: column;">
      <div id="welcome-message" style="
  border: none;
  height: auto;
  width: 1045px;
  background: #fff;
  border-radius: 5px;
  box-shadow: 0 0 15px rgb(0 0 0 / 20%);
  padding-top: 30px;
  padding-bottom: 30px;
  padding-left: 50px;
  padding-right: 50px;
  margin-bottom: 40px;
  ">
        <h2>Welcome <?php echo $_SESSION["username"]; ?></h2>
        <br>
        <p>You have successfully been approved for being a vendor  click "Add products" to update store <a href="add-product.php"> Add products </a> 
      </div>

      <!--<div class="feature-listings" style="
  border: none;
  height: auto;
  width: 1045px;
  display: flex;
  background: #fff;
  border-radius: 5px;
  box-shadow: 0 0 15px rgb(0 0 0 / 20%);
  padding-top: 30px;
  padding-bottom: 30px;
  padding-left: 50px;
  padding-right: 50px;
  margin-bottom: 50px;
  ">
        <h2>Feature listings</h2>
      </div>

      <div id="top-listings" style="
    border: none;
    height: auto;
    width: 1045px;
    display: flex;
    background: #fff;
    border-radius: 5px;
    box-shadow: 0 0 15px rgb(0 0 0 / 20%);
    padding-top: 30px;
    padding-bottom: 30px;
    padding-left: 50px;
    padding-right: 50px;
    ">
        <h2>Top listings</h2>
      </div>

    </div>

  </div>-->
  <div class="flex-wrapper"></div>
  <div id="footer" style="height: 200px; width: 100%; position: relative; bottom: 0; background-color: #e0e0e0; margin-top: 40px; text-align: left; padding: 50px;">
    <a href="#">Home</a></div>
  </div>
</body>

</html>
            
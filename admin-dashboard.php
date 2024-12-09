<?php
session_start();
//include('auth.php');
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="style.css" />
  <link rel="stylesheet" href="password-strength-indicator.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>

<body style="width: 100%;">
  <div class="container" style="    
    top: 2%;
    width: 98%;
    height: 100%;
    border-radius: 5px;
    box-shadow: 0 0 15px rgb(0 0 0 / 20%);">


    <h1>REUP MARKET</h1>
   
    <!--<h5>Dashboard</h5>-->
    <nav class="navbar navbar-default">
      <div class="container-fluid">
        <div class="navbar-header">
          <a class="navbar-brand" href="homepage.php">REUP MARKET</a>
        </div>
        <ul class="nav navbar-nav">
          <li><a href="homepage.php">Home</a></li>
          <li><a href="vendor.php">Vendor</a></li>
          <li class="active"><a href="admin-dashboard.php">Admin</a></li>
          <li><a href="pm_check.php">Messages</a></li>
          <li><a href="chat-index-page.php">Chat</a></li>
          <li><a href="logout.php">Log Out</a></li>
        </ul>
        <div style="">
          <?php include("bitcoin-ticker.php"); ?>

        </div>
      </div>
    </nav>
    <header></header>

<div id="row-1" style="width: 100%; display: flex; flex-direction: row; justify-content: center; margin-top: 60px;">

  <div class="user-profile" style=" 
  border: none;
  height: auto;
  width: 300px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  background: #fff;
  border-radius: 5px;
  box-shadow: 0 0 15px rgb(0 0 0 / 20%);
  padding-bottom: 30px;
  margin-right: 1300px;
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
    <div class="column-2" style="display: flex; flex-direction: column;">
      <div id="welcome-message" style="
  border: none;
    height: auto;
    width: 800px;
    background: #fff;
    border-radius: 5px;
    box-shadow: 0 0 15px rgb(0 0 0 / 20%);
    padding-top: 30px;
    padding-bottom: 30px;
    padding-left: 50px;
    padding-right: 50px;
    margin-bottom: 45px;
    position: absolute;
    top: 200px;
    right: 550px;
  ">
        <h2>Welcome logitech</h2>
        <br>
        <?
          
        
        ?>
        <a href="add_product.php">Add Product</a>
        <p>Here is where the add product button will be.</p>
      </div>

      <div class="feature-listings" style="
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
    margin-top: 60px;
    position: absolute;
    margin-left: 400px;
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
    position: absolute;
    right: 200;
    padding-right: 50px;
    right: 200px;
    /* top: -22px; */
    margin-top: 400px;
    left: 600px;
">
        <h2>Top listings</h2>
      </div>

    </div>
  </div>
  </div>

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
      margin-left:-795px;
      ">
        <h3>Catagories
          <br>
          <br>
          <div class="card-body" style="font-size: 18px;">
                  <ul class="nav nav-pills flex-column ">
                 
<br>
<li><b> ➤<a href="listing_category?id=1" class="nav-link"> Fraud <span class="badge badge-secondary float-right">7502</span></a></b></li>
<li></li>
<li><b>➤<a href="listing_category?id=10" class="nav-link">Hacking &amp; Spam <span class="badge badge-secondary float-right">1260</span></a></b></li>
<li></li>
<li><b>➤<a href="listing_category?id=22" class="nav-link"> Malware <span class="badge badge-secondary float-right">153</span></a></b></li>
<li></li>
  <li><b>➤<a href="listing_category?id=38" class="nav-link">Drugs &amp; Chemicals <span class="badge badge-secondary float-right">45995</span></a></b></li>
  <li></li>
  <li><b>➤<a href="listing_category?id=111" class="nav-link"> Services <span class="badge badge-secondary float-right">454</span></a></b></li>
  <li></li>
  <li><b>➤<a href="listing_category?id=124" class="nav-link"> Security &amp; Hosting <span class="badge badge-secondary float-right">160</span></a></b></li>
  <li></li>
  <li><b>➤<a href="listing_category?id=135" class="nav-link"> Guides &amp; Tutorials <span class="badge badge-secondary float-right">3560</span></a></b></li>
  <li></li>
  <li><b>➤<a href="listing_category?id=144" class="nav-link"> Software <span class="badge badge-secondary float-right">711</span></a></b></li>
  <li></li>
  <li><b>➤<a href="listing_category?id=151" class="nav-link"> Digital Items <span class="badge badge-secondary float-right">1817</span></a></b></li>
  <li></li>
  <li style="margin-left:-1.5px;"><b>➤<a href="listing_category?id=158" class="nav-link"> Websites &amp; Graphic Design <span class="badge badge-secondary float-right">19</span></a></b></li>
  <li></li>
  <li><b>➤<a href="listing_category?id=165" class="nav-link"> Jewels &amp; Precious Metals <span class="badge badge-secondary float-right">25</span></a></b></li><li>
</li>
  <li><b>➤<a href="listing_category?id=171" class="nav-link">Counterfeit Items <span class="badge badge-secondary float-right">1009</span></a></b></li>
  <li></li>
  <li><b>➤<a href="listing_category?id=182" class="nav-link"> Carded Items <span class="badge badge-secondary float-right">52</span></a></b></li>
  <li></li>
  <li><b>➤<a href="listing_category?id=198" class="nav-link">Automotive-related Items <span class="badge badge-secondary float-right">16</span></a></b></li>
  <li></li>
  <li><b>➤<a href="listing_category?id=217" class="nav-link"> Legitimate Items <span class="badge badge-secondary float-right">62</span></a></b></li>
  <li></li>
  <li><b>➤<a href="listing_category?id=233" class="nav-link"> Other Listings <span class="badge badge-secondary float-right">221</span></a></b></li><li>                

  </li></ul></div>
</body>
</html>
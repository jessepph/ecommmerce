<?  
require_once("code.php");

?>
<script>alert('Record Added Successfully');</script>

<?header("Location: add-category.php"); ?>



<html>
<head>
  <meta charset="utf-8">
  <title>Add Category</title>
  <link rel="stylesheet" href="style.css" />
  <link rel="stylesheet" href="password-strength-indicator.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  
</head>
<? ?>
<body style="width: 100%;">
  <div class="container" style="    
    top: 2%;
    width: 98%;
    height: 100%;
    border-radius: 5px;
    box-shadow: 0 0 15px rgb(0 0 0 / 20%);">


    <h1 style="margin-left:700px;">REUP MARKET</h1>
   
    <!--<h5>Dashboard</h5>-->
    <nav class="navbar navbar-default">
      <div class="container-fluid">
        <div class="navbar-header">
          <a class="navbar-brand" href="homepage.php">REUP MARKET</a>
        </div>
        <ul class="nav navbar-nav">
        <li><a href="homepage.php">Home</a></li>
        <li><a href="vendor.php">Vendor</a></li>
          <li class="active"><a href="add-category.php">Add Category</a></li>
          <li><a href="category.php">All Category</a></li>
          <li><a href="edit-category.php">Edit Category</a></li>
          <li><a href="pm_check.php">Messages</a></li>
          <li><a href="chat-index-page.php">Chat</a></li>
          <li> <a href="register.php">Register</a></li>
          <li><a href="logout.php">Log Out</a></li>
        </ul>
        <div style=" margin-top: 15px; margin-left: 1500px;">
          <?php include("bitcoin-ticker.php"); ?>

        </div>
      </div>
    </nav>
   
  <header></header>
  <div class="container" style="text-align: center; height: 60%; border: 1px solid black;">
    <h1 style="text-align:center;">Add A Category</h1>
    
    <div class="container" style="">
        <div class="row">
            <div class="col-md-12" style="height:200px;">
                <div class="card-header">
                    <div class="card-body">
                        <form action="add-category2.php" method="POST" enctype="multipart/form-data">
                        <div class="row">
                         
                         
                            <div class="col-md-6">
                                
                        
                    <label>Name: <input required placeholder="Enter category name " type="text" name="name" class="form-control"></label>
                   
                   </div> 
                   <div class="col-md-2">
                   <label for="">Slug: <input required name="slug" placeholder="Enter Slug Name" name="slug" type="text" class="form-control"></label>
                   </div> 
                   <div class="col-md-6" style="">
                        
                        <label for="">Description: <input required placeholder="Enter Description " type="text" name="description" class="form-control"></label>
                       
                       </div> 
                       <div class="col-md-2">
                   <label for=""> Upload Image: <input required style="width:162%;" placeholder="Upload Image" name="image" type="file" accept="image/*" class="form-control"></label>
                   </div>
                   
                   <div class="col-md-6" style="position: absolute; top: 145px;">
                        
                        <label for="">Meta Title: <input required placeholder="Enter Meta Title " type="text" name="meta_title" class="form-control"></label>
                       
                       </div> 
                       <div class="col-md-2" style="position: absolute; margin-top: 230px; left: 185px;">
                   <label for=""> Meta Description: <input required name="meta_description" style="width:162%;" placeholder="Enter Meta Description" type="text" class="form-control"></label>
                   </div>
                   
                   <div class="col-md-6" style="position: absolute; margin-top: 200; margin-left: 430px;">
                        
                        <label for="">Meta Keywords: <input required placeholder="Enter Meta Keywords " type="text" name="meta_keywords" class="form-control"></label>
                       
                       </div> 

                       <div class="col-md-2" style="position:absolute;">
                   
                     <label for="">Popular: <input  type="checkbox" name="popular" class="form-control"></label>
                     <label for="">Status <input  type="checkbox" name="status" class="form-control"></label>
                       
                       </div> 
                 </div>
                   
            </div>
        </div>
    </div>
   
    <!--<button name="submit" class="btn btn-primary" name="add_category_btn" style="position: absolute; margin-top: 400px; margin-left: -550px;">Save</button>-->
    <input name="add_category_btn" style="position:absolute; margin-left: -700px; margin-top: 400px;" type="submit" value="Add Category" />
</form> 
    </div>
   </div>

</body>
<footer>

</footer>

</html>































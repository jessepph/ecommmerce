<?php
session_start();
require_once("db.php");
$query = "SELECT * FROM products";
$result = mysqli_query($con,$query);
$row = mysqli_fetch_array($result);
$catQuery = "SELECT * FROM categories";
$catResultCatName = mysqli_query($con,$catQuery);
$rowResultCatName = mysqli_fetch_array($catResultCatName);

?>
<html>
    <head>

    <meta http-equiv="Content-type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" type="text/css" href="Bohemia%20-%20Homepage_files/flexboxgrid.min.css">
        <link rel="stylesheet" type="text/css" href="Bohemia%20-%20Homepage_files/fontawesome-all.min.css">
        <link rel="stylesheet" type="text/css" href="Bohemia%20-%20Homepage_files/style.css">
        <link rel="stylesheet" type="text/css" href="Bohemia%20-%20Homepage_files/main.css">
        <link rel="stylesheet" type="text/css" href="Bohemia%20-%20Homepage_files/responsive.css">        
        <link rel="stylesheet" type="text/css" href="Bohemia%20-%20Homepage_files/sprite.css">
        <link rel="icon" type="image/jpeg" href="img/pentagram.jpg">
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div classs="card-header">
                            <h2 class="display-6" style="text-align:center;">Product Details</h2>
                        </div>
                        <div class="card-body">
                        <table class="table table-bordered" style="display:inline-block;">
                         
                        <th>
                        <tr>
                            <?php
                               while($row = mysqli_fetch_assoc($result)){
                                $query3 = "SELECT  * FROM categories WHERE name='shoes'";
                                $result3 = mysqli_query($con,$query3);
                                $row3 = mysqli_fetch_array($result3);
                               
                            ?>
                            <!-- Create program that takes CAT ID and -> To Cat Name -->
                            <!-- Create program that makes each product a class or object and store in array then print each class -->
                            <th><tr><td style="">ID: <?php echo $row['id'];?></td></tr>                        
                            <tr><td style="">Product Title: <?php echo $row['name'];?></td></tr>
                            <tr><td style="">Description: <?php echo $row['description'];?></td></tr>
                            <tr><td style="">Original Price: <?php echo $row['original_price'];?></td></tr>
                            <tr><td style="">Selling Price: <?php echo $row['selling_price'];?></td></tr>
                            <tr><td style="">Quantity: <?php echo $row['qty'];?></td></tr>
                            <th>Categories</th>
                            
                            <tr><td style="">Category Name: <?php echo $row3['name'];?><br></tr>
                            
                        <?php }?>
                                  
                               </th>
                        </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      
    </body>
</html>
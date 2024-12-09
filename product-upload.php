<?php
                $db = mysqli_connect('localhost', 'root', '', 'market');
                $sid = $_POST['subcategories'];
                $query = "SELECT id FROM subcategory WHERE sid='$sid'";
                $result = mysqli_query($db,$query);
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                $id = $row['id'];
                $query = "SELECT name FROM category WHERE id='$id'";
                $result = mysqli_query($db,$query);
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                $category_name = $row['name'];
                $pid = rand(9999,1000);
                $brand = $_POST['brand'];
                $productname = $_POST['pname'];
                $qty = $_POST['qty'];
                $price = $_POST['price'];
                date_default_timezone_set("Asia/Kolkata");
                $t=time(); 
                $created_date = date("Y-m-d:h-i-s",$t);
                $updated_date = date("Y-m-d:h-i-s",$t);
                $total = count($_FILES["fileToUpload"]["name"]);
                $target_dir = "products-images/";
                for($i = 0 ; $i < $total; $i++){
                    $target_file[$i] = $target_dir . basename($_FILES["fileToUpload"]["name"][$i]);
                    $uploadOk = 1;
                    $imageFileType = strtolower(pathinfo($target_file[$i],PATHINFO_EXTENSION));
                    
                    // Check if image file is a actual image or fake image
                    
                      $check = getimagesize($_FILES["fileToUpload"]["tmp_name"][$i]);
                      if($check !== false) {
                       
                        $uploadOk = 1;
                      } else {
                        
                        $uploadOk = 2;
                      }
                   
                    
                    // Check if file already exists
                    if (file_exists($target_file[$i])) {
                      
                      $uploadOk = 3;
                    }
                    
                    // Check file size
                    if ($_FILES["fileToUpload"]["size"][$i] > 500000) {
                      
                        $uploadOk = 4;
                    }
                    
                    // Allow certain file formats
                    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" && $imageFileType != "webp" ) {
                      
                      $uploadOk = 5;
                    }
                    
                    // Check if $uploadOk is set to 0 by an error
                    if ($uploadOk == 5) {
                      echo 5; exit();
                    } 
                    else 
                        if($uploadOk == 4){
                            echo 4; exit();
                        }
                        else
                            if($uploadOk == 3){
                                echo 3; exit();
                            }
                            else
                                if($uploadOk == 2){
                                    echo 2; exit();
                                }
                            else{
                                move_uploaded_file($_FILES["fileToUpload"]["tmp_name"][$i], $target_file[$i]);
                               
                                
                            }
                }
                $query= "INSERT INTO products (id,category_id,category_name,product_name,subcategory_id,price,brand,qty,image,created_date,updated_date) VALUES('$pid','$id','$category_name','$productname','$sid','$price','$brand','$qty','$target_file[0]','$created_date','$updated_date')";
                $result = mysqli_query($db,$query);
                if(!$result){
                    die(mysqli_error());
                    echo 0;
                    exit();
                }
                else{
                    for($i = 0 ; $i < $total ; $i++)
                    {
                         $query = "INSERT INTO product_images (pid,image) VALUES('$pid','$target_file[$i]')";
                         $result = mysqli_query($db,$query);
                    }
                    echo 1;
                }
                
                
?>
                

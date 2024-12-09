<?php

session_start();
// include('db.php');
// include('alertify.php');
require 'db.php';
$con = mysqli_connect("localhost", "root", "", "market");
$username = mysqli_query($con,"SELECT * FROM register WHERE username='username'");
$id = mysqli_query($con,"SELECT * FROM register WHERE id='id'");
// echo  $_SESSION["username"];


    if(isset($_POST['scope']))
    {
        $scope = $_POST['scope'];
        switch ($scope)
        {
            case "add":
                $prod_id = $_POST['prod_id'];
                $prod_qty = $_POST['prod_qty'];
                $user_id = $_SESSION['username'];
                $chk_existing_cart = "SELECT * FROM carts WHERE prod_id='$prod_id' AND user_id='$user_id' ";
                $chk_existing_cart_run = mysqli_query($con, $chk_existing_cart);
                if(mysqli_num_rows($chk_existing_cart_run) > 0)
                {
                    echo "existing";
                }
                else
                {
                    $insert_query = "INSERT INTO carts (user_id, prod_id, prod_qty) VALUES ('$user_id','$prod_id','$prod_qty')";
                    $insert_query_run = mysqli_query($con, $insert_query);

                    if($insert_query_run)
                    {
                        //Add product successfully alertify code here
                        echo 201;
                    }
                    else
                    {
                        echo 500;
                    }
                }

             
                break;

            case "update":
                $prod_id = $_POST['prod_id'];
                $prod_qty = $_POST['prod_qty'];

                $user_id = $_SESSION['auth_user']['user_id'];

                $chk_existing_cart = "SELECT * FROM carts WHERE prod_id='$prod_id' AND user_id='$user_id' ";
                $chk_existing_cart_run = mysqli_query($con, $chk_existing_cart);

                if(mysqli_num_rows($chk_existing_cart_run) > 0)
                {
                    $update_query = "UPDATE carts SET prod_qty='$prod_qty' WHERE prod_id='$prod_id' AND user_id='$user_id' ";
                    $update_query_run = mysqli_query($con, $update_query);
                    if($update_query_run){
                        echo 200;
                    }else{
                        echo 500;
                    }
                }
                else
                {
                    echo "something went wrong";
                }
                
                break;

            case "delete":
                $cart_id = $_POST['cart_id'];

                $user_id = $_SESSION['auth_user']['user_id'];

                $chk_existing_cart = "SELECT * FROM carts WHERE id='$cart_id' AND user_id='$user_id' ";
                $chk_existing_cart_run = mysqli_query($con, $chk_existing_cart);

                if(mysqli_num_rows($chk_existing_cart_run) > 0)
                {
                    $delete_query = "DELETE FROM carts WHERE id='$cart_id' ";
                    $delete_query_run = mysqli_query($con, $delete_query);
                    if($delete_query_run){
                        echo 200;
                    }else{
                        echo "something went wrong";
                    }
                }
                else
                {
                    echo "something went wrong";
                }

                break;
                
            case "get_toal_quantity":

                $user_id = $_SESSION['username'];

                $fetch_cart_query = "SELECT SUM(prod_qty) as total_count FROM carts WHERE user_id='$user_id' ";
                $fetch_cart_query_run = mysqli_query($con, $fetch_cart_query);

                if(mysqli_num_rows($fetch_cart_query_run) > 0)
                {
                    $data = mysqli_fetch_assoc($fetch_cart_query_run);
                    echo $data['total_count'];
                }
                else
                {
                    echo 000;
                }

            default:
                // echo 500;
        }
    }


else
{
    echo 401;
}


?>
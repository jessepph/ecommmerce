<?php 

session_start();
include('db.php');
// Database connection parameters
$servername = "localhost";
$dbusername = "root";
$password = "";
$dbname = "market";
//$port = 888;

// Create a new database connection
$conn = new mysqli($servername, $dbusername, $password, $dbname);


function getAllActive($table)
{
    global $conn;
    $query = "SELECT * FROM $table WHERE status='0' ";
    return $query_run = mysqli_query($conn, $query);
}


function getAllTrending()
{
    global $conn;
    $query = "SELECT * FROM products WHERE trending='1' AND status='0' ";
    return $query_run = mysqli_query($conn, $query);
}

function getVendorName()
{
    global $conn;
    $query = "SELECT * FROM products WHERE vendor_name=''";
    return $query_run = mysqli_query($conn, $query);
}

function getSlugActive($table, $slug)
{
    global $conn;
    $query = "SELECT * FROM $table WHERE slug='$slug' AND status='0' LIMIT 1";
    return $query_run = mysqli_query($conn, $query);
}

function getProdByCategory($conn, $category_id)
{
    $category_id = (int)$category_id; // Ensure category_id is treated as an integer
    $query = "SELECT * FROM products WHERE category_id='$category_id' AND status='0' ";
    return mysqli_query($conn, $query);
}

function getIDActive($table, $id)
{
    global $conn;
    $query = "SELECT * FROM $table WHERE id='$id' AND status='0' ";
    return $query_run = mysqli_query($conn, $query);
}

function getCartItems()
{
    global $conn;
    $userId = $_SESSION['username'];
    $query = "SELECT c.id as cid, c.prod_id, c.prod_qty, p.id as pid, p.name, p.image, p.selling_price 
                FROM carts c, products p WHERE c.prod_id=p.id AND c.user_id='$userId' ORDER BY c.id DESC ";
    return $query_run = mysqli_query($conn, $query);

}

/*function checkTrackingNoValid($trackingNo)
{
    global $con;
    $userId = $_SESSION['auth_user']['user_id'];

    $query = "SELECT * FROM orders WHERE tracking_no='$trackingNo' AND user_id='$userId' ";
    return mysqli_query($con, $query);
}

function getOrders()
{
    global $con;
    $userId = $_SESSION['auth_user']['user_id'];

    $query = "SELECT * FROM orders WHERE user_id='$userId' ORDER BY id DESC";
    return $query_run = mysqli_query($con, $query);
}*/

/*function redirect($url, $message)
{
    $_SESSION['message'] = $message;
    header('Location: '.$url);
    exit(0);
}*/



?>
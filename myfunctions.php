<?php

// Function to fetch a record by ID, using prepared statements for security
function getByID_custom($table, $id)
{
    global $conn; // Assuming $conn is your database connection
    $query = "SELECT * FROM $table WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);  // 'i' for integer type (assuming 'id' is an integer)
    $stmt->execute();
    return $stmt->get_result(); // Fetch the result
}

// Function to fetch all records from a table
if (!function_exists('getAll')) {
    function getAll($table)
    {
        global $conn;

        // Ensure the table name is safe (use a whitelist of allowed table names or use mysqli_real_escape_string)
        $table = mysqli_real_escape_string($conn, $table);

        $query = "SELECT * FROM `$table`";
        $query_run = mysqli_query($conn, $query);

        // Check for errors in the query execution
        if (!$query_run) {
            die("Database query failed: " . mysqli_error($conn));
        }

        return $query_run;
    }
}

// Function to fetch a record by ID (old version with direct SQL query)
function getByID($table, $id)
{
    global $conn; // Assuming $conn is your database connection
    $query = "SELECT * FROM $table WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);  // 'i' for integer type (assuming 'id' is an integer)
    $stmt->execute();
    return $stmt->get_result(); // Fetch the result
}

// Function for redirecting with a message
function redirect($url, $message)
{
    $_SESSION['message'] = $message;
    header('Location: ' . $url);
    exit(0);
}

// Fetch all pending orders
function getAllOrders()
{
    global $conn;
    $query = "SELECT * FROM orders WHERE status = '0'";
    return mysqli_query($conn, $query);
}

// Fetch all orders with a non-zero status (order history)
function getOrderHistory()
{
    global $conn;
    $query = "SELECT * FROM orders WHERE status != '0'";
    return mysqli_query($conn, $query);
}

// Check if the tracking number is valid
function checkTrackingNoValid($trackingNo)
{
    global $conn;
    $query = "SELECT * FROM orders WHERE tracking_no = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $trackingNo);  // 's' for string type
    $stmt->execute();
    return $stmt->get_result();
}

// Get total money for today
function todayMoney()
{
    global $conn;
    $todayDate = date('Y-m-d');
    $query = "SELECT SUM(total_price) FROM orders WHERE created_at LIKE ?";
    $stmt = $conn->prepare($query);
    $todayDate = $todayDate . '%'; // Add the wildcard for the LIKE clause
    $stmt->bind_param("s", $todayDate);
    $stmt->execute();
    return $stmt->get_result();
}

// Get total users who registered today
function todayUsers()
{
    global $conn;
    $todayDate = date('Y-m-d');
    $query = "SELECT id FROM users WHERE created_at LIKE ?";
    $stmt = $conn->prepare($query);
    $todayDate = $todayDate . '%'; // Add the wildcard for the LIKE clause
    $stmt->bind_param("s", $todayDate);
    $stmt->execute();
    return $stmt->get_result();
}

// Get the total number of users
function totalUsers()
{
    global $conn;
    $query = "SELECT id FROM users";
    return mysqli_query($conn, $query);
}

// Get total sales amount
function totalSales()
{
    global $conn;
    $query = "SELECT SUM(total_price) FROM orders";
    return mysqli_query($conn, $query);
}

?>

<?php
session_start(); // Start the session

include 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // Return an error or redirect to login page
    echo 'User not logged in';
    exit();
}

// Retrieve the logged-in username
$fromUser = $_SESSION['username'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $recipient = mysqli_real_escape_string($con, $_POST['username']);
    $subject = mysqli_real_escape_string($con, $_POST['subject']);
    $message = mysqli_real_escape_string($con, $_POST['message']);

    // Validate recipient name
    $sql = "SELECT * FROM register WHERE username = '$recipient'";
    $result = mysqli_query($con, $sql);

    if (mysqli_num_rows($result) > 0) {
        // Insert the message into the messages table
        $insert_sql = "INSERT INTO messages (FromUser, ToUser, message, receive_date) 
                       VALUES ('$fromUser', '$recipient', '$body', NOW())";
        $insert_result = mysqli_query($con, $insert_sql);

        if ($insert_result) {
            echo 'Message sent successfully';
        } else {
            echo 'Error inserting message: ' . mysqli_error($con);
        }
    } else {
        echo 'Recipient does not exist';
    }
}
?>

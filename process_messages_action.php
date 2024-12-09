<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "CoheedAndCambria666!";
$dbname = "market";
$port = 888;

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname,$port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start the session
session_start();
$current_user = $_SESSION['username'];

// Sanitize user input
$safe_current_user = $conn->real_escape_string($current_user);

// Get action and message IDs from the form
$do_messages = $_POST['do_messages'];
$do_action = $_POST['do_action'];
$message_ids = $_POST['message_ids']; // Assuming this is an array of IDs

// Convert message IDs to a comma-separated string for the SQL query
if (!empty($message_ids) && is_array($message_ids)) {
    $message_ids_str = implode(',', array_map('intval', $message_ids));

    // Determine SQL query based on the action
    if ($do_action == 'markread') {
        $update_sql = "UPDATE messages SET is_read = 1 WHERE ToUser = '$safe_current_user' AND id IN ($message_ids_str)";
    } elseif ($do_action == 'markunread') {
        $update_sql = "UPDATE messages SET is_read = 0 WHERE ToUser = '$safe_current_user' AND id IN ($message_ids_str)";
    } elseif ($do_action == 'delete') {
        $delete_sql = "DELETE FROM messages WHERE ToUser = '$safe_current_user' AND id IN ($message_ids_str)";
        if (!$conn->query($delete_sql)) {
            echo "Error deleting records: " . $conn->error;
        }
    }

    // Execute the update query if it's a mark as read/unread action
    if (isset($update_sql)) {
        if (!$conn->query($update_sql)) {
            echo "Error updating records: " . $conn->error;
        }
    }
}

// Close the connection
$conn->close();

// Redirect back to the messages page
header("Location: messages.php");
exit();
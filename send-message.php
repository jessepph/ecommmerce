<?php
session_start();
require("db.php");

// Ensure user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$to_user = isset($_POST['to_user']) ? htmlspecialchars($_POST['to_user']) : '';
$subject = isset($_POST['subject']) ? htmlspecialchars($_POST['subject']) : '';
$message = isset($_POST['message']) ? htmlspecialchars($_POST['message']) : '';
$original_message_id = isset($_POST['original_message_id']) ? intval($_POST['original_message_id']) : 0;
$from_user = $_SESSION['username'];

// Validate input
if (empty($to_user) || empty($subject) || empty($message)) {
    echo 'All fields are required.';
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "CoheedAndCambria666!", "market");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare the message for insertion into the messages table
$query = "INSERT INTO messages (sender, recipient, subject, message, original_message_id, sent_time) VALUES (?, ?, ?, ?, ?, NOW())";
$stmt = $conn->prepare($query);
$stmt->bind_param("ssssi", $from_user, $to_user, $subject, $message, $original_message_id);

if ($stmt->execute()) {
    $message_id = $stmt->insert_id;

    // Insert the message into the pm_inbox table for both the sender and recipient
    $inbox_query = "INSERT INTO pm_inbox (username, message_id, subject, message, sent_time) VALUES (?, ?, ?, ?, NOW())";
    
    // For the sender
    $stmt = $conn->prepare($inbox_query);
    $stmt->bind_param("siss", $from_user, $message_id, $subject, $message);
    $stmt->execute();

    // For the recipient
    $stmt = $conn->prepare($inbox_query);
    $stmt->bind_param("siss", $to_user, $message_id, $subject, $message);
    $stmt->execute();

    echo 'Message sent successfully';
} else {
    echo 'Failed to send message: ' . $conn->error;
}

$stmt->close();
$conn->close();
?>

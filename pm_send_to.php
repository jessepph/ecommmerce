<?php
//include('connect_i.php');
require_once("pm_check.php");
session_start();
// Database connection parameters
$host = 'localhost';
$user = 'root';
$password = 'CoheedAndCambria666!';
$database = 'market';
$port = 888;

// Create a new database connection
$conn = new mysqli($host, $user, $password, $database, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Retrieve the user's role from the database
$user_role = 'Buyer'; // Default value

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    
    // Prepare and execute the query to get the user's role
    $query = "SELECT account_role FROM register WHERE username = ?";
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        die("Database query preparation failed: " . $conn->error);
    }
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($user_role);
    $stmt->fetch();
    $stmt->close();
}

// Initialize success message flag
$message_sent = false;

// Handle message sending
if (isset($_POST['submit'])) {
    $to_username = $_POST['to_username'];
    $title = $_POST['title'];
    $content = $_POST['message']; // Renamed from 'content' to 'message' to match HTML form field
    $to_userid = $_POST['to_userid'];
    $userid = $_POST['userid'];
    $from_username = $_POST['from_username'];
    $senddate = $_POST['senddate'];

    if (!empty($to_username) && !empty($title) && !empty($content)) {
        $sqlInsert = "INSERT INTO pm_outbox (username, title, content, to_userid, userid, to_username, senddate) 
                      VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $connect->prepare($sqlInsert);
        $stmt->bind_param("sssssss", $username, $title, $content, $to_userid, $userid, $to_username, $senddate);
        if ($stmt->execute()) {
            $message_sent = true; // Set flag to true if message is sent successfully
        }
        $stmt->close();
    }
}

// Logged in person info
$sql = "SELECT id, username FROM register WHERE username='" . $_SESSION['username'] . "'";
$query = mysqli_query($connect, $sql);

while ($row = mysqli_fetch_array($query)) {
    $pid = $row['id'];
    $username = $row['username'];
}

// Fetch all users for the dropdown list
$sqlUsers = "SELECT id, username FROM register";
$queryUsers = mysqli_query($connect, $sqlUsers);
$users = [];
while ($row = mysqli_fetch_array($queryUsers)) {
    $users[] = $row;
}

$TOid = $TOuser = "";
if (isset($_POST['to_username'])) {
    $to_username = $_POST['to_username'];
    $sqlCommand = "SELECT id, username FROM register WHERE username='$to_username' LIMIT 1";
    $query = mysqli_query($connect, $sqlCommand);

    while ($row = mysqli_fetch_array($query)) {
        $TOid = $row['id'];
        $TOuser = $row['username'];
    }

    mysqli_free_result($query);
}

mysqli_close($connect);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Send Message</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="icon" type="image/jpeg" href="img/pentagram.jpg">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        .container {
            margin-top: 70px; /* Adjust this to ensure it fits below the fixed navigation bar */
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }
        .form-group input[type="submit"] {
            background-color: #14a1ed;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 4px;
        }
        .form-group input[type="submit"]:hover {
            background-color: #0e8bce;
        }
        .form-group input[readonly] {
            background-color: #f0f0f0;
        }
        .alert-success {
            color: green;
            background-color: #d4edda;
            border-color: #c3e6cb;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
            border: 1px solid transparent;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Send Message</h1>
        <?php if ($message_sent): ?>
            <div class="alert-success">
                Message Sent Successfully!
            </div>
        <?php endif; ?>
        <form action="pm_send_to.php" method="post">
            <div class="form-group">
                <label for="to_username">Sending To:</label>
                <select name="to_username" id="to_username">
                    <option value="">Select a user</option>
                    <?php foreach ($users as $user): ?>
                        <option value="<?php echo htmlspecialchars($user['username']); ?>"
                            <?php if ($user['username'] === $TOuser) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($user['username']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="title">Title:</label>
                <input name="title" type="text" id="title" />
            </div>
            <div class="form-group">
                <label for="message">Message:</label>
                <textarea name="message" id="message"></textarea>
            </div>
            <input name="to_userid" type="hidden" value="<?php echo htmlspecialchars($TOid); ?>" />
            <input name="userid" type="hidden" value="<?php echo htmlspecialchars($pid); ?>" />
            <input name="from_username" type="hidden" value="<?php echo htmlspecialchars($username); ?>" />
            <input name="senddate" type="hidden" value="<?php echo date("Y-m-d H:i:s"); ?>" /> <!-- Updated format -->
            <div class="form-group">
                <input type="submit" name="submit" id="submit" value="Send" />
            </div>
        </form>
    </div>
</body>
</html>

<?php
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

// Update the SQL query with the correct column name
$sql = "SELECT id, username FROM register"; // Adjust column names as needed

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

$options = "";

// Fetch and display user data
while ($row = mysqli_fetch_array($result)) {
    $USERid = $row['id'];
    $USERNAME = $row['username']; // Ensure this matches your database column name

    $options .= "<option value=\"$USERid\">" . htmlspecialchars($USERNAME) . "</option>";
}

// Close the database connection
mysqli_close($conn);
?>

<?php
// Ensure session is started and username is set
if (!isset($_SESSION['username'])) {
    die("User not logged in.");
}
$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asmodeus - Send A Message</title>
    <!-- CSS Files -->
    <link rel="stylesheet" href="Listings_files/flexboxgrid.min.css">
    <link rel="stylesheet" href="Listings_files/fontawesome-all.min.css">
    <link rel="stylesheet" href="Listings_files/style.css">
    <link rel="stylesheet" href="Listings_files/main.css">
    <link rel="stylesheet" href="Listings_files/responsive.css">
    <link rel="stylesheet" href="product-view.css">
    <link rel="stylesheet" href="sprite.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="password-strength-indicator.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/jpeg" href="img/pentagram.jpg">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <style>
        table {
            width: 100%;
            margin: 20px auto;
            border-collapse: collapse;
        }
        td, th {
            padding: 10px;
            border: 1px solid #ddd;
        }
        .message-row {
            text-align: center;
        }
        .message-title {
            font-weight: bold;
        }
        .message-title a {
            text-decoration: none;
        }
        .message-title a:hover {
            text-decoration: underline;
        }
        .message-actions {
            margin-top: 10px;
        }
        .message-actions a {
            margin-right: 10px;
            color: #007bff;
            text-decoration: none;
        }
        .message-actions a.delete {
            color: #dc3545;
        }
        .message-actions a i {
            margin-right: 5px;
        }
        .success-message {
            color: green;
            text-align: center;
            margin-top: 20px;
        }
        .error-message {
            color: red;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="navigation">
    <!-- Navigation content here -->
</div>

<form action="pm_send_to.php" id="form" method="post">
    <label for="to_username">Send message to:</label><br>
    <select id="to_username" name="to_username" style="width: 60%;">
        <?= $options; ?>
    </select><br><br>
    <input type="submit" style="color:white; background-color:#1a6cdd;" id="submit" value="Select User"/>
</form>

<p style="position:absolute; top: 178px; left: 375px;">You are currently logged in as, &nbsp;&nbsp;<?php echo htmlspecialchars($username); ?></p>

</body>
</html>

<?php
session_start();


// Database connection parameters
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'market';
//$port = 888;

// Create database connection using PDO
try {
    $dsn = "mysql:host=$host;dbname=$database;";
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Retrieve all users with the 'vendor' role from the 'register' table
$query = "SELECT id, username, bitcoin_wallet_address FROM register WHERE account_role = 'vendor'";
$stmt = $pdo->prepare($query);
$stmt->execute();
$vendors = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Insert each vendor into the 'vendors' table, ensuring no duplicates
foreach ($vendors as $vendor) {
    $vendorId = $vendor['id'];
    $username = $vendor['username'];
    $bitcoinWalletAddress = $vendor['bitcoin_wallet_address'];
    
    // Check if the vendor already exists in the 'vendors' table
    $checkVendorQuery = "SELECT COUNT(*) FROM vendors WHERE id = ?";
    $checkStmt = $pdo->prepare($checkVendorQuery);
    $checkStmt->execute([$vendorId]);
    $vendorExists = $checkStmt->fetchColumn();
    
    // If the vendor already exists, skip the insert without displaying a message
    if ($vendorExists > 0) {
        // Simply continue without displaying the message
        continue; // Skip to the next vendor without output
    }
    
    // Insert the vendor into the 'vendors' table
    $insertQuery = "INSERT INTO vendors (id, name, bitcoin_wallet_address) VALUES (?, ?, ?)";
    $insertStmt = $pdo->prepare($insertQuery);
    
    try {
        $insertStmt->execute([$vendorId, $username, $bitcoinWalletAddress]);
    } catch (PDOException $e) {
        // Optionally, handle errors silently or log them
        // Log the error or handle silently
    }
}

//echo "Vendor insertion process completed.";

// Initialize variables
$success_message = '';
$error_message = '';

// Function to sanitize input
function sanitize_input($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// Function to get a Bitcoin address from the file
function get_btc_address($filename) {
    if (!file_exists($filename)) {
        die("Bitcoin address file not found.");
    }

    $addresses = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    if (empty($addresses)) {
        die("No Bitcoin addresses available.");
    }

    $btc_address = array_shift($addresses);

    // Write the remaining addresses back to the file
    file_put_contents($filename, implode(PHP_EOL, $addresses));

    return $btc_address;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize input
    $username = sanitize_input($_POST['username']);
    $password = sanitize_input($_POST['userPassword']);
    $confirmPassword = sanitize_input($_POST['confirmPassword']);
    $pin = sanitize_input($_POST['pin']);
    $account_role = sanitize_input($_POST['account_role']);
    $vendor_rating = isset($_POST['vendor_rating']) ? (int)sanitize_input($_POST['vendor_rating']) : 0; 
    $vendorApproved = isset($_POST['vendorApproved']) ? (int)sanitize_input($_POST['vendorApproved']) : 0; // Set to 0 if not provided
    $bitcoin_balance = 0; // Default value for bitcoin_balance
    $available_bitcoin_balance = 0; // Default value for available_bitcoin_balance
    $dateJoined = date('Y-m-d'); // Set the current date
    $trust_level = 0; // Set default trust level
    $level = 0; // Set default level
    $total_orders = 0; // Set default total_orders
    $profile_image = ''; // Set default profile_image to an empty string

    // Basic validation
    if (empty($username) || empty($password) || empty($confirmPassword) || empty($pin) || empty($account_role)) {
        $error_message = 'All fields are required!';
    } elseif ($password !== $confirmPassword) {
        $error_message = 'Passwords do not match!';
    } elseif (!is_numeric($pin) || strlen($pin) < 4 || strlen($pin) > 6) {
        $error_message = 'PIN must be a numeric value between 4 and 6 digits!';
    } else {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Get a Bitcoin address from the file
        $bitcoin_wallet_address = get_btc_address('btc_deposit_wallet_addresses.txt');

        // Use prepared statements to prevent SQL injection
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO register 
                (username, userPassword, pin, account_role, vendor_rating, vendorApproved, bitcoin_balance, available_bitcoin_balance, dateJoined, trust_level, level, total_orders, profile_image, bitcoin_wallet_address) 
                VALUES 
                (:username, :userPassword, :pin, :account_role, :vendor_rating, :vendorApproved, :bitcoin_balance, :available_bitcoin_balance, :dateJoined, :trust_level, :level, :total_orders, :profile_image, :bitcoin_wallet_address)"
            );

            // Bind parameters
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->bindParam(':userPassword', $hashed_password, PDO::PARAM_STR);
            $stmt->bindParam(':pin', $pin, PDO::PARAM_INT);
            $stmt->bindParam(':account_role', $account_role, PDO::PARAM_STR);
            $stmt->bindParam(':vendor_rating', $vendor_rating, PDO::PARAM_INT);
            $stmt->bindParam(':vendorApproved', $vendorApproved, PDO::PARAM_INT);
            $stmt->bindParam(':bitcoin_balance', $bitcoin_balance, PDO::PARAM_STR);
            $stmt->bindParam(':available_bitcoin_balance', $available_bitcoin_balance, PDO::PARAM_STR);
            $stmt->bindParam(':dateJoined', $dateJoined, PDO::PARAM_STR);
            $stmt->bindParam(':trust_level', $trust_level, PDO::PARAM_INT);
            $stmt->bindParam(':level', $level, PDO::PARAM_INT);
            $stmt->bindParam(':total_orders', $total_orders, PDO::PARAM_INT);
            $stmt->bindParam(':profile_image', $profile_image, PDO::PARAM_STR);
            $stmt->bindParam(':bitcoin_wallet_address', $bitcoin_wallet_address, PDO::PARAM_STR);

            if ($stmt->execute()) {
                $success_message = 'Registration successful!';
            } else {
                $error_message = 'Error occurred. Please try again later.';
            }
        } catch (PDOException $e) {
            $error_message = 'Failed to prepare or execute the SQL statement: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css" />
    <link rel="stylesheet" href="style2.css">
    <link rel="icon" type="image/jpeg" href="img/pentagram.jpg">
    <script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body style="height: 135%;">
    <div class="container" style="
        margin-left: 40%;
        position: absolute;
        background: #fff;
        padding: 20px 30px;
        width: 420px;
        border-radius: 5px;
        box-shadow: 0 0 15px rgb(0 0 0 / 20%);
        top: 20px;">
        <img src="images/logo.png">
    </div>

    <div class="container" style="
        margin-left: 40%;
        margin-top: 187px;
        background: #fff;
        padding: 20px 30px;
        width: 420px;
        border-radius: 5px;
        box-shadow: 0 0 15px rgb(0 0 0 / 20%);">
        <h4 style="text-align:center;">Register Your Account</h4>
        <form method="post" action="" id="register_form">
            <div style="text-align:center;">
                <input style="width: 200px; border-radius: 2px; border: 1px solid #CCC; padding: 10px; color: #333; font-size: 14px; margin-top: 10px; text-align: center;" type="text" name="username" placeholder="Username" required>
                <input style="width: 200px; border-radius: 2px; border: 1px solid #CCC; padding: 10px; color: #333; font-size: 14px; margin-top: 10px; text-align: center;" type="password" name="userPassword" placeholder="Password" required>
                <input style="width: 200px; border-radius: 2px; border: 1px solid #CCC; padding: 10px; color: #333; font-size: 14px; margin-top: 10px; text-align: center;" type="password" name="confirmPassword" placeholder="Confirm Password" required>
                <input style="width: 200px; border-radius: 2px; border: 1px solid #CCC; padding: 10px; color: #333; font-size: 14px; margin-top: 10px; text-align: center;" type="text" name="pin" placeholder="4 to 6 digit PIN" required>
                <select id="account_role" name="account_role" style="width:200px; margin-top: 10px; border: 1px solid lightgrey; padding-left: 15px; border-radius: 5px; font-size: 15px;">
                    <option value="Buyer">Buyer</option>
                    <option value="Vendor">Vendor</option>
                </select>
                <div style="text-align:center; margin-top: 20px;">
                    <button type="submit" name="register" id="reg_btn" style="padding: 10px 25px; color: #fff; background-color: #0067ab; font-size: 16px; border: 1px solid #0164a5; border-radius: 2px; cursor: pointer;">Register</button>
                    <a href="login.php"><button type="button" name="login" id="login_btn" style="padding: 10px 25px; margin-left: 10px; color: #fff; background-color: #0067ab; font-size: 16px; border: 1px solid #0164a5; border-radius: 2px; cursor: pointer;">Login</button></a>
                </div>
            </div>
        </form>
    </div>

    <script>
    $(document).ready(function() {
        <?php if (!empty($success_message)) : ?>
            alertify.success('<?php echo htmlspecialchars($success_message, ENT_QUOTES, 'UTF-8'); ?>');
        <?php elseif (!empty($error_message)) : ?>
            alertify.error('<?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?>');
        <?php endif; ?>
    });
    </script>
</body>
</html>

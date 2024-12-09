<?php
// Database connection parameters
$servername = "localhost";
$dbusername = "root";
$password = "CoheedAndCambria666!";
$dbname = "market";

// Create a new database connection
$conn = new mysqli($servername, $dbusername, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    
    // Prepare and execute the query to get the user's role
    $query = "SELECT account_role FROM register WHERE username = ?";
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($user_role);
        $stmt->fetch();
        $stmt->close();
    } else {
        // Handle preparation error
        die("Database query preparation failed: " . $conn->error);
    }
}

// Check user role and conditionally show C Panel
$showControlPanel = ($user_role !== 'Buyer');


if (isset($_POST['register'])) {
	$username = $_POST["username"];
	//$email = $_POST['email'];
	$userPassword = $_POST["userPassword"];
	$confirmPassword = $_POST["confirmPassword"];
	$hash = password_hash($userPassword, PASSWORD_DEFAULT);
	$chash = password_hash($confirmPassword, PASSWORD_DEFAULT);
	$account_role = $_POST["account_role"];
	$pin = $_POST["pin"];
	$vendorApproved = 0;

	// username and emial validation
	$sql_u = "SELECT * FROM register WHERE username='$username'";
	$sql_up = "SELECT * FROM register WHERE userPassword='$hash'";
	$sql_cp = "SELECT * FROM register WHERE confirmPassword=$chash'";
	$sql_e = "SELECT * FROM register WHERE email='$email'";
	$sql_ar = "SELECT * FROM register WHERE acoount_role='$account_role'";
	$sql_pin = "SELECT * FROM register WHERE pin='$pin'";
	$res_u = mysqli_query($db, $sql_u);

	$username = mysqli_real_escape_string($db, $_POST["username"]);


	// password must be greater than 8 characters
	$password_length_invalid = strlen($userPassword) < 6;

	// password and confirm password do not match validation
	$passwords_do_not_match = $userPassword !== $confirmPassword;

	$pinLengthInvalid = strlen($pin) < 4;
	$pinLengthLong = strlen($pin) > 4;
	// must contain a 1 special character

	// must contain at least 1 capital letter

	// date  and time example: 2022-12-1 12:30:00
	// todo: set timezone to local 
	date_default_timezone_set('Asia/Kolkata');
	$timestamp = time();
	$date_time = date("Y-m-d H:i:s");
	// Given password
	// $userPassword = "";
	// $confirmPassword = "";
	// $password = "";
	// Validate password strength
	$uppercase = preg_match('@[A-Z]@', $userPassword);
	$lowercase = preg_match('@[a-z]@', $userPassword);
	$number    = preg_match('@[0-9]@', $userPassword);
	$specialChars = preg_match('@[^\w]@', $userPassword);
	$passwordValidated = $uppercase && $lowercase && $number && $specialChars && strlen($userPassword) < 6;
    $invalidPassword = !$uppercase || !$lowercase || !$number || !$specialChars;
	$myPassword = $_POST["userPassword"];

	if (mysqli_num_rows($res_u)) {
		$name_error = "Sorry... username already taken";
		
	}
    if ( ctype_alnum($myPassword))
       
		$password_error = "Invalid Password";
    
		else if ($passwords_do_not_match) {
			$confirmPassword_error = "The passwords must match";
		}
		else if ($pinLengthInvalid) {
			$confirmPassword_error = "Pin must be a minimum of 4 characters";
		}
		
		else if ($pinLengthLong) {
			$pin_error = "Pin can't be more than 4 characters in length";
		}

		else {
	     
		$password_error = "Valid Password Entry Inserted Into Database Successfully!";
		$query = "INSERT INTO register (username, userPassword, confirmPassword, pin, account_role,dateJoined,vendorApproved) 
		VALUES ('$username', '$hash','$chash','$pin','$account_role', '$date_time','$vendorApproved')";
		$results = mysqli_query($db, $query);
		header("Location: login.php");
		}

    
	

	
    /*
	if (empty(!$userPassword)) {

	}  else if ($password_length_invalid) {
		$password_error = "Password must be greater than 6 characters";
	} else if ($pinLengthInvalid) {
		$pin_error = "Pin must be a minimum of 4 characters";
	} else if ($pinLengthLong) {
		$pin_error = "Pin can't be more than 4 characters in length";
	} else if (!$passwordValidated) {
		$confirmPassword_error = "Note: Password must be alphanumeric";
	} else {
		$query = "INSERT INTO register (username, userPassword, confirmPassword, pin, account_role,dateJoined) 
		VALUES ('$username', '" . md5($userPassword) . "','" . md5($confirmPassword) . "','$pin','$account_role', '$date_time')";
		$results = mysqli_query($db, $query);

		header("Location: successful-registry.php");
		exit();
	}*/
}

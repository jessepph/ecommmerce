<?php
    require('db.php');
    session_start();
    include_once("insert-bitcoin-wallet-address.php");
// Database connection parameters
$servername = "localhost";
$dbusername = "root";
$password = "";
$dbname = "market";

// Create a new database connection
$conn = new mysqli($servername, $dbusername, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

    // If form submitted, insert values into the database.
    if (isset($_POST['username'])) {
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $password = $_POST['password'];
        
        $query = "SELECT * FROM `register` WHERE username='$username'";
        $result = mysqli_query($conn, $query) or die(mysqli_error($mysql));
        $rows = mysqli_num_rows($result);
        
        if ($rows == 1) {
            $row = mysqli_fetch_assoc($result);
            if (password_verify($password, $row['userPassword'])) {
                $_SESSION['username'] = $username;
                $_SESSION['dateJoined'] = $row['dateJoined'];
                $_SESSION['trust_level'] = $row['trust_level'];
                $_SESSION['total_orders'] = $row['total_orders'];
                $_SESSION['account_role'] = $row['account_role'];
                $account_role = $_SESSION['account_role'];

                switch ($account_role) {
                    case 'Admin':
                        header("Location: homepage.php");
                        exit();
                    case 'Vendor':
                        header("Location: homepage.php");
                        exit();
                    default:
                        header("Location: homepage.php");
                        exit();
                }
            } else {
                //Make this a pop windows alerrt that shows the message "Wrong Credentials!
                echo "<script>alert('You have entered the wrong credentials. Please login with correct username and password.'); window.location='login.php'</script>";
            }
        } else {
            echo "<script>alert('You have entered the wrong credentials. Please login with correct username and password.'); window.location='login.php'</script>";
        }

    }
    ?>

<!DOCTYPE html>
<html>
<head>
    <title>Bohemia - Login</title>
    <meta http-equiv="Content-type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="day.css" />
    <link rel="stylesheet" href="style.css" /> <!-- Include your custom styles -->
    <style>
        /* Add custom styles for responsiveness here */
        body {
            font-size: 16px;
            margin: 0;
            padding: 0;
        }

        .login-banner {
            background-color: #f3f3f3;
            text-align: center;
            padding: 5%;
        }

        .login-head-container {
            width: 80%;
            margin: 2% auto;
        }

        h1 {
            color: black;
        }

        .form {
            width: 80%;
            margin: 0 auto;
            text-align: center;
            border: 1px solid black;
            padding: 2%;
        }

        form {
            margin-top: 2%;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
        }

        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }

        p {
            margin-top: 2%;
        }
       
      
    </style>
</head>
<body>
    <div class="login-banner">
        <div class="login-head-container">
            <h1>ASMODEUS MARKET</h1>
        </div>
        <div class="form">
            <h1>Log In</h1>
            <form action="" method="post" name="login">
                <input type="text" name="username" placeholder="Username" required />
                <input type="password" name="password" placeholder="Password" required />
                <input name="login_btn" type="submit" value="Login" />
            </form>
            <p>Not registered yet? <a href='register.php'>Register Here</a></p>
        </div>
    </div>
</body>
</html>
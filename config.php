<?php
    define('USER', 'root');
    define('PASSWORD', '');
    define('HOST', 'localhost');
    define('DATABASE', 'market');
    try {
        $connection = new PDO("mysql:host=".HOST.";dbname=".DATABASE, USER, PASSWORD);
    } catch (PDOException $e) {
        exit("Error: " . $e->getMessage());
    }


    if (isset($_POST['register'])) {
        $username = $_POST['username'];
      $pin = $_POST['pin'];
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirmPassword'];
      $email = $_POST['email'];
      // username and emial validation
      $sql_u = "SELECT * FROM register WHERE username='$username'";
        $sql_e = "SELECT * FROM register WHERE email='$email'";
        $res_u = mysqli_query($db, $sql_u);
        $res_e = mysqli_query($db, $sql_e);
  
  
      // password must be greater than 8 characters
      $password_length_invalid = strlen($password) < 8;
  
      // password and confirm password do not match validation
      $passwords_do_not_match = $password !== $confirmPassword;
      
      // must contain a 1 special character
      
      // must contain at least 1 capital letter
  
      // date  and time example: 2022-12-1 12:30:00
      // todo: set timezone to local 
      date_default_timezone_set('Asia/Kolkata');
      $timestamp = time();
      $date_time = date("Y-m-d H:i:s");
      // Given password
       $password = 'user-input-pass';
  
       // Validate password strength
        $uppercase = preg_match('@[A-Z]@', $password);
        $lowercase = preg_match('@[a-z]@', $password);
        $number    = preg_match('@[0-9]@', $password);
        $specialChars = preg_match('@[^\w]@', $password);
       
  if(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8) {
      echo 'Password must be a minimum of 8 characters and use one special character such as % or *';
  }else{
      echo 'Strong password.';
  }
      
        if (mysqli_num_rows($res_u) > 0) {
          $name_error = "Sorry... username already taken"; 	
      }else if  ($passwords_do_not_match) {
          $password_error = "The passwords must match";
      }  else if ($password_length_invalid) {
          
          $password_error = "Password must be greater than 8 characters";
        }else{
          
          $query = "INSERT INTO register (username,password,confirmPassword,email,pin,dateJoined) 
                      VALUES ('$username','$email' ''".md5($password)."','".md5($confirmPassword)."','$pin','$date_time')";
             $results = mysqli_query($db, $query);
             
             exit();
        }
    
  
  
    if (isset($_POST['register'])) {
        $username = $_POST['username'];
        $pin = $_POST['pin'];
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirmPassword'];
        $confirmPassword = $_POST['email'];
      // username and emial validation
      $sql_u = "SELECT * FROM register WHERE username='$username'";
        $sql_e = "SELECT * FROM register WHERE email='$email'";
        $res_u = mysqli_query($db, $sql_u);
        $res_e = mysqli_query($db, $sql_e);
  
  
      // password must be greater than 8 characters
      $password_length_invalid = strlen($password) < 8;
  
      // password and confirm password do not match validation
      $passwords_do_not_match = $password !== $confirmPassword;
      
      // must contain a 1 special character
      
      // must contain at least 1 capital letter
  
      // date  and time example: 2022-12-1 12:30:00
      // todo: set timezone to local 
      date_default_timezone_set('Asia/Kolkata');
      $timestamp = time();
      $date_time = date("Y-m-d H:i:s");
      // Given password
       $password = 'user-input-pass';
  
       // Validate password strength
        $uppercase = preg_match('@[A-Z]@', $password);
        $lowercase = preg_match('@[a-z]@', $password);
        $number    = preg_match('@[0-9]@', $password);
        $specialChars = preg_match('@[^\w]@', $password);
       
  if(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) > 8) {
      echo 'Password must be a minimum of 8 characters and use one special character such as % or *';
  }else{
      echo 'Strong password.';
  }
      
        if (mysqli_num_rows($res_u) > 0) {
          $name_error = "Sorry... username already taken"; 	
      }else if  ($passwords_do_not_match) {
          $password_error = "The passwords must match";
      }  else if ($password_length_invalid) {
          
          $password_error = "Password must be greater than 8 characters";
        }else{
          
          $query = "INSERT INTO register (username,password,confirmPassword,email,pin,dateJoined) 
                      VALUES ('$username','$email', ''".md5($password)."','".md5($confirmPassword)."','$pin','$date_time')";
             $results = mysqli_query($db, $query);
             
             exit();
        }
        }
    }
?>
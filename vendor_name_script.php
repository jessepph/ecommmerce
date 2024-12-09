<?php
require_once("db.php");
session_start();

$query3 = "SELECT vendor_name FROM products WHERE vendor_name='$vendor_name'" ;
$result3 = mysqli_query($con,$query3);
$row = mysqli_fetch_array($result3);
echo $row;


?>
<html>
<head>
<link rel="icon" type="image/jpeg" href="img/pentagram.jpg">
</head>
<body>
    <h1>Sold by <?php echo "<p>" .$row. "</p>";?></h1>

</body>
</html>
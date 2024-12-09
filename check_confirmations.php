<?php
require_once("db.php");

$query = "SELECT txid FROM invoices WHERE status = 'pending'";
$stmt = $conn->prepare($query);
if ($stmt === false) {
    die('Error preparing the SQL query: ' . $conn->error);
}

$stmt->execute();
$stmt->store_result();
$stmt->bind_result($transactionId);

while ($stmt->fetch()) {
    checkTransactionStatus($transactionId); // Function to check the confirmation status
}
?>
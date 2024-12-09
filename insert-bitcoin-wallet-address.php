<?php
// Database credentials
$host = 'localhost';       // The database server
$dbname = 'market';        // The database name
$dbusername = 'root';      // The database username
$dbpassword = '';          // The database password (empty in this case)

try {
    // The PDO DSN (Data Source Name) specifies the database type and host
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8";
    
    // Create the PDO instance and set error handling to exceptions
    $pdo = new PDO($dsn, $dbusername, $dbpassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Optionally, you can set the charset to UTF8 for proper encoding
    $pdo->exec("SET NAMES 'utf8'");

} catch (PDOException $e) {
    // If there is an error during the connection, show an error message
    die("Connection failed: " . $e->getMessage());
}

// 1. Retrieve all vendors from the register table
$queryGetAllVendors = "
    SELECT r.username, r.bitcoin_wallet_address
    FROM register r
    WHERE r.bitcoin_wallet_address IS NOT NULL AND r.bitcoin_wallet_address != ''
";

$stmt = $pdo->prepare($queryGetAllVendors);
$stmt->execute();

$vendors = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($vendors) {
    // Loop through each vendor
    foreach ($vendors as $vendor) {
        $vendorUsername = $vendor['username'];
        $vendorBitcoinWalletAddress = $vendor['bitcoin_wallet_address'];

        // 2. Update the products table for all products where the vendor_name matches
        $queryUpdateProduct = "
            UPDATE products
            SET bitcoin_wallet_address = ?
            WHERE vendor_name = ? AND (bitcoin_wallet_address IS NULL OR bitcoin_wallet_address = '')
        ";

        $stmtUpdate = $pdo->prepare($queryUpdateProduct);
        $stmtUpdate->execute([$vendorBitcoinWalletAddress, $vendorUsername]);

        // Check how many rows were updated for this vendor
        $rowsUpdated = $stmtUpdate->rowCount();
        if ($rowsUpdated > 0) {
            //echo "Updated $rowsUpdated products for vendor $vendorUsername with Bitcoin wallet address.<br>";
        } else {
            //echo "No products found to update for vendor $vendorUsername.<br>";
        }

        // 3. Insert any new products with the correct Bitcoin wallet address if needed
        $queryInsertProduct = "
            INSERT INTO products (vendor_name, bitcoin_wallet_address)
            SELECT ?, ? 
            WHERE NOT EXISTS (
                SELECT 1 FROM products WHERE vendor_name = ? AND bitcoin_wallet_address IS NOT NULL
            )
        ";

        $stmtInsert = $pdo->prepare($queryInsertProduct);
        $stmtInsert->execute([$vendorUsername, $vendorBitcoinWalletAddress, $vendorUsername]);

        // Check if the insert was successful
        if ($stmtInsert->rowCount() > 0) {
            //echo "Inserted products for vendor $vendorUsername where wallet address was missing.<br>";
        }
    }
} else {
    //echo "No vendors found with a Bitcoin wallet address.";
}
?>
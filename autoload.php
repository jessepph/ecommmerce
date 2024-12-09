<?php
// Include Composer's autoloader
require_once __DIR__ . 'autoload.php';

// Now you can use any libraries you've installed
use BitWasp\Bitcoin\KeyFactory;

// Example usage of the Bitcoin library
$key = KeyFactory::create();
$privateKey = $key->getPrivateKey()->getHex();
echo "Private Key: " . $privateKey;
?>
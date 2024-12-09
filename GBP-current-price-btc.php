<?php

// Function to fetch Bitcoin price from CoinDesk API with retry mechanism
function fetchBitcoinPriceGBP() {
    $url = 'https://api.coindesk.com/v1/bpi/currentprice/GBP.json';
    $retryAttempts = 5; // Number of retry attempts
    $retryDelay = 1; // Initial retry delay in seconds

    for ($attempt = 1; $attempt <= $retryAttempts; $attempt++) {
        $response = @file_get_contents($url); // Suppress errors to handle them gracefully

        if ($response !== false) {
            $data = json_decode($response, true);

            // Check if the response is valid
            if (isset($data['bpi']['GBP']['rate'])) {
                return $data['bpi']['GBP']['rate'];
            } else {
                // Invalid response, retry
                continue;
            }
        }

        // Wait before retrying
        sleep($retryDelay);

        // Exponential backoff: increase retry delay exponentially
        $retryDelay *= 2;
    }

    // All retry attempts failed, return null
    return null;
}

// Fetch Bitcoin price in GBP
$bitcoinPriceGBP = fetchBitcoinPriceGBP();

if ($bitcoinPriceGBP !== null) {
    echo "£$bitcoinPriceGBP";
} else {
    echo "Failed to fetch Bitcoin price in GBP after multiple retry attempts.";
}
?>

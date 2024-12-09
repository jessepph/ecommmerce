<?php

// Function to fetch Bitcoin price with limited retries
function fetchBitcoinPriceWithRetries($retryCount = 0) {
    // Maximum number of retries
    $maxRetries = 5;
    
    // Check if maximum retries reached
    if ($retryCount >= $maxRetries) {
        echo "Maximum retry limit reached. Unable to fetch Bitcoin price.";
        return false;
    }
    
    // Set your CryptoCompare API key here
    $apiKey = 'YOUR_API_KEY';
    
    // Fetching Bitcoin price from CryptoCompare API
    $url = 'https://min-api.cryptocompare.com/data/price?fsym=BTC&tsyms=USD&api_key=' . $apiKey;
    
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($curl);
    $curl_error = curl_error($curl);
    
    curl_close($curl);
    
    // Check if request was successful
    if ($response === false) {
        // Request failed, retry after a delay
        sleep(5); // Wait for 5 seconds before retrying
        return fetchBitcoinPriceWithRetries($retryCount + 1); // Retry fetching Bitcoin price
    } else {
        // Request was successful, decode the JSON response
        $bitcoin_prices = json_decode($response, true);
        
        // Check if Bitcoin price exists in the response
        if (isset($bitcoin_prices['USD'])) {
            // Extract Bitcoin price from the response
            return $bitcoin_prices['USD'];
        } else {
            // Bitcoin price not found in the response, retry after a delay
            sleep(10); // Wait for 5 seconds before retrying
            return fetchBitcoinPriceWithRetries($retryCount + 1); // Retry fetching Bitcoin price
        }
    }
}

// Fetch Bitcoin price with retries
$bitcoin_price = fetchBitcoinPriceWithRetries();
if ($bitcoin_price !== false) {
    echo "$$bitcoin_price";
}

?>
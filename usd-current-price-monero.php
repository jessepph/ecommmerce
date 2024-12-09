<?php

// Function to fetch Monero price in USD from CoinCap.io API
function getMoneroPriceUSD() {
    $url = 'https://api.coincap.io/v2/assets/monero';

    // Retry mechanism for handling rate limiting
    $retry_count = 0;
    while ($retry_count < 3) {
        $response = @file_get_contents($url);
        if ($response === false) {
            // Delay before retrying
            usleep(500000); // 500ms
            $retry_count++;
        } else {
            break; // Successful response received
        }
    }

    // Check if response is valid
    if ($response === false) {
        return 0; // Return zero if unable to fetch data after retries
    }

    $data = json_decode($response, true);

    // Check if the expected data structure is present in the response
    if (!isset($data['data']['priceUsd'])) {
        return 0; // Return zero if data structure is not as expected
    }

    return $data['data']['priceUsd'];
}

// Fetch Monero price in USD
$monero_price_usd = getMoneroPriceUSD();

// Check if Monero price in USD is available
if ($monero_price_usd != 0) {
    echo "$" . number_format($monero_price_usd, 2);
} else {
    echo "Unable to retrieve Monero price in USD.";
}

?>
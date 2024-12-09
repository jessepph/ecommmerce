<?php

// Function to fetch Bitcoin price in AUD from CoinCap.io API
function getBitcoinPriceAUD() {
    $url = 'https://api.coincap.io/v2/assets/bitcoin';

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

    // Convert USD price to AUD (you may need to update the conversion rate dynamically)
    $usd_price = $data['data']['priceUsd'];
    $aud_price = $usd_price * 1.34; // Assuming a conversion rate of 1 USD = 1.34 AUD

    return $aud_price;
}

// Fetch Bitcoin price in AUD
$bitcoin_price_aud = getBitcoinPriceAUD();

// Check if Bitcoin price in AUD is available
if ($bitcoin_price_aud != 0) {
    echo "AU$ " . number_format($bitcoin_price_aud, 2);
} else {
    echo "Unable to retrieve Bitcoin price in AUD.";
}

?>

<?php

// Function to fetch Bitcoin price in AUD from CoinGecko API
function getBitcoinPriceAUD() {
    $url = 'https://api.coingecko.com/api/v3/simple/price?ids=bitcoin&vs_currencies=aud';

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
    if (!isset($data['bitcoin']['aud'])) {
        return 0; // Return zero if data structure is not as expected
    }

    return $data['bitcoin']['aud'];
}

?>

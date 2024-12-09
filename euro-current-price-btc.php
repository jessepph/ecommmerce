<?php

// Function to fetch Bitcoin price from CryptoCompare API with error handling
function getBitcoinPriceEUR() {
    $url = 'https://min-api.cryptocompare.com/data/price';
    $parameters = [
        'fsym' => 'BTC', // From Symbol (Bitcoin)
        'tsyms' => 'EUR', // To Symbol (Euro)
    ];

    $qs = http_build_query($parameters); // Build query string

    $request = "{$url}?{$qs}";

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $request,
        CURLOPT_RETURNTRANSFER => true,
    ));

    $response = curl_exec($curl);
    $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    // Check if request was successful (status 200)
    if ($http_status === 200) {
        $data = json_decode($response, true);

        // Check if the response contains Bitcoin price in euros
        if (isset($data['EUR'])) {
            return $data['EUR']; // Return Bitcoin price in euros
        } else {
            return null; // Bitcoin price in euros not found in response
        }
    } else {
        return null; // Error occurred or API request failed
    }
}

// Fetch Bitcoin price in euros
$bitcoin_price_eur = getBitcoinPriceEUR();

// Check if Bitcoin price in euros is available
if ($bitcoin_price_eur !== null) {
    echo "€" . number_format($bitcoin_price_eur, 2); // Format price with two decimals
} else {
    echo "Unable to retrieve Bitcoin price in euros.";
}

?>

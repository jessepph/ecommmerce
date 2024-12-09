<?php

// Function to fetch Monero price in GBP from CoinCap API
function getMoneroPriceGBP() {
    $api_key = 'YOUR_API_KEY'; // Replace with your CoinCap API key
    $url = 'https://api.coincap.io/v2/assets/monero';

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => array(
            'Accept: application/json',
            'Authorization: Bearer ' . $api_key
        )
    ));

    $response = curl_exec($curl);
    $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    // Check if request was successful (status 200)
    if ($http_status === 200) {
        $data = json_decode($response, true);

        // Check if the response contains Monero data
        if (isset($data['data']['priceUsd'])) {
            $price_usd = $data['data']['priceUsd'];
            $price_gbp = $price_usd * getUsdToGbpExchangeRate(); // Convert USD to GBP
            return $price_gbp;
        } else {
            return null; // Monero data not found in response
        }
    } else {
        return null; // Error occurred or API request failed
    }
}

// Function to get current USD to GBP exchange rate (dummy function for illustration)
function getUsdToGbpExchangeRate() {
    // Replace with actual exchange rate API call or use a fixed rate
    return 0.75; // Example: 1 USD = 0.75 GBP
}

// Fetch Monero price in GBP
$monero_price_gbp = getMoneroPriceGBP();

// Check if Monero price in GBP is available
if ($monero_price_gbp !== null) {
    echo "£" . number_format($monero_price_gbp, 2); // Format price with two decimals
} else {
    echo "Unable to retrieve Monero price in GBP.";
}

?>
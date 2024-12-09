<?php

// Function to fetch Bitcoin price from CryptoCompare API with error handling
function getBitcoinPriceCAD() {
    $api_key = 'YOUR_API_KEY'; // Replace with your CryptoCompare API key
    $url = 'https://min-api.cryptocompare.com/data/price?fsym=BTC&tsyms=CAD';

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => array(
            'Authorization: Apikey ' . $api_key
        )
    ));

    $response = curl_exec($curl);
    $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    // Check if request was successful (status 200)
    if ($http_status === 200) {
        $data = json_decode($response, true);

        // Check if the response contains Bitcoin price in CAD
        if (isset($data['CAD'])) {
            return $data['CAD']; // Return Bitcoin price in CAD
        } else {
            return null; // Bitcoin price in CAD not found in response
        }
    } else {
        return null; // Error occurred or API request failed
    }
}

// Fetch Bitcoin price in CAD
$bitcoin_price_cad = getBitcoinPriceCAD();

// Check if Bitcoin price in CAD is available
if ($bitcoin_price_cad !== null) {
    echo "CA$ " . number_format($bitcoin_price_cad, 2); // Format price with two decimals
} else {
    echo "Unable to retrieve Bitcoin price in CAD.";
}

?>

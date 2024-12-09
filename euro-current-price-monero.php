<?php

// Function to fetch Monero price in EUR from CryptoCompare API
function getMoneroPriceEUR() {
    $api_key = 'YOUR_API_KEY'; // Replace with your CryptoCompare API key
    $url = 'https://min-api.cryptocompare.com/data/price?fsym=XMR&tsyms=EUR';

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

        // Check if the response contains Monero price in EUR
        if (isset($data['EUR'])) {
            return $data['EUR']; // Return Monero price in EUR
        } else {
            return null; // Monero price in EUR not found in response
        }
    } else {
        return null; // Error occurred or API request failed
    }
}

// Fetch Monero price in EUR
$monero_price_eur = getMoneroPriceEUR();

// Check if Monero price in EUR is available
if ($monero_price_eur !== null) {
    echo "€" . number_format($monero_price_eur, 2); // Format price with two decimals
} else {
    echo "Unable to retrieve Monero price in EUR.";
}

?>
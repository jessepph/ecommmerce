<?php

// Function to fetch Monero price in AUD from CryptoCompare API
function getMoneroPriceAUD() {
    $url = 'https://min-api.cryptocompare.com/data/price?fsym=XMR&tsyms=AUD';

    // Initialize curl session
    $ch = curl_init();

    // Set curl options
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Timeout in seconds

    // Execute curl session
    $response = curl_exec($ch);

    // Check for curl errors
    if (curl_errno($ch)) {
        // Handle curl error (e.g., log, fallback, etc.)
        curl_close($ch);
        return 0; // Return zero or handle appropriately
    }

    // Close curl session
    curl_close($ch);

    // Decode JSON response
    $data = json_decode($response, true);

    // Check if the expected data structure is present in the response
    if (!isset($data['AUD'])) {
        return 0; // Return zero or handle appropriately
    }

    return $data['AUD'];
}

// Fetch Monero price in AUD
$monero_price_aud = getMoneroPriceAUD();

// Check if Monero price in AUD is available
if ($monero_price_aud != 0) {
    echo "AU$" . number_format($monero_price_aud, 2);
} else {
    echo "Price data unavailable at the moment."; // Custom message indicating temporary unavailability
}

?>
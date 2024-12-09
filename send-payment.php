<?php
session_start();

// Make sure the session data exists
if (!isset($_SESSION['vendor_wallet']) || !isset($_SESSION['expected_amount_btc'])) {
    exit("Error: Missing payment details.");
}

// Retrieve the vendor's wallet address and expected payment amount
$vendorWallet = $_SESSION['vendor_wallet'];
$expectedAmountBtc = $_SESSION['expected_amount_btc'];

// Generate the Bitcoin payment URL (bitcoin: URI scheme)
$paymentUrl = "bitcoin:$vendorWallet?amount=$expectedAmountBtc";

// Redirect user to their Bitcoin wallet or display QR code
// You can either redirect them to their wallet app or show a QR code for manual payment
header("Location: $paymentUrl");
exit;
?>

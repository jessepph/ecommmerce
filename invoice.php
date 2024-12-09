<?php
include_once "config2.php";
include_once "functions2.php";

// Check for the invoice code
if (!isset($_GET['code'])) {
    exit("No invoice code provided.");
}

$code = mysqli_escape_string($conn, $_GET['code']);

// Get invoice information
$address = getAddress($code);
$product = getInvoiceProduct($code);
$status = getStatus($code);
$price = getInvoicePrice($code);

// Function to format price as currency
function formatCurrency($price) {
    return number_format($price, 2); // Format to 2 decimal places
}

// Function to convert USD price to Bitcoin
function convertToBitcoin($usdPrice) {
    if (!is_numeric($usdPrice) || $usdPrice <= 0) {
        return 'Error: Invalid USD amount.';
    }

    $apiUrl = "https://api.coingecko.com/api/v3/simple/price?ids=bitcoin&vs_currencies=usd";

    $response = @file_get_contents($apiUrl);
    if ($response === false) {
        error_log("Error fetching data from API."); // Log the error
        return 'Error: Unable to fetch conversion rate.';
    }

    $data = json_decode($response, true);
    if (!isset($data['bitcoin']['usd'])) {
        error_log("Invalid response from API: " . $response); // Log invalid response
        return 'Error: Invalid response from API.';
    }

    $btcPrice = $data['bitcoin']['usd'];

    if ($btcPrice <= 0) {
        return 'Error: Invalid BTC price.';
    }

    return $usdPrice / $btcPrice;
}

// Attempt to convert price to BTC
$btcAmount = convertToBitcoin($price);
$roundedBtcAmount = is_numeric($btcAmount) ? round(floatval($btcAmount), 8) : 'Error: Unable to convert price to BTC. Please try again later.';

// Validate retrieved data
if (empty($address) || empty($product) || $price === null) {
    exit("Error: Unable to retrieve invoice details. Please try again.");
}

// Status translation
$statusval = $status;
$info = "";
switch ($status) {
    case 0:
    case 1:
        $status = "<span style='color: orangered' id='status'>PENDING</span>";
        $info = "<p>Your payment has been received. Invoice will be marked paid on two blockchain confirmations.</p>";
        break;
    case 2:
        $status = "<span style='color: green' id='status'>PAID</span>";
        break;
    case -1:
        $status = "<span style='color: red' id='status'>UNPAID</span>";
        break;
    case -2:
        $status = "<span style='color: red' id='status'>Too little paid, please pay the rest.</span>";
        break;
    default:
        $status = "<span style='color: red' id='status'>Error, expired</span>";
}

// Blockonomics payment integration
$blockonomics_api_key = 'g1UBefPGx6YvjBVpqGPn0xhFHg74u9FkAq4ScuVGqHg'; // Your Blockonomics API key
$blockonomics_url = "https://www.blockonomics.co/api/new_address?api_code={$blockonomics_api_key}";

$address_response = @file_get_contents($blockonomics_url);
if ($address_response === false) {
    error_log("Error fetching address from Blockonomics API.");
    exit("Error: Unable to retrieve payment address. Please check your API key and try again.");
}

$address_data = json_decode($address_response, true);
if (!isset($address_data['address'])) {
    error_log("Invalid response from Blockonomics: " . $address_response);
    exit("Error: Unable to retrieve payment address. Response: " . $address_response);
}

$paymentAddress = $address_data['address'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .row {
            width: 80%;
            margin: 0 auto;
        }
        .product-hold {
            width: 100%;
            margin-top: 35px;
        }
        .product {
            width: 25%;
            float: left;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="index.php">Bitcoin Example</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="index.php">Store</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="orders.php">Purchases</a>
                </li>
            </ul>
        </div>
    </nav>

    <main>
        <div class="row">
            <h1 style="width:100%;">Invoice</h1>
            <?php if ($roundedBtcAmount === 'Error: Invalid conversion'): ?>
                <p style="color: red;">Error: Unable to convert price to BTC. Please try again later.</p>
            <?php else: ?>
                <p>Please pay <strong><?php echo $roundedBtcAmount; ?> BTC</strong> to address: <span id="address"><?php echo $paymentAddress; ?></span></p>
            <?php endif; ?>
            
            <?php
            // QR code generation
            $cht = "qr";
            $chs = "300x300";
            $chl = $paymentAddress;
            $choe = "UTF-8";
            $qrcode = "https://chart.googleapis.com/chart?cht={$cht}&chs={$chs}&chl={$chl}&choe={$choe}";
            ?>
            <div class="qr-hold">
                <img src="<?php echo $qrcode; ?>" alt="My QR code" style="width:250px;">
            </div>
            <p style="display:block;width:100%;">Status: <?php echo $status; ?></p>
            <?php echo $info; ?>
            <h2 style="width:100%; margin-top: 20px;">What you're paying for:</h2>
            <h4 style="width:100%; margin-top: 20px;"><?php echo getProduct($product); ?></h4>
            <p><?php echo getDescription($product); ?></p>
        </div>
    </main>

    <script>
        var status = <?php echo $statusval; ?>;
        
        // Create socket variables
        if (status < 2 && status !== -2) {
            var addr = document.getElementById("address").innerHTML;
            var wsuri2 = "wss://www.blockonomics.co/payment/" + addr;
            var socket = new WebSocket(wsuri2, "protocolOne");
            socket.onmessage = function(event) {
                var response = JSON.parse(event.data);
                // Refresh page if payment moved up one status
                if (response.status > status) {
                    setTimeout(function() { window.location.reload(); }, 1000);
                }
            }
        }
    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

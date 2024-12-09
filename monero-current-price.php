

<?php


$curl = curl_init();

curl_setopt_array($curl, [
	CURLOPT_URL => "https://monero-price-tracker.p.rapidapi.com/api/asset",
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_ENCODING => "",
	CURLOPT_MAXREDIRS => 10,
	CURLOPT_TIMEOUT => 30,
	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	CURLOPT_CUSTOMREQUEST => "GET",
	CURLOPT_HTTPHEADER => [
		"X-RapidAPI-Host: monero-price-tracker.p.rapidapi.com",
		"X-RapidAPI-Key: 56698bb1admshc0e0a095a299fb1p1a0a07jsn274ce4182d07"
	],
]);

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
	echo "cURL Error #:" . $err;
} else {
	echo $response;
}
  
?>

<!-- code here -->
<?php

$url = "https://bitpay.com/api/rates";
$json = json_decode(file_get_contents($url));
$dollar = $btc = 0;
foreach($json as $obj){
  if($obj->code=='USD') $btc = $obj->rate;
 
}

echo '<p style="margin-top:15px;">1 bitcoin = $'. $btc . ' USD<br></p>';
//echo '10 dollars = '. round($dollar*10,8) . ' BTC<br>';
?>
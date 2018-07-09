<?php

require_once __DIR__ . '/PriceMonitorHttpClient.php';
 
$client = new PriceMonitorHttpClient();
$res = $client->request(
    'POST',
    'http://6ec15927.ngrok.io/api/updateContractInfo'
);
 
/** @return array */
return json_decode($res->getBody(), true);

?>
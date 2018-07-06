<?php

require_once __DIR__ . '/PriceMonitorHttpClient.php';
 

$client = new PriceMonitorHttpClient();
$res = $client->request(
    'GET',
    'https://plentymiddleware.000webhostapp.com/articles'
);
 
/** @return array */
return json_decode($res->getBody(), true);
<?php

require_once __DIR__ . '/PriceMonitorHttpClient.php';
 

$client = new PriceMonitorHttpClient();
$res = $client->request(
    'GET',
    'https://jsonplaceholder.typicode.com/posts/1'
);
 
/** @return array */
return json_decode($res->getBody(), true);
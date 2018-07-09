<?php

require_once __DIR__ . '/PriceMonitorHttpClient.php';
 

$client = new PriceMonitorHttpClient();
$res = $client->request(
    'GET',
    'http://6ec15927.ngrok.io/api/login?email=goran.stamenkovski@logeecom.com&password=Goran'
);
 
/** @return array */
return json_decode($res->getBody(), true);
<?php

require_once __DIR__ . '/PriceMonitorHttpClient.php';

$middlewareBaseUrl = SdkRestApi::getParam('gatewayBasePath');

$pricemonitorId = SdkRestApi::getParam('pricemonitorId');
$isEnabled = SdkRestApi::getParam('isEnabled');


$client = new PriceMonitorHttpClient();
$res = $client->request(
    'POST',
    $middlewareBaseUrl.'/api/saveSchedulePrices',
    [],
    [
        'pricemonitorId' => $pricemonitorId,
        'isEnabled' => $isEnabled
    ]
);
 
/** @return array */
return json_decode($res->getBody(), true);

?>
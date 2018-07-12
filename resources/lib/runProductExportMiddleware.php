<?php

require_once __DIR__ . '/PriceMonitorHttpClient.php';

$middlewareBaseUrl = SdkRestApi::getParam('gatewayBasePath');
 
$priceMonitorId = SdkRestApi::getParam('pricemonitorId');

$client = new PriceMonitorHttpClient();
$res = $client->request(
    'GET',
    $middlewareBaseUrl.'/api/runProductExport?pricemonitorId='.$priceMonitorId
);
 
/** @return array */
return json_decode($res->getBody(), true);

?>
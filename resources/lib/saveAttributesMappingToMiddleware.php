<?php

require_once __DIR__ . '/PriceMonitorHttpClient.php';

$middlewareBaseUrl = SdkRestApi::getParam('gatewayBasePath');

$priceMonitorId = SdkRestApi::getParam('priceMonitorId');
$mappings = SdkRestApi::getParam('mappings');


$client = new PriceMonitorHttpClient();
$res = $client->request(
    'POST',
    $middlewareBaseUrl.'/api/saveAttributesMapping',
    [],
    [
        'priceMonitorId' => $priceMonitorId,
        'mappings' => json_encode($mappings)
    ]
);
 
/** @return array */
return json_decode($res->getBody(), true);

?>
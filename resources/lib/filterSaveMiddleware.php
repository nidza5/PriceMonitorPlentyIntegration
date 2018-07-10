<?php

require_once __DIR__ . '/PriceMonitorHttpClient.php';

$middlewareBaseUrl = SdkRestApi::getParam('gatewayBasePath');

$filters = SdkRestApi::getParam('filters');
$type = SdkRestApi::getParam('type');
$priceMonitorId = SdkRestApi::getParam('priceMonitorId');


$client = new PriceMonitorHttpClient();
$res = $client->request(
    'POST',
    $middlewareBaseUrl.'/api/saveFilter',
    [],
    [
        'filters' => $filters,
        'type' => $type,
        'priceMonitorId' => $priceMonitorId
    ]
);
 
/** @return array */
return json_decode($res->getBody(), true);

?>
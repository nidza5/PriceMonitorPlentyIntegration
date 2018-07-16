<?php

require_once __DIR__ . '/PriceMonitorHttpClient.php';

$middlewareBaseUrl = SdkRestApi::getParam('gatewayBasePath');

$filters = SdkRestApi::getParam('filters');
$type = SdkRestApi::getParam('type');
$priceMonitorId = SdkRestApi::getParam('priceMonitorId');
$tenantId = SdkRestApi::getParam('tenantId');
$access_token = SdkRestApi::getParam('access_token');


$client = new PriceMonitorHttpClient();
$res = $client->request(
    'POST',
    $middlewareBaseUrl.'/api/saveFilter',
    ['Authorization' => $access_token ],
    [
        'filters' => $filters,
        'type' => $type,
        'priceMonitorId' => $priceMonitorId,
        'tenantId' => $tenantId
    ]
);
 
/** @return array */
return json_decode($res->getBody(), true);

?>
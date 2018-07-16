<?php

require_once __DIR__ . '/PriceMonitorHttpClient.php';

$middlewareBaseUrl = SdkRestApi::getParam('gatewayBasePath');

$priceMonitorId = SdkRestApi::getParam('priceMonitorId');
$mappings = SdkRestApi::getParam('mappings');
$tenantId = SdkRestApi::getParam('tenantId');
$access_token = SdkRestApi::getParam('accessToken');


$client = new PriceMonitorHttpClient();
$res = $client->request(
    'POST',
    $middlewareBaseUrl.'/api/saveAttributesMapping',
    ['Authorization' => 'Bearer '.$access_token],
    [
        'priceMonitorId' => $priceMonitorId,
        'mappings' => json_encode($mappings),
        'tenantId' => $tenantId
    ]
);
 
/** @return array */
return json_decode($res->getBody(), true);

?>
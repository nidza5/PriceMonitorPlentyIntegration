<?php

require_once __DIR__ . '/PriceMonitorHttpClient.php';

$middlewareBaseUrl = SdkRestApi::getParam('gatewayBasePath');

$filterType = SdkRestApi::getParam('filterType');

$priceMonitorId = SdkRestApi::getParam('priceMonitorId');

$allVariations = SdkRestApi::getParam('allVariations');

$attributesFromPlenty = SdkRestApi::getParam('attributesFromPlenty');

$tenantId = SdkRestApi::getParam('tenantId');

$access_token = SdkRestApi::getParam('accessToken');

$client = new PriceMonitorHttpClient();

$res = $client->request(
    'POST',
    $middlewareBaseUrl.'/api/preview',
    ['Authorization' => 'Bearer '.$access_token],
    [
        'filterType' => $filterType,
        'priceMonitorId' => $priceMonitorId,
        'allVariations' => json_encode($allVariations),
        'attributesFromPlenty' => json_encode($attributesFromPlenty),
        'tenantId' => $tenantId
    ]
);
 
/** @return array */
return json_decode($res->getBody(), true);

?>
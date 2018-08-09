<?php

require_once __DIR__ . '/PriceMonitorHttpClient.php';

$middlewareBaseUrl = SdkRestApi::getParam('gatewayBasePath');

$pricemonitorId = SdkRestApi::getParam('pricemonitorId');
$isEnabled = SdkRestApi::getParam('enableImport');
$tenantId = SdkRestApi::getParam('tenantId');
$access_token = SdkRestApi::getParam('accessToken');

$client = new PriceMonitorHttpClient();
$res = $client->request(
    'POST',
    $middlewareBaseUrl.'/api/saveSchedulePrices',
    ['Authorization' => 'Bearer '.$access_token],
    [
        'pricemonitorId' => $pricemonitorId,
        'enableImport' => $isEnabled,
        'tenantId' => $tenantId
    ]
);
 
/** @return array */
return json_decode($res->getBody(), true);

?>
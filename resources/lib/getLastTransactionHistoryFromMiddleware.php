<?php

require_once __DIR__ . '/PriceMonitorHttpClient.php';

$middlewareBaseUrl = SdkRestApi::getParam('gatewayBasePath');
 
$type = SdkRestApi::getParam('type');

$priceMonitorId = SdkRestApi::getParam('pricemonitorId');

$tenantId = SdkRestApi::getParam('tenantId');

$access_token = SdkRestApi::getParam('accessToken');

$client = new PriceMonitorHttpClient();
$res = $client->request(
    'GET',
    $middlewareBaseUrl.'/api/getLastTransactionHistory?pricemonitorId='.$priceMonitorId.'&type='.$type.'&tenantId='.$tenantId,
    ['Authorization' => 'Bearer '.$access_token]
);
 
/** @return array */
return json_decode($res->getBody(), true);

?>
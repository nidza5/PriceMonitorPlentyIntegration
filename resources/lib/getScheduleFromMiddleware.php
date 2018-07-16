<?php

require_once __DIR__ . '/PriceMonitorHttpClient.php';

$middlewareBaseUrl = SdkRestApi::getParam('gatewayBasePath');
 
$priceMonitorId = SdkRestApi::getParam('pricemonitorId');

$tenantId = SdkRestApi::getParam('tenantId');

$access_token = SdkRestApi::getParam('accessToken');

$client = new PriceMonitorHttpClient();
$res = $client->request(
    'GET',
    $middlewareBaseUrl.'/api/getSchedule?pricemonitorId='.$priceMonitorId.'&tenantId='.$tenantId,
    ['Authorization' => 'Bearer '.$access_token]
);
 
/** @return array */
return json_decode($res->getBody(), true);

?>
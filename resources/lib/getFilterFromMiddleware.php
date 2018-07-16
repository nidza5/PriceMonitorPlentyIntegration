<?php

require_once __DIR__ . '/PriceMonitorHttpClient.php';

$middlewareBaseUrl = SdkRestApi::getParam('gatewayBasePath');
 
$filterType = SdkRestApi::getParam('filterType');

$priceMonitorId = SdkRestApi::getParam('priceMonitorId');

$tenantId = SdkRestApi::getParam('tenantId');

$access_token = SdkRestApi::getParam('accessToken');

$client = new PriceMonitorHttpClient();
$res = $client->request(
    'GET',
    $middlewareBaseUrl.'/api/getFilters?priceMonitorId='.$priceMonitorId.'&filterType='.$filterType.'&tenantId='.$tenantId,
    ['Authorization' => 'Bearer '.$access_token]
);
 
/** @return array */
return json_decode($res->getBody(), true);

?>
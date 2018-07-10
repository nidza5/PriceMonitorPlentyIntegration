<?php

require_once __DIR__ . '/PriceMonitorHttpClient.php';

$middlewareBaseUrl = SdkRestApi::getParam('gatewayBasePath');
 
$filterType = SdkRestApi::getParam('filterType');

$priceMonitorId = SdkRestApi::getParam('priceMonitorId');

$client = new PriceMonitorHttpClient();
$res = $client->request(
    'GET',
    $middlewareBaseUrl.'/api/getFilters?priceMonitorId='.$priceMonitorId.'&filterType='.$filterType
);
 
/** @return array */
return json_decode($res->getBody(), true);

?>
<?php

require_once __DIR__ . '/PriceMonitorHttpClient.php';

$middlewareBaseUrl = SdkRestApi::getParam('gatewayBasePath');
 
$priceMonitorId = SdkRestApi::getParam('priceMonitorContractId');

$client = new PriceMonitorHttpClient();
$res = $client->request(
    'GET',
    $middlewareBaseUrl.'/api/getMappedAttributes?priceMonitorContractId='.$priceMonitorId
);
 
/** @return array */
return json_decode($res->getBody(), true);

?>
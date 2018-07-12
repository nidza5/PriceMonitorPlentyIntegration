<?php

require_once __DIR__ . '/PriceMonitorHttpClient.php';

$middlewareBaseUrl = SdkRestApi::getParam('gatewayBasePath');
 
$pricemonitorId = SdkRestApi::getParam('pricemonitorId');
$masterId = SdkRestApi::getParam('masterId');
$type = SdkRestApi::getParam('type');
$limit = SdkRestApi::getParam('limit');
$offset = SdkRestApi::getParam('offset');

$client = new PriceMonitorHttpClient();
$res = $client->request(
    'GET',
    $middlewareBaseUrl.'/api/getTransactionHistory?pricemonitorId='.$pricemonitorId.'&masterId='.$masterId.'&type='.$type.'&limit='.$limit.'&offset='.$offset 
);
 
/** @return array */
return json_decode($res->getBody(), true);

?>
<?php

require_once __DIR__ . '/PriceMonitorHttpClient.php';

$middlewareBaseUrl = SdkRestApi::getParam('gatewayBasePath');

$startAt = SdkRestApi::getParam('startAt');
$enableExport = SdkRestApi::getParam('enableExport');
$exportInterval = SdkRestApi::getParam('exportInterval');
$pricemonitorId = SdkRestApi::getParam('pricemonitorId');
$tenantId = SdkRestApi::getParam('tenantId');
$access_token = SdkRestApi::getParam('accessToken');

$client = new PriceMonitorHttpClient();
$res = $client->request(
    'POST',
    $middlewareBaseUrl.'/api/saveSchedule',
    ['Authorization' => 'Bearer '.$access_token],
    [
        'pricemonitorId' => $pricemonitorId,
        'startAt' => $startAt,
        'enableExport' => $enableExport,
        'exportInterval' => $exportInterval,
        'tenantId' => $tenantId
    ]
);
 
/** @return array */
return json_decode($res->getBody(), true);

?>
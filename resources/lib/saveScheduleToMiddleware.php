<?php

require_once __DIR__ . '/PriceMonitorHttpClient.php';

$middlewareBaseUrl = SdkRestApi::getParam('gatewayBasePath');

$startAt = SdkRestApi::getParam('startAt');
$enableExport = SdkRestApi::getParam('enableExport');
$exportInterval = SdkRestApi::getParam('exportInterval');
$pricemonitorId = SdkRestApi::getParam('pricemonitorId');


$client = new PriceMonitorHttpClient();
$res = $client->request(
    'POST',
    $middlewareBaseUrl.'/api/saveSchedule',
    [],
    [
        'pricemonitorId' => $pricemonitorId,
        'startAt' => $startAt,
        'enableExport' => $enableExport,
        'exportInterval' => $exportInterval
    ]
);
 
/** @return array */
return json_decode($res->getBody(), true);

?>
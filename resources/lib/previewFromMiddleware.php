<?php

require_once __DIR__ . '/PriceMonitorHttpClient.php';

$middlewareBaseUrl = SdkRestApi::getParam('gatewayBasePath');

$filterType = SdkRestApi::getParam('filterType');

$priceMonitorId = SdkRestApi::getParam('priceMonitorId');

$allVariations = SdkRestApi::getParam('allVariations');

$attributesFromPlenty = SdkRestApi::getParam('attributesFromPlenty');

$client = new PriceMonitorHttpClient();
// $res = $client->request(
//     'GET',
//     $middlewareBaseUrl.'/api/preview?filterType='.$filterType.'&priceMonitorId='.$priceMonitorId.'&allVariations[]='.$allVariations.'&attributesFromPlenty[]='.$attributesFromPlenty
// );

$res = $client->request(
    'POST',
    $middlewareBaseUrl.'/api/preview',
    [],
    [
        'filterType' => $filterType,
        'priceMonitorId' => $priceMonitorId,
        'allVariations' => json_encode($allVariations),
        'attributesFromPlenty' => json_encode($attributesFromPlenty)
    ]
);
 
/** @return array */
return json_decode($res->getBody(), true);

?>
<?php

require_once __DIR__ . '/PriceMonitorHttpClient.php';
 
$middlewareBaseUrl = SdkRestApi::getParam('gatewayBasePath');

$idContract = SdkRestApi::getParam('idContract');
$priceMonitorId = SdkRestApi::getParam('priceMonitorId');
$salesPriceImportIn = SdkRestApi::getParam('salesPriceImportIn');
$isInsertSalesPrice = SdkRestApi::getParam('isInsertSalesPrice');


$client = new PriceMonitorHttpClient();
$res = $client->request(
    'POST',
    $middlewareBaseUrl +'/api/updateContractInfo',
    [
    ],
    [
        'id' => $idContract,
        'priceMonitorId' => $priceMonitorId,
        'salesPricesImport' => $salesPriceImportIn,
        'isInsertSalesPrice' => $isInsertSalesPrice
    ]
);
 
/** @return array */
return json_decode($res->getBody(), true);

?>
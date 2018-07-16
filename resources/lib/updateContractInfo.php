<?php

require_once __DIR__ . '/PriceMonitorHttpClient.php';
 
$middlewareBaseUrl = SdkRestApi::getParam('gatewayBasePath');

$idContract = SdkRestApi::getParam('idContract');
$priceMonitorId = SdkRestApi::getParam('priceMonitorId');
$salesPriceImportIn = SdkRestApi::getParam('salesPriceImportIn');
$isInsertSalesPrice = SdkRestApi::getParam('isInsertSalesPrice');
$tenantId = SdkRestApi::getParam('tenantId');
$access_token = SdkRestApi::getParam('access_token');


$client = new PriceMonitorHttpClient();
$res = $client->request(
    'POST',
    $middlewareBaseUrl.'/api/updateContractInfo',
    [ 'Authorization' => $access_token ],
    [
        'id' => $idContract,
        'priceMonitorId' => $priceMonitorId,
        'salesPricesImport' => $salesPriceImportIn,
        'isInsertSalesPrice' => $isInsertSalesPrice,
        'tenantId' => $tenantId
    ]
);
 
/** @return array */
return json_decode($res->getBody(), true);

?>
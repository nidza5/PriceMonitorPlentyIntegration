<?php

require_once __DIR__ . '/PriceMonitorHttpClient.php';
 
$middlewareBaseUrl = SdkRestApi::getParam('gatewayBasePath');

$idContract = SdkRestApi::getParam('idContract');
$priceMonitorId = SdkRestApi::getParam('priceMonitorId');
$salesPriceImportIn = SdkRestApi::getParam('salesPriceImportIn');
$isInsertSalesPrice = SdkRestApi::getParam('isInsertSalesPrice');
$tenantId = SdkRestApi::getParam('tenantId');
$access_token = SdkRestApi::getParam('accessToken');

$client = new PriceMonitorHttpClient();
$res = $client->request(
    'POST',
    $middlewareBaseUrl.'/api/updateContractInfo',
    [ 'Authorization' => 'Bearer '.$access_token],
    [
        'id' => $idContract,
        'priceMonitorId' => $priceMonitorId,
        'salesPricesImport' => $salesPriceImportIn,
        'isInsertSalesPrice' => $isInsertSalesPrice,
        'tenantId' => $tenantId,
        'access_token' => $access_token
    ]
);
 
/** @return array */
return json_decode($res->getBody(), true);

?>
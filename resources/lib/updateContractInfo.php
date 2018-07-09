<?php

require_once __DIR__ . '/PriceMonitorHttpClient.php';
 
$idContract = SdkRestApi::getParam('idContract');
$priceMonitorId = SdkRestApi::getParam('priceMonitorId');
$salesPriceImportIn = SdkRestApi::getParam('salesPriceImportIn');
$isInsertSalesPrice = SdkRestApi::getParam('isInsertSalesPrice');


$client = new PriceMonitorHttpClient();
$res = $client->request(
    'POST',
    'http://6ec15927.ngrok.io/api/updateContractInfo',
    array(
        'body' => array(
            'idContract' => $idContract,
            'priceMonitorId' => $priceMonitorId,
            'salesPriceImportIn' => $salesPriceImportIn,
            'isInsertSalesPrice' => $isInsertSalesPrice
        )
    )
);
 
/** @return array */
return json_decode($res->getBody(), true);

?>
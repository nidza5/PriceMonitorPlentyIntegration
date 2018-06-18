<?php

require_once __DIR__ . '/PriceMonitorSdkHelper.php';

try {
    
    $masterId = SdkRestApi::getParam('masterId');
    $pricemonitorId = SdkRestApi::getParam('pricemonitorId');
    $limit = SdkRestApi::getParam('limit');
    $offset = SdkRestApi::getParam('offset');
    $transactionHistoryDetailsRecord = SdkRestApi::getParam('transactionHistoryDetailsRecord');
    $totalDetailedRecords = SdkRestApi::getParam('totalDetailedRecords');
    $transactionHistoryRecords = SdkRestApi::getParam('transactionHistoryRecords');
    $totalHistoryRecords = SdkRestApi::getParam('totalHistoryRecords');

    return PriceMonitorSdkHelper::getTransHistoryDetails($pricemonitorId,$masterId,$limit,$offset);

} catch(\Exception $ex) {
    
    $response = [
        'Code' => $ex->getCode(),
        'Message' => $ex->getMessage()
     ];

    return $response;
}


?>
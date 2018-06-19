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

    $emailForConfig = SdkRestApi::getParam('emailForConfig');
    $passwordForConfig = SdkRestApi::getParam('passwordForConfig');

     PriceMonitorSdkHelper::registerConfigService($emailForConfig,$passwordForConfig);

    return PriceMonitorSdkHelper::getTransHistoryDetails($pricemonitorId,$masterId,$limit,$offset,$transactionHistoryDetailsRecord,$totalDetailedRecords,$transactionHistoryRecords,$totalHistoryRecords);

} catch(\Exception $ex) {
    
    $response = [
        'Code' => $ex->getCode(),
        'Message' => $ex->getMessage()
     ];

    return $response;
}


?>
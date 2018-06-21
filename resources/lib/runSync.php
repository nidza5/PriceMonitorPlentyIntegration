<?php

require_once __DIR__ . '/RunnerService.php';
require_once __DIR__ . '/PriceMonitorSdkHelper.php';

try {

   $queueModel = SdkRestApi::getParam('queueModel');
   $queueName = SdkRestApi::getParam('queueName');
   
   $emailForConfig = SdkRestApi::getParam('emailForConfig');
   $passwordForConfig = SdkRestApi::getParam('passwordForConfig');

   $filterType = SdkRestApi::getParam('filterType');
   $priceMonitorId = SdkRestApi::getParam('priceMonitorId');
   $productFilterRepo = SdkRestApi::getParam('productFilterRepo');

   $products = SdkRestApi::getParam('productFilterRepo');

   PriceMonitorSdkHelper::registerConfigService($emailForConfig,$passwordForConfig);

   $runnerService = new RunnerService($queueModel);
   return  $runnerService->runSync($queueName);

} catch(\Exception $ex) {
    
    $response = [
        'Code' => $ex->getCode(),
        'Message' => $ex->getMessage()
     ];

    return $response;
}

?>
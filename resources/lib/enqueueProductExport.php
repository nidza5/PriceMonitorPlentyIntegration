<?php

require_once __DIR__ . '/RunnerService.php';
require_once __DIR__ . '/PriceMonitorSdkHelper.php';
require_once __DIR__ . '/QueueStorage.php';

try {

    $priceMonitorId = SdkRestApi::getParam('priceMonitorId');

    $queueModel = SdkRestApi::getParam('queueModel');

    $runnerService = new RunnerService($queueModel);

    $queueStorage = new QueueStorage($queueModel);

    return ["peek" =>$queueStorage->peek("Default")];

    // $emailForConfig = SdkRestApi::getParam('emailForConfig');

    // $passwordForConfig = SdkRestApi::getParam('passwordForConfig');

    // PriceMonitorSdkHelper::registerConfigService($emailForConfig,$passwordForConfig);

    // return $runnerService->enqueueProductExportJob($priceMonitorId);
    
  //  return  $runnerService->runAsync();

} catch(\Exception $ex) {
    
    $response = [
        'Code' => $ex->getCode(),
        'Message' => $ex->getMessage()
     ];

    return $response;
}

?>
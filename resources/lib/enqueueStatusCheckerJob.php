<?php

require_once __DIR__ . '/RunnerService.php';
require_once __DIR__ . '/PriceMonitorSdkHelper.php';

try {

    $queueModel = SdkRestApi::getParam('queueModel');

    $exportTaskId = SdkRestApi::getParam('exportTaskId');
    
    $contractId = SdkRestApi::getParam('contractId');

    $runnerService = new RunnerService($queueModel);

    return $runnerService->enqueueStatusCheckerJob($exportTaskId,$contractId);
    
  //  return  $runnerService->runAsync();

} catch(\Exception $ex) {
    
    $response = [
        'Code' => $ex->getCode(),
        'Message' => $ex->getMessage()
     ];

    return $response;
}

?>
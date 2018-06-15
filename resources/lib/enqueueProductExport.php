<?php

require_once __DIR__ . '/RunnerService.php';

try {

    $priceMonitorId = SdkRestApi::getParam('priceMonitorId');

    $queueModel = SdkRestApi::getParam('queueModel');

    $runnerService = new RunnerService($queueModel);

   return $runnerService->enqueueProductExportJob($priceMonitorId);
    
  //  return  $runnerService->runAsync();

} catch(\Exception $ex) {
    
    $response = [
        'Code' => $ex->getCode(),
        'Message' => $ex->getMessage()
     ];

    return $response;
}

?>
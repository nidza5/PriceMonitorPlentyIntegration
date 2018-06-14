<?php

require_once __DIR__ . '/RunnerService.php';

try {

    $priceMonitorId = SdkRestApi::getParam('priceMonitorId');

    $runnerService = new RunnerService();
    
    $runnerService->enqueueProductExportJob($priceMonitorId);
    
    $runnerService->runAsync();

} catch(\Exception $ex) {
    
    $response = [
        'Code' => $ex->getCode(),
        'Message' => $ex->getMessage()
     ];

    return $response;
}

?>